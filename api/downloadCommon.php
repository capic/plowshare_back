<?php
/**
 * get the download
 * @param $id
 * @return mixed
 * @throws Exception
 * @throws PDOException
 */
function getDownloadObject($id)
{
    $sql_query = "select * from download where id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql_query);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $pdoDownload = $stmt->fetchObject();
        $dbCon = null;

        $download = new Download();
        $download->fromPdoDownload($pdoDownload);

        return $download;
    } catch (PDOException $e) {
        throw $e;
    }
}

function addDownloadObject($paramName, $paramLink, $paramSize, $paramStatus, $paramPidPlowdown, $paramPidCurl, $paramProgress, $paramAverageSpeed, $paramTimeLeft)
{
    $id = null;
    $download = findByLink($paramLink);
    if (!$download) {
        $sql = "INSERT INTO download (name, link, size, status, pid_plowdown, pid_curl, progress, average_speed, time_left) VALUES (:name, :link, :size, :status, :pidPlowdown, :pidCurl, :progress, :averageSpeed, :timeLeft)";
        try {
            $dbCon = getConnection();
            $stmt = $dbCon->prepare($sql);
            $stmt->bindParam("name", $paramName);
            $stmt->bindParam("link", $paramLink);
            $stmt->bindParam("size", $paramSize);
            $stmt->bindParam("status", $paramStatus);
            $stmt->bindParam("pidPlowdown", $paramPidPlowdown);
            $stmt->bindParam("pidCurl", $paramPidCurl);
            $stmt->bindParam("progress", $paramProgress);
            $stmt->bindParam("averageSpeed", $paramAverageSpeed);
            $stmt->bindParam("timeLeft", $paramTimeLeft);
            $stmt->execute();

            $id = $dbCon->lastInsertId();

            $dbCon = null;

        } catch (PDOException $e) {
            throw $e;
        }
    } else {
        $id = $download->getId();
    }

    return $id;
}

/**
 * update the download
 * @param $id
 * @param $paramName
 * @param $paramLink
 * @param $paramStatus
 * @param $paramPid
 * @return mixed
 * @throws Exception
 * @throws PDOException
 */
function updateDownloadObject($id, $paramName, $paramLink, $paramSize, $paramStatus, $paramProgress, $paramAverageSpeed, $paramTimeLeft, $paramPidPython)
{
    $sql = "UPDATE download SET name=:name, link=:link, size=:size, status=:status, progress=:progress, average_speed=:averageSpeed, time_left=:timeLeft, pid_python=:pidPython WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("name", $paramName);
        $stmt->bindParam("link", $paramLink);
        $stmt->bindParam("size", $paramSize);
        $stmt->bindParam("status", $paramStatus);
        $stmt->bindParam("progress", $paramProgress);
        $stmt->bindParam("averageSpeed", $paramAverageSpeed);
        $stmt->bindParam("timeLeft", $paramTimeLeft);
        $stmt->bindParam("pidPython", $paramPidPython);
        $stmt->bindParam("id", $id);

        $status = $stmt->execute();

        $dbCon = null;

        return getDownloadObject($id);
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * check the status of the download link
 * @param $download
 * @return mixed
 */
function checkDownloadStatus($download)
{
    if ($download->getStatus() == DOWNLOAD_STATUS_STARTING || $download->getStatus() == DOWNLOAD_STATUS_IN_PROGRESS) {
        $download->readInformationsFromLog();
        $status = $download->getStatus();

        if ($status == DOWNLOAD_STATUS_IN_PROGRESS) {
            if ($download->getProgress() == 100) {
                $status = DOWNLOAD_STATUS_FINISHED;
            }/* else {
                $status = DOWNLOAD_STATUS_WAITING;
            }*/
        }

        $download = updateDownloadObject($download->getId(), $download->getName(), $download->getLink(), $download->getSize(), $status, $download->getProgress(), $download->getAverageSpeed(), $download->getTimeLeft(), $download->getPidPython());
    }

    return $download;
}

function treatDirectories()
{
    $cdir = scandir(DIRECTORY_TELECHARGEMENT_TEXT);

    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            importLinksFromFile(DIRECTORY_TELECHARGEMENT_TEXT . $value);
        }
    }
}

function findByLink($link)
{
    $sql = "SELECT * FROM download WHERE UPPER(link) = :link";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("link", $link);
        $stmt->execute();
        $pdoDownload = $stmt->fetchObject();
        $dbCon = null;

        if ($pdoDownload) {
            $download = new Download();
            $download->fromPdoDownload($pdoDownload);
        } else {
            $download = false;
        }
        return $download;
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

/**
 * import the link contained in a file
 * @param $filePath
 */
function importLinksFromFile($filePath)
{
    $lines = file($filePath);

    foreach ($lines as $line_num => $line) {
        $pos = strpos($line, "#");
        $pos1 = strpos($line, "http://");

        if (is_int($pos) == false && is_int($pos1) == true) {
            $link = checkDownloadAvailabilityObject($line);
            $status = $link->getStatus();

            if ($status == DOWNLOAD_STATUS_ON_LINE) {
                $downloadId = addDownloadObject($link->getName(), $link->getLink(), $link->getSize(), $link->getStatus(), 0, 0, 0, 0, 0);
                $download = getDownloadObject($downloadId);
                $download->readInformationsFromLog();

                $status = DOWNLOAD_STATUS_WAITING;

                if ($download->getProcessPlowdown()) {
                    $status = DOWNLOAD_STATUS_IN_PROGRESS;
                }
            }

            updateDownloadObject($download->getId(), $download->getName(), $download->getLink(), $download->getSize(), $status, $download->getProcessPlowdown()->getPid(), $download->getProcessCurl()->getPid(), $download->getProgress(), $download->getAverageSpeed(), $download->getTimeLeft());
        }
    }
}

?>