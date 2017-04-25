<?php

namespace jstore;

class jstoreObject
{
    private $json;
    private $store;
    private $jstoreObjectKey;
    public function __construct($store)
    {        
        $this->store = $store;
    }

    public function __toString(){
        return $this->toJSON();
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
    
    /** Outputs the object as a JSON */
    public function toJSON()
    {   
        $allvals = Array();
        $reflection = new \ReflectionObject($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $prop){
            $propkey = $prop->getName();
            $allvals[$propkey] = $this->$propkey;
        }
        $json = json_encode($allvals, JSON_PRETTY_PRINT);
        return $json;
    }

    /** Outputs the object as a PHP Array */
    public function toArray()
    {
        $array = json_decode($this->toJSON(), true);
        return $array;
    }

    /** Sets values in the current object instance: use $thisobj->set(['key'=>'value']) */
    public function set($newvalues)
    {
        $array = $this->toArray();
        foreach ($newvalues as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /** Deletes the entry corrensponding to the given key in the object instance: $thisobj->delete('key') */
    public function delete($key)
    {
        $array = $this->toArray();
        unset($array[$key]);
        $this->json = json_encode($array, JSON_PRETTY_PRINT);
        return $this;
    }
}