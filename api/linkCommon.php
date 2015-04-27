<?php
function checkLinkAvailability($linkToAdd)
{
    $link = new Link();
    $link->setLink($linkToAdd);

    $return = checkAvailability($linkToAdd);
    $fileInfo = treatReturnCheckAvailabilty($return);

    if ($fileInfo != null) {
        $link->setName($fileInfo->name);
        $link->setSize($fileInfo->size);
        $link->setStatus(LINK_STATUS_ON_LINE);
    } else {
        $link->setName($linkToAdd);
        $link->setSize(0);
        $link->setStatus(LINK_STATUS_DELETED);
    }

    return $link;
}

function getLinksObject()
{
    $sql_query = "select * from link order by id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->query($sql_query);
        $pdoLinksList = $stmt->fetchAll(PDO::FETCH_OBJ);

        $linksList = array();
        foreach ($pdoLinksList as $pdoLink) {
            $link = new Link();
            $link->fromPdoLink($pdoLink);

            array_push($linksList, $link);
        }

        $dbCon = null;
        return $linksList;
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

function getLinkObject($id)
{
    $sql_query = "select * from link where id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql_query);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $pdoLink = $stmt->fetchObject();
        $dbCon = null;

        $link = new Link();
        if ($pdoLink) {
            $link->fromPdoLink($pdoLink);
        }

        return $link;
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}

function updateLinkObject($id, $name, $link, $size, $status)
{
    $sql = "UPDATE link SET name=:name, link=:link, size=:size, size=:size, status=:status WHERE id=:id";

    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("name", $paramName);
        $stmt->bindParam("link", $paramLink);
        $stmt->bindParam("size", $paramSize);
        $stmt->bindParam("status", $paramStatus);
        $stmt->bindParam("id", $id);

        $status = $stmt->execute();

        $dbCon = null;

        return $status;
    } catch (PDOException $e) {
        throw e;
    }
}

function deleteLinkObject($id)
{
    $sql = "DELETE FROM link WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("id", $id);
        $status = $stmt->execute();
        $dbCon = null;
        return $status;
    } catch (PDOException $e) {
        throw $e;
    }
}

function refreshLinkObject($link)
{
    $link = checkLinkAvailability($link->getLink());

    $status = updateLinkObject($link->getId(), $link->getName(), $link->getLink(), $link->getSize(), $link->getStatus());

    return $status;
}

function addDownloadFromLinkObject($idLink)
{
    $link = getLinkObject($idLink);
    $paramName = $link->name; // Getting parameter with names
    $paramLink = $link->link; // Getting parameter with names
    $paramSize = $link->size;
    $paramStatus = DOWNLOAD_STATUS_WAITING;

    $download = null;

    try {
        $idDownload = addDownloadObject($paramName, $paramLink, $paramSize, $paramStatus, 0, 0, 0, 0, 0);

        if ($idDownload) {
            $status = deleteLinkObject($idLink);

            if ($status) {
                $download = getDownloadObject($idDownload);
            } else {
                throw new Exception();
            }
        } else {
            throw new Exception();
        }
    } catch (Exception $e) {
        throw $e;
    }

    return $download;
}

?>