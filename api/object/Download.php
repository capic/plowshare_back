<?php
include_once 'Link.php';

class Download extends Link
{
    public $progressFile;
    public $progressPart;
    public $averageSpeed;
    public $timeSpent;
    public $timeLeft;
    public $pidPython;
    public $priority;
    public $sizePart;
    public $sizeFileDownloaded;
    public $sizePartDownloaded;

    function __construct()
    {
        $this->id = -1;
        $this->sizeFile = -1;
        $this->progressFile = -1;
        $this->progressPart = -1;
        $this->averageSpeed = -1;
        $this->timeSpent = -1;
        $this->timeLeft = -1;
        $this->pidPython = -1;
        $this->lifecycle_insert_date =  null;
        $this->lifecycle_update_date =  null;
        $this->hasInfosPlowdown = false;
        $this->priority = 0;
        $this->sizePart = -1;
        $this->sizeFileDownloaded = -1;
        $this->sizePartDownloaded = -1;
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
     * @param int $origin_size
     */
    public function setOriginSize($origin_size)
    {
        $this->origin_size = $origin_size;
    }

    /**
     * @return int
     */
    public function getOriginSize()
    {
        return $this->origin_size;
    }
    
     /**
     * @param mixed $progress
     */
    public function setProgressFile($progress)
    {
        $this->progressFile = $progress;
    }

    /**
     * @return mixed
     */
    public function getProgressFile()
    {
        return $this->progressFile;
    }

    /**
     * @param mixed $progress
     */
    public function setProgressPart($progress)
    {
        $this->progressPart = $progress;
    }

    /**
     * @return mixed
     */
    public function getProgressPart()
    {
        return $this->progressPart;
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
     * @param int $timeSpent
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;
    }

    /**
     * @return int
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
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
     * @param null $lifecycle_insert_date
     */
    public function setLifecycleInsertDate($lifecycle_insert_date)
    {
        $this->lifecycle_insert_date = $lifecycle_insert_date;
    }

    /**
     * @return null
     */
    public function getLifecycleInsertDate()
    {
        return $this->lifecycle_insert_date;
    }

    /**
     * @param null $lifecycle_update_date
     */
    public function setLifecycleUpdateDate($lifecycle_update_date)
    {
        $this->lifecycle_update_date = $lifecycle_update_date;
    }

    /**
     * @return null
     */
    public function getLifecycleUpdateDate()
    {
        return $this->lifecycle_update_date;
    }

    /**
     * @param string $infosPlowdown
     */
    public function setHasInfosPlowdown($hasInfosPlowdown)
    {
        $this->hasInfosPlowdown = $hasInfosPlowdown;
    }

    /**
     * @return string
     */
    public function getHasInfosPlowdown()
    {
        return $this->hasInfosPlowdown;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $sizeFileDownloaded
     */
    public function setSizeFileDownloaded($sizeFileDownloaded)
    {
        $this->sizeFileDownloaded = $sizeFileDownloaded;
    }

    /**
     * @return int
     */
    public function getSizeFileDownloaded()
    {
        return $this->sizeFileDownloaded;
    }

    /**
     * @param int $sizePart
     */
    public function setSizePart($sizePart)
    {
        $this->sizePart = $sizePart;
    }

    /**
     * @return int
     */
    public function getSizePart()
    {
        return $this->sizePart;
    }

    /**
     * @param int $sizePartDownloaded
     */
    public function setSizePartDownloaded($sizePartDownloaded)
    {
        $this->sizePartDownloaded = $sizePartDownloaded;
    }

    /**
     * @return int
     */
    public function getSizePartDownloaded()
    {
        return $this->sizePartDownloaded;
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
        $this->sizeFile = $pdoDownload->size_file;
        $this->sizePart = $pdoDownload->size_part;
        $this->sizeFileDownloaded = $pdoDownload->size_file_downloaded;
        $this->sizePartDownloaded = $pdoDownload->size_part_downloaded;
        $this->status = $pdoDownload->status;
        if ($download->sizeFile > 0) {
            $this->progressFile = ($download->size_file_downloaded * 100) / $download->sizeFile;
        }
        $this->progressPart = $pdoDownload->progress_part;
        $this->averageSpeed = $pdoDownload->average_speed;
        $this->timeSpent = $pdoDownload->time_spent;
        $this->timeLeft = $pdoDownload->time_left;
        $this->pidPython = $pdoDownload->pid_python;
        $this->lifecycle_insert_date = $pdoDownload->lifecycle_insert_date;
        $this->lifecycle_update_date = $pdoDownload->lifecycle_update_date;
        $this->hasInfosPlowdown = $pdoDownload->infos_plowdown ? true : false;
        $this->priority = $pdoDownload->priority;
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

                $this->progressPart = intval($progress);
                $this->averageSpeed = intval($averageSpeed);
                $this->timeLeft = intval($timeLeftHour) * 3600 + intval($timeLeftMin) * 60 + intval($timeLeftSec);
                $this->sizeFile = $size;
            }
        }
    }
} 
