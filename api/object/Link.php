<?php
class Link {
    public $id;
    public $name;
    public $package;
    public $link;
    public $sizeFile;
    public $status;

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
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }/**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }/**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }/**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }/**
     * @param mixed $size
     */
    public function setSizeFile($size)
    {
        $this->sizeFile = $size;
    }/**
     * @return mixed
     */
    public function getSizeFile()
    {
        return $this->sizeFile;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function fromPdoLink($pdoLink) {
        $this->id = $pdoLink->id;
        $this->name = $pdoLink->name;
        $this->package = $pdoLink->package;
        $this->link = $pdoLink->link;
        $this->sizeFile = $pdoLink->size;
        $this->status = $pdoLink->status;
    }
}
?>