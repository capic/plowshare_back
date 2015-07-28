<?php
    include_once 'constant.php';

    function getConnection() {
        $conn = null;
        try {
            $conn = new PDO(DB_DNS, DB_USERNAME, DB_PASSWORD);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }

        return $conn;
    }

    function checkAvailability($link) {
        $command = '/usr/bin/plowprobe --printf \'# {"name":"%f","sizeFile":"%s"}\' ' . $link;
        $command_return = shell_exec($command);

        return $command_return;
    }

    function treatReturnCheckAvailabilty($return) {
        $pos = strpos($return, "# ");

        if (is_int($pos) == true) {
            $return = str_replace("# ", "", $return);

            $fileInfo = json_decode($return);
        } else {
            $fileInfo = null;
        }

        return $fileInfo;
    }