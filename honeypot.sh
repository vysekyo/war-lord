#!/bin/bash

trap 'echo "^C"' INT
trap 'echo "^Z"' TSTP

LOG="/var/tmp/.syslog"

SECRET_HASH="bd215ed37d2a8de55327d96990a5b38d41cfc8440ba851e139e0855617957fb5"

echo "sh: 0: getcwd() failed: No such file or directory"
sleep 0.3
echo

while true; do
    printf "%s@%s:/# " "$(whoami)" "$(hostname)"
    read -r cmd

    echo "$(date) :: $cmd" >> "$LOG"

    input_hash=$(echo -n "$cmd" | sha256sum | cut -d' ' -f1)

    if [ "$input_hash" = "$SECRET_HASH" ]; then
        echo "sh: segmentation fault"
        sleep 1
        exec /bin/bash --noprofile --norc
    fi

    case "$cmd" in
        "")
            continue
            ;;

        ls)
            echo "ls: reading directory '.': Input/output error"
            ;;

        pwd)
            echo "pwd: error retrieving current directory"
            ;;

        cd*)
            target=$(echo "$cmd" | cut -d' ' -f2)
            echo "sh: cd: can't cd to $target"
            ;;

        whoami)
            echo "$(whoami)"
            ;;

        cat*)
            file=$(echo "$cmd" | cut -d' ' -f2)
            echo "cat: $file: No such file or directory"
            ;;

        dmesg)
            echo "dmesg: read kernel buffer failed: Operation not permitted"
            ;;

        *)
            case $((RANDOM % 2)) in
                0) echo "cannot execute binary file" ;;
                1) echo "cannot execute binary file" ;;
            esac
            ;;
    esac

    if [ $((RANDOM % 15)) -eq 0 ]; then
        echo "bash: warning: shell level ($((RANDOM%2000))) too high, resetting to 1"
    fi
done