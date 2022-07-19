<?php
class processor extends auth{
    public $uid;
    function uploadAttachments($d,$u){
        $this->ins_to_db("attachments",["file_link","identifier","type"],[$d[1],$u,"attachment"]);
    }
}