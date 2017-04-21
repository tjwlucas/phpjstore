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
    // Call save() method to save modifications made to the object
    public function save()
    {
        return file_put_contents($this->store->datapath."/data/$this->key.json", $this->json);
    }
    
    // Outputs the object as a JSON
    public function toJSON()
    {
        return $this->json;
    }

    // Outputs the object as a PHP Array
    public function toArray()
    {
        $array = json_decode($this->json, true);
        return $array;
    }

    // Sets values in the current object instance: use $thisobj->set(['key'=>'value'])
    public function set($newvalues)
    {
        $array = $this->toArray();
        foreach ($newvalues as $key => $value) {
            $array[$key] = $value;
        }
        $this->json = json_encode($array, JSON_PRETTY_PRINT);
        return $this;
    }

    // Deletes the entry corrensponding to the given key in the object instance: $thisobj->delete('key')
    public function delete($key)
    {
        $array = $this->toArray();
        unset($array[$key]);
        $this->json = json_encode($array, JSON_PRETTY_PRINT);
        return $this;
    }
}