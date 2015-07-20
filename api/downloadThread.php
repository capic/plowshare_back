<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 01/05/15
 * Time: 16:19
 */
class downloadThread extends Thread {
    const COMMAND = "/usr/bin/python /var/www/download_basic.py start ";

    private $id;

    public function  __construct($id) {
        $this->id = $id;
    }

    public function run() {
        $command = COMMAND . $this->id;

        popen($command,"r");
    }
}