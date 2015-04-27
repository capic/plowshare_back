<?php
/**
 * Created by PhpStorm.
 * User: VINCENT
 * Date: 14/08/14
 * Time: 11:27
 */

class Process{
    private $pid;
    private $command;
    private $logfile;

    public function __construct($cl=false){
        if ($cl != false){
            $this->command = $cl;
            $this->logfile = "dev/null";
            $this->runCom();
        }
    }
    private function runCom(){
        //$command = 'nohup ' . $this->command . ' > ' . $this->logfile . ' 2>&1 & echo $!';
        //$retour = exec($this->command ,$op);
        $retour = popen($this->command,"r");
        $this->pid = (int)$op[0];
    }

    public function setPid($pid){
        $this->pid = $pid;
    }

    public function getPid(){
        return $this->pid;
    }

    public function setCommand($command) {
        $this->command = $command;
    }

    public function getCommand() {
        return $this->command;
    }

    public function setLogfile($logfile) {
        $this->logfile = $logfile;
    }

    public function getLogfile() {
        return $this->logfile;
    }

    public function status(){
        $command = 'ps -p '.$this->pid;
        exec($command,$op);
        if (!isset($op[1]))return false;
        else return true;
    }

    public function start(){
        if ($this->command != '')$this->runCom();
        else return true;
    }

    public function stop(){
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false)return true;
        else return false;
    }


}