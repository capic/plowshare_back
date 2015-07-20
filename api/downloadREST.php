<?php
/*
 * - protéger par authentificaton oauth2
 * - apparemment on ne peut pas démarrer le téléchargement d'un lien si déjà un lien en cours de téléchargement
 * - mettre au statut terminé quand le téléchargement est fini
 * - suppression d'un téléchargement en cours doit killer le processus
 * - limiter un telechargement par hebergeur
 * - quand des nouveaux fichiers sont ajoutés et que les téléchargement sont en cours, ces nouveaux telechargements doivent aussi être pris en compte
 * - trouver le moyen d'avoir un telechargement continue tant qu'il y a des telechargment en attente
 * - la size d'un lien change si il y a reprise, le pourcentage aussi
 * - problème lorsque le téléchargement est à 100% le download n'a pas le bon progress ni le bon statut
 * - décompresser à la fin du téléchargement
 */
include_once 'constant.php';
include_once 'object/Link.php';
include_once 'object/Download.php';
include_once 'object/Message.php';
include_once 'object/InfosPlowdown.php';
include_once 'downloadCommon.php';

/**
 * get the list of status available for the downloads
 */
function getDownloadStatus() {
    $sql_query = "select id, name from download_status order by ord";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $downloadStatus  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;

        echo json_encode($downloadStatus);
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * get the list of downloads
 */
function getDownloads() {
    $sql_query = "select * from download order by id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $pdoDownloadsList  = $stmt->fetchAll(PDO::FETCH_OBJ);

        $downloadsReturn = array();
        foreach($pdoDownloadsList as $pdoDownload) {
            $download = new Download();
            $download->fromPdoDownload($pdoDownload);

            array_push($downloadsReturn, checkDownloadStatus($download));
        }

        $dbCon = null;
        echo json_encode($downloadsReturn);
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * get the download
 * @param $id
 */
function getDownload($id) {
    try {
        $download = getDownloadObject($id);

        echo json_encode($download);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getInfosPlowdown($id) {
    $sql_query = "select infos_plowdown from download where id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql_query);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $pdoDownload = $stmt->fetchObject();
        $dbCon = null;

        $infosPlowdown = new InfosPlowdown('');

        if ($pdoDownload) {
            $infosPlowdown->setInfos($pdoDownload->infos_plowdown);
        }

        echo json_encode($infosPlowdown);
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * get the downloads which contains name
 * @param $name
 */
function findDownloadByName($name) {
    $sql = "SELECT * FROM download WHERE UPPER(name) LIKE :name ORDER BY name";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $query = "%".$name."%";
        $stmt->bindParam("name", $query);
        $stmt->execute();
        $pdoDownloadsList = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;

        $downloadsList = array();
        foreach($pdoDownloadsList as $pdoDownload) {
            $download = new Download();
            $download->fromPdoDownload($pdoDownload);

            array_push($downloadsList, $download);
        }

        echo json_encode($downloadsList);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * add a download
 */
function addDownload() {

    global $app;
    $req = $app->request(); // Getting parameter with names
    $downloadPost = json_decode($req->getBody());
    $paramName = $downloadPost->name; // Getting parameter with names
    $paramLink = $downloadPost->link; // Getting parameter with names
    $paramSize = $downloadPost->size;
    $paramStatus = DOWNLOAD_STATUS_WAITING;

    try {
        $download = null;

        $id = addDownloadObject($paramName, $paramLink, $paramSize, $paramStatus, 0, 0, 0, 0, 0);

        // set the object if no error
        if ($id != null) {
            $download = new Download();
            $download->setId($id);
            $download->setName($paramName);
            $download->setLink($paramLink);
            $download->setSize($paramSize);
            $download->setStatus($paramStatus);
            $download->setProcessCurl(null);
            $download->setProcessPlowdown(null);
            $download->setProgress(0);
            $download->setAverageSpeed(0);
            $download->setTimeLeft(0);
        } else {
            throw new PDOException;
        }

        echo json_encode($download);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * update a download
 * @param $id
 */
function updateDownload($id) {
    global $app;
    $req = $app->request();
    $paramName = $req->params('name');
    $paramLink = $req->params('link');
    $paramStatus = $req->params('status');
    try {
        $download = getDownloadObject($id);

        $status = updateDownloadObject($id, $paramName, $paramLink, $download->getSize(), $paramStatus, $download->getProcessPlowdown()->getPid(), $download->getProcessCurl()->getPid(), $download->getProgress(), $download->getAverageSpeed(), $download->getTimeLeft());
        echo '{"status" : ' . json_encode($status) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * delete a download
 * @param $id
 */
function deleteDownload($id) {
    $sql = "DELETE FROM download WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("id", $id);
        $status = $stmt->execute();
        $dbCon = null;
        echo '{"status" : ' . json_encode($status) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * delete a list of downloads
 */
function deleteDownloads() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $listId = json_decode($req->getBody())->ListId;

    $sql = "DELETE FROM download WHERE id IN (" . implode(", ", $listId) . ")";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $status = $stmt->execute();
        $dbCon = null;
        echo '{"status" : ' . json_encode($status) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function deleteInfosPlowdown($id) {
    $sql = "UPDATE download SET infos_plowdown WHERE id=:id";

    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("id", $id);
        $status = $stmt->execute();
        $dbCon = null;
        echo '{"status" : ' . json_encode($status) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * start the downloads
 */
function startDownloads() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $object = json_decode($req->getBody());
    $id = $object->id;

    $message = new Message();
    $message->text = "Téléchargement démarré";

    $app->zmqSocket->send(json_encode($message));

    $tabDownloads = array();

    if ($id == -1) {
    //while (1) {
        $sql_query = "select * from download where status=:status having min(id)";
        try {
            $dbCon = getConnection();
            $stmt = $dbCon->prepare($sql_query);
            $status = DOWNLOAD_STATUS_WAITING;
            $stmt->bindParam("status", $status);
            $stmt->execute();
            $pdoDownloadsList  = $stmt->fetchAll(PDO::FETCH_OBJ);
            $dbCon = null;

            foreach($pdoDownloadsList as $pdoDownload) {
                $download = new Download();
                $download->fromPdoDownload($pdoDownload);

                //$command = "/usr/local/bin/plowdown -r 10 -x -m --9kweu=I1QOR00P692PN4Q4669U --temp-rename --temp-directory " . DIRECTORY_TELECHARGEMENT_DESTINATION_TEMP . " -o " . DIRECTORY_TELECHARGEMENT_DESTINATION . " " . $download->link;
                $command = "python2.7 download_basic.py " . $download->id;

                $logfile = DIRECTORY_TELECHARGEMENT_LOG . $download->name . ".log";

                $process = new Process();
                $process->setCommand($command);
                $process->setLogfile($logfile);
                $process->start();

                $download = updateDownloadObject($download->getId(), $download->getName(), $download->getLink(), $download->getSize(), DOWNLOAD_STATUS_IN_PROGRESS , $download->getProcessPlowdown()->getPid() , $download->getProcessCurl()->getPid(), $download->getProgress(), $download->getAverageSpeed(), $download->getTimeLeft());

                array_push($tabDownloads, $download);
            }
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }

       // sleep(3600);
  //  }
    } else {
        $download = getDownloadObject($id);
        $download->start();

        $downloadSocket = $app->zmpContext->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
        $downloadSocket->connect("tcp://127.0.0.1:9000");

        $download = getDownloadObject($id);
        array_push($tabDownloads, $download);
    }

    echo json_encode($tabDownloads);
}

/**
 * stop the downloads
 */
function stopDownloads() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $object = json_decode($req->getBody());
    $id = $object->id;

    $tabDownloads = array();

    if ($id == -1) {
        $sql_query = "select * from download where status=:status";

        try {
            $dbCon = getConnection();
            $stmt = $dbCon->prepare($sql_query);
            $status = DOWNLOAD_STATUS_IN_PROGRESS;
            $stmt->bindParam("status", $status);
            $stmt->execute();
            $pdoDownloadsList  = $stmt->fetchAll(PDO::FETCH_OBJ);
            $dbCon = null;

            foreach($pdoDownloadsList as $pdoDownload) {
                $download = new Download();
                $download->fromPdoDownload($pdoDownload);

                $process = new Process();
                $process->setPid($download->getPid());

                // kill the plowdown process
                $process->stop();
                // kill the curl process
                $process->getProcessByDownloadName($download->getName());

            }
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    } else {
        $download = getDownloadObject($id);
        $download->stop();

       // $download = updateDownloadObject($download->getId(), $download->getName(), $download->getLink(), $download->getSize(), DOWNLOAD_STATUS_STARTING, $download->getProcessPlowdown()->getPid() , $download->getProcessCurl()->getPid(), $download->getProgress(), $download->getAverageSpeed(), $download->getTimeLeft());
        $download = getDownloadObject($id);
        array_push($tabDownloads, $download);
    }

    echo json_encode($tabDownloads);
}

/**
 * check the availability of a link
 */
function checkDownloadAvailability($id) {

    $download = getDownloadObject($id);

    if ($download->getStatus() != DOWNLOAD_STATUS_IN_PROGRESS && $download->getStatus() != DOWNLOAD_STATUS_FINISHED) {

        $return = checkAvailability($download->getLink());

        $pidPlowdown = 0;
        $pidCurl = 0;
        $pidPython = 0;

        $fileInfo = treatReturnCheckAvailabilty($return);

        if ($fileInfo != null) {
            $download->setName($fileInfo->name);
            $download->setSize($fileInfo->size);
            $download->setStatus(DOWNLOAD_STATUS_WAITING);
            $pidPython = $download->getPidPython();
        }
        else {
            $download->setName($download->getLink());
            $download->setSize(0);
            $download->setStatus(DOWNLOAD_STATUS_DELETED);
        }


        updateDownloadObject($download->getId(), $download->getName(), $download->getLink(), $download->getSize(),$download->getStatus(), $download->getProgress(), $download->getAverageSpeed(), $download->getTimeLeft(), $pidPython);
    }


    echo json_encode($download);
}

function refreshDownloads() {
    getDownloads();
}

function refreshDownload($id) {
    $download = checkDownloadAvailability($id);

    return $download;
}

function importDownloads() {
    treatDirectories();

    getDownloads();
}

function test() {
//    $command = "screen -list | grep 1fichier";
//    $command_return = shell_exec($command);
//
//    $pid = explode(".1fichier", $command_return);
//    $command = "screen -r " . trim($pid[0]);
//
//    $command_return = shell_exec($command);
//
//    echo $command_return;

    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://127.0.0.1:6666");

    $message = new Message();
    $message->text = "Téléchargement terminé";

    $socket->send(json_encode($message));
}
