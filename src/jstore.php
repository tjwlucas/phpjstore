<?php

namespace jstore;

class jstore
{
    private $json;
    public $datapath;
    public $adminpost;

    /** Recursive copy function from comment at https://secure.php.net/manual/en/function.copy.php#91010
    * Used in __construct() to populate the storage destination from the default structure
	*/
    private static function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    jstore::recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function __construct($saveto = '')
    {
        if ($saveto == '') {
            $saveto = jstore::fsdir().'/../data';
        }
        // If storage destination doesn't exist, initiate it, using defaults
        if (!file_exists($saveto)) {
            jstore::recurse_copy(jstore::fsdir().'/defaults', $saveto);
        }
        $this->datapath = $saveto;
    }

    /** Used to get the directory where this class file is located */
    public static function dir()
    {
        $JSTORE_DIR         = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__);
        $JSTORE_DOCS_ROOT   = str_replace(DIRECTORY_SEPARATOR, '/', isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : dirname(__DIR__));
        return trim(str_replace($JSTORE_DOCS_ROOT, '', $JSTORE_DIR), "/");
    }

    public static function fsdir()
    {
        return dirname(__file__);
    }

    /** Add HTML to include the javascript used for admin pages (jdorn's fantastic 'json-editor')
    * https://github.com/jdorn/json-editor
	*/
    public static function script($jquery = false)
    {
        $output = '';
        if ($jquery == true) {    // If $jquery is set to true, it will include a copy of that, too, using CDN
            $output .= '<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>';
        }
        $output .= '<script src="/'.jstore::dir().'/jsoneditor.min.js"></script>';
        return $output;
    }

    public static function bootstrap3()
    {
   /** Include a CDN copy of bootstrap3, if wanted, for rendering the admin interface */
        return '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">';
    }

    public function JsonToObj($json){
        $array = json_decode($json, $assoc = true);
        $obj = $this->ArrayToObj($array);
        return $obj;
    }
    
    public function ArrayToObj($array){
        $obj = new jstoreObject($this);
        foreach( $array as $arraykey => $arrayval){
            if( is_array($arrayval) ){
                $obj->$arraykey = $this->ArrayToObj($arrayval);
            }
            else{
                $obj->$arraykey = $arrayval;
            }
        }
        return $obj;
    }

    /** Obtain a list of keys (JSON files) stored in datapath */
    public function getSets()
    {
        $setlist = [];
        foreach (glob($this->datapath."/data/*.json") as $file) {
            preg_match("`".$this->datapath."/data/(.*).json`", $file, $key, PREG_OFFSET_CAPTURE);
            $setlist[] = $key[1][0];
        }
        return $setlist;
    }

    /** Returns a list of available schemas (JSON files) stored in [datapath]/schemas */
    public function getSchemas()
    {
        $setlist = [];
        foreach (glob($this->datapath."/schemas/*.json") as $file) {
            preg_match("`".$this->datapath."/schemas/(.*).json`", $file, $key, PREG_OFFSET_CAPTURE);
            $setlist[] = $key[1][0];
        }
        return $setlist;
    }

    /** Returns jstoreObject item with the specified key (If there are data stored for it,
    * it will be populated, but otherwise will instantiate an empty object)
	*/
    public function get($key)
    {
        //$item = new jstoreObject($this);
        if ($this->exists($key)) {
                $json = file_get_contents($this->datapath.'/data/'.$key.'.json');
                $item = $this->JsonToObj($json);
        } 
        $item->setKey($key);
        return $item;
    }

    public function exists($key){
        return file_exists($this->datapath.'/data/'.$key.'.json');
    }

    /** Returns an admin panel for editing data, as defined in a schema in [datastore]/schemas
    * Make sure you have run jstore::script() on your page first, or nothing will display
	*/
    public function admin($key)
    {
        $default = $this->get($key)->toArray();
        ob_start();
        include($this->datapath."/schemas/".$key.".json");
        $schema = ob_get_clean();
        $schema_array = json_decode($schema, true);
        foreach ($default as $arraykey => $entry) {
            $schema_array['properties'][$arraykey]['default'] = $entry;
        }
        $schema = json_encode($schema_array, JSON_PRETTY_PRINT);
        ob_start();
        include('admintemplate.php');
        $output = ob_get_clean();
        return $output;
    }

    /** Shortcut to store simple global variables i.e. use $jstore->setGlobal(['key'=>'value'])
    *	(Where $jstore is your jstore object)
	*/
    public function setGlobal($array)
    {
        $obj = $this->getGlobals();
        $obj->set($array);
        $obj->save();
    }
    
    /** Shortcut to retrieve all global variables
    * The above example would be retrieved with $jstore->getGlobals()
    * And would retrieve the full array of global variables
    * So $jstore->getGlobals()['key'] would be your 'value' string
	*/
    public function getGlobals()
    {
        return $this->get('../global');
    }

    /** Shortcut to retrieve simple global variables
    * The above example would be retrieved with $jstore->getGlobal('key')
    * And would retrieve your 'value' string
	*/
    public function getGlobal($key)
    {
        $array = $this->getGlobals()->toArray();
        return $array[$key];
    }

    /** Shortcut to delete a global variable
    * e.g. to delete the above example:  $jstore->deleteGlobal('key')
    * Nothing is returned
	*/
    public function deleteGlobal($key)
    {
        $this->getGlobals()->delete($key)->save();
    }

    /** Place registerEndpoint() at the top of the destination specified in the $adminpost property
    * Must be placed before any output is sent for the response to return correctly
    * Rememmber that anyone who can send data to this method can modify your data, so make sure only 
    * Authorised users have access to wherever you put it!
	*/
    public function registerEndpoint()
    {
        if (isset($_POST['key']) and isset($_POST['json'])) {
            $data = new jstoreObject($this);
            $data->key = $_POST['key'];
            $data->json = $_POST['json'];
            $data->save();
            exit('jstoresuccess');
        }
    }
}


