<?php
include_once 'constant.php';
include_once 'object/Link.php';
include_once 'linkCommon.php';

/**
 * get the list of status
 */
function getLinkStatus() {
    $sql_query = "select id, name from link_status order by ord";
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
 * refresh a link
 * |-> get the link by id
 * |-> use the refresh link common function
 * |-> get the link updated
 *
 * @param $id
 *  the id of the link
 * @return Link
 *  the update link
 */
function refreshLink($id) {
    $link = getLinkObject($id);

    $status = refreshLinkObject($link);

    $link = getLinkObject($id);

    if ($status) {
        echo json_encode($link);
    } else {
        echo '{"error":{"text":"refresh link error"}}';
    }
}

/**
 * refresh all links
 * |-> get all links
 * |-> refresh all links
 * |-> get all the updated links
 */
function refreshLinks() {
    $linksList = getLinksObject();

    foreach($linksList as $link) {
        $status = refreshLinkObject($link);
    }

    $linksList = getLinksObject();

    echo json_encode($linksList);
}

/**
 * get all links
 */
function getLinks() {
    try {
        $linksList = getLinksObject();

        echo json_encode($linksList);
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * get the link
 * @param $id
 */
function getLink($id) {
    try {
        $link = getLinkObject($id);

        echo json_encode($link);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * get the downloads which contains name
 * @param $name
 */
function findLinkByName($name) {
    $sql = "SELECT * FROM link WHERE UPPER(name) LIKE :name ORDER BY name";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $query = "%".$name."%";
        $stmt->bindParam("name", $query);
        $stmt->execute();
        $pdoLinksList = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;

        echo '{"links": ' . json_encode($pdoLinksList) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * update the link
 *
 * @param $id
 */
function updateLink($id) {
    global $app;
    $req = $app->request();
    $paramName = $req->params('name');
    $paramLink = $req->params('link');
    $paramSize = $req->params('size');
    $paramStatus = $req->params('status');

    try {
        $status = updateLinkObject($id, $paramName, $paramLink, $paramSize, $paramStatus);

        echo '{"status" : ' . json_encode($status) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * delete the link
 *
 * @param $id
 */
function deleteLink($id) {
    $sql = "DELETE FROM link WHERE id=:id";
    try {
        $status = deleteLinkObject($id);
        echo '{"status" : ' . json_encode($status) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * add a link
 */
function addLink() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $object = json_decode($req->getBody());
    $paramLink = $object->link;

    $sql = "INSERT INTO link (name, link, size, status) VALUES (:name, :link, :size, :status)";

    try {
        $link = checkLinkAvailability($paramLink);

        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $name = $link->getName();
        $size = $link->getSize();
        $status = $link->getStatus();
        $stmt->bindParam("name", $name);
        $stmt->bindParam("link", $paramLink);
        $stmt->bindParam("size", $size);
        $stmt->bindParam("status", $status);

        $stmt->execute();

        $link->id = $dbCon->lastInsertId();

        $dbCon = null;

        echo json_encode($link);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * delete a list of links
 */
function deleteLinks() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $listId = json_decode($req->getBody())->ListId;;

    $sql = "DELETE FROM link WHERE id IN (" . implode(", ", $listId) . ")";
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

/**
 * add a download from a link
 *
 * @return bool
 * @throws Exception
 */
function addDownloadFromLink() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $object = json_decode($req->getBody());
    $idLink = $object->id;

    try {
        $download = addDownloadFromLinkObject($idLink);

        if ($download != null) {
            echo json_encode($download);
        } else {
            throw new Exception();
        }
    } catch(Exception $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function addDownloadsFromLinks() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $listId = json_decode($req->getBody())->ListId;

    $downloadsList = array();
    $listLinkId = array();

    try {
        foreach($listId as $id) {
            $download = addDownloadFromLinkObject($id);

            if ($download != null) {
                $downloadsList[] = $download;
                $listLinkId[] = $id;
            }
        }

        echo '{"downloads":' . json_encode($downloadsList) . ', "linksId"&:' . json_encode($listLinkId) . '}';
    } catch(Exception $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

?>