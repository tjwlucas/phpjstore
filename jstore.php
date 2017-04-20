<?php

class jstore
{
    private $json = '';
    public $datapath = '';
    public function __construct($saveto = '')
    {
        if ($saveto == '') {
            die('Must set storage destination!');
        }
        // If storage destination doesn't exist, create it
        if (!file_exists($saveto.'/schemas')) {
            mkdir($saveto.'/schemas', 0777, true);
        }
        if (!file_exists($saveto.'/data')) {
            mkdir($saveto.'/data', 0777, true);
        }
        $this->datapath = $saveto;
    }

    // Obtain a list of keys (JSON files) stored in datapath
    public function getSets()
    {
        $setlist = [];
        foreach (glob($this->datapath."/data/*.json") as $file) {
            preg_match("`".$this->datapath."/data/(.*).json`", $file, $key, PREG_OFFSET_CAPTURE);
            $setlist[] = $key[1][0];
        }
        return $setlist;
    }

    public function getSchemas()
    {
        $setlist = [];
        foreach (glob($this->datapath."/schemas/*.json") as $file) {
            preg_match("`".$this->datapath."/schemas/(.*).json`", $file, $key, PREG_OFFSET_CAPTURE);
            $setlist[] = $key[1][0];
        }
        return $setlist;
    }

    public function get($key)
    {
        $item = new jstoreObject($this);
        if(file_exists($this->datapath.'/data/'.$key.'.json')){
                $item->json = file_get_contents($this->datapath.'/data/'.$key.'.json');
        }
        else{
            $item->json = '{}';
        }
        $item->key = $key;
        return $item;
    }

    public function admin($key)
    {
        $default = $this->get($key)->toArray();
        foreach ($default as $arraykey => $entry) {
            $default[$arraykey] = ', "default": '.json_encode($entry,JSON_PRETTY_PRINT);
        }
        include('admintemplate.php');
    }

    public function getGlobals(){
        return $this->get('../global');
    }

    public function setGlobal($array)
    {
        $obj = $this->getGlobals();
        $obj->set($array);
        $obj->save();
    }

    public function getGlobal($key){
        $array = $this->getGlobals()->toArray();
        return $array[$key];
    }

    public function deleteGlobal($key){
        $this->getGlobals()->delete($key)->save();
    }

    public function registerEndpoint(){
        if(isset($_POST['key']) AND isset($_POST['json'])){
            $data = new jstoreObject($this);
            $data->key = $_POST['key'];
            $data->json = $_POST['json'];
            return $data->save();
        }
    }
}

class jstoreObject {
    public $json = '';
    public $key = '';
    public function __construct($store){
        $this->store = $store;
    }
    public function save(){
        return file_put_contents($this->store->datapath."/data/$this->key.json", $this->json);
    }
    
    public function toJSON()
    {
        return $this->json;
    }

    public function toArray()
    {
        $array = json_decode($this->json, true);
        return $array;
    }

    public function set($newvalues){
        $array = $this->toArray();
        foreach($newvalues as $key => $value){
            $array[$key] = $value;
        }
        $this->json = json_encode($array, JSON_PRETTY_PRINT);
        return $this;
    }

    public function delete($key){
        $array = $this->toArray();
        unset($array[$key]);
        $this->json = json_encode($array, JSON_PRETTY_PRINT);
        return $this;
    }
}