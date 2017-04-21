<?php

namespace jstore;

class jstoreObject
{
    public $json;
    public $key;
    public function __construct($store)
    {
        $this->store = $store;
    }
    public function save()
    {
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

    public function set($newvalues)
    {
        $array = $this->toArray();
        foreach ($newvalues as $key => $value) {
            $array[$key] = $value;
        }
        $this->json = json_encode($array, JSON_PRETTY_PRINT);
        return $this;
    }

    public function delete($key)
    {
        $array = $this->toArray();
        unset($array[$key]);
        $this->json = json_encode($array, JSON_PRETTY_PRINT);
        return $this;
    }
}