<?php

class jstore
{
    private $json = '';
    public $datapath = '';
    public function __construct($saveto = '')
    {	
        if($saveto == ''){ die('Must set storage destination!'); }
        // If storage destination doesn't exist, create it
        if(!file_exists($saveto.'/schemas')){
            mkdir($saveto.'/schemas', 0777, True);
        }
        $this->datapath = $saveto;
    }

    // Obtain a list of keys (JSON files) stored in datapath
    public function getSets() {
        $setlist = [];
        foreach( glob($this->datapath."/*.json") as $file){
        preg_match("`".$this->datapath."/(.*).json`", $file, $key, PREG_OFFSET_CAPTURE);
        $setlist[] = $key[1][0];
        }
        return $setlist;
    }

    public function get($key) {
        $this->json = file_get_contents($this->datapath.'/'.$key.'.json');
        return $this;
    }

    public function toJSON() {
        return $this->json;
    }

    public function toArray(){
        $array = json_decode($this->json, TRUE);
        return $array;
    }

    public function admin($key){
        $default = $this->get($key)->toArray();
        foreach($default as $arraykey => $entry){
            $default[$arraykey] = json_encode($entry);
        }
        include('admintemplate.php');
    }
}
