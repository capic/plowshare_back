<?php
include_once 'Link.php';

class Download extends Link
{
    public $progress;
    public $averageSpeed;
    public $timeLeft;
    public $pidPython;

    function __construct()
    {
        $this->id = -1;
        $this->progress = -1;
        $this->averageSpeed = -1;
        $this->timeLeft = -1;
        $this->pidPython = -1;
        $this->infosPlowdown = '';
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param mixed $averageSpeed
     */
    public function setAverageSpeed($averageSpeed)
    {
        $this->averageSpeed = $averageSpeed;
    }

    /**
     * @return mixed
     */
    public function getAverageSpeed()
    {
        return $this->averageSpeed;
    }

    /**
     * @param mixed $timeLeft
     */
    public function setTimeLeft($timeLeft)
    {
        $this->timeLeft = $timeLeft;
    }

    /**
     * @return mixed
     */
    public function getTimeLeft()
    {
        return $this->timeLeft;
    }

    public function setPidPython($pidPython)
    {
        $this->pidPython = $pidPython;
    }

    public function getPidPython()
    {
        return $this->pidPython;
    }

    /**
     * @param string $infosPlowdown
     */
    public function setInfosPlowdown($infosPlowdown)
    {
        $this->infosPlowdown = $infosPlowdown;
    }

    /**
     * @return string
     */
    public function getInfosPlowdown()
    {
        return $this->infosPlowdown;
    }

    public function start()
    {
        $command = "/usr/bin/python /var/www/download_basic.py start " . $this->id . " &";

        popen($command,"r");
  //      proc_open($command, )
    }

    public function stop()
    {
        $command = "/usr/bin/python /var/www/download_basic.py stop " . $this->id . " &";
        // to wait the command return to update in time the UI
        shell_exec($command);
    }

    public function fromPdoDownload($pdoDownload)
    {
        $this->id = $pdoDownload->id;
        $this->name = $pdoDownload->name;
        $this->link = $pdoDownload->link;
        $this->size = $pdoDownload->size;
        $this->status = $pdoDownload->status;
        $this->progress = $pdoDownload->progress;
        $this->averageSpeed = $pdoDownload->average_speed;
        $this->timeLeft = $pdoDownload->time_left;
        $this->pidPython = $pdoDownload->pid_python;
        $this->infosPlowdown = $pdoDownload->infos_plowdown;
    }

    function readInformationsFromLog()
    {
        $line = '';

        if (file_exists(DIRECTORY_TELECHARGEMENT_LOG . $this->name . ".log")) {
            $f = fopen(DIRECTORY_TELECHARGEMENT_LOG . $this->name . ".log", 'r');
            $cursor = -1;

            fseek($f, $cursor, SEEK_END);
            $char = fgetc($f);

            /**
             * Trim trailing newline chars of the file
             */
            while ($char === "\n" || $char === "\r") {
                fseek($f, $cursor--, SEEK_END);
                $char = fgetc($f);
            }

            /**
             * Read until the start of file or first newline char
             */
            while (strlen($line) != 78 && $char !== false /*&& ($line[0] == " " || is_int($line[0])) && (($line[77] == 'k') || $line[77] == 'M')*/) {
                $line = '';

                while ($char !== false && $char !== "\n" && $char !== "\r") {
                    /**
                     * Prepend the new char
                     */
                    $line = $char . $line;
                    fseek($f, $cursor--, SEEK_END);
                    $char = fgetc($f);
                }
                fseek($f, $cursor--, SEEK_END);
                $char = fgetc($f);
            }

            $firstChar = substr($line, 0, 1);

            if (strlen($line) > 0 && ($firstChar == " " || is_int($firstChar))) {
                $progress = substr($line, 0, 3);
                $size = substr($line, 4, 4);
                $sizeUnit = substr($line, 8, 1);
                $averageSpeed = substr($line, 32, 5);
                $averageSpeedUnit = substr($line, 37, 1);
                $timeLeft = explode(":", substr($line, 65, 8));
                $timeLeftHour = $timeLeft[0];
                $timeLeftMin = $timeLeft[1];
                $timeLeftSec = $timeLeft[2];

                switch ($sizeUnit) {
                    case 'K':
                        $size = intval($size) * 1024;
                        break;
                    case 'M':
                        $size = intval($size) * 1024 * 1024;
                        break;
                }

                switch ($averageSpeedUnit) {
                    case 'k':
                        $averageSpeed = intval($averageSpeed) * 1024;
                        break;
                    case 'M':
                        $averageSpeed = intval($averageSpeed) * 1024 * 1024;
                        break;
                }

                $this->progress = intval($progress);
                $this->averageSpeed = intval($averageSpeed);
                $this->timeLeft = intval($timeLeftHour) * 3600 + intval($timeLeftMin) * 60 + intval($timeLeftSec);
                $this->size = $size;
            }
        }
    }
} 