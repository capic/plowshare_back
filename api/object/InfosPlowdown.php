<?php

class InfosPlowdown {
    public $infos;

    function __construct() {
        $this->infos = "";
    }

    /**
     * @param string $infos
     */
    public function setInfos($infos)
    {
        $this->infos = $infos;
    }

    /**
     * @return string
     */
    public function getInfos()
    {
        return $this->infos;
    }
}