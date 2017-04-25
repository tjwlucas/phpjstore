<?php

namespace jstore;

class jstoreRootObject extends jstoreObject
{
    private $jstoreObjectKey;

    private $store;
    public function __construct($store)
    {        
        $this->store = $store;
    }

    public function setKey($key){
        $this->jstoreObjectKey = $key;
    }

    /** Call save() method to save modifications made to the object */
    public function save()
    {
        $json = $this->toJSON();
        return file_put_contents($this->store->datapath."/data/$this->jstoreObjectKey.json", $json);
    }
}