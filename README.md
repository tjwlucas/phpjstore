# phpjstore

Phpjstore provides a class to save and access schemaless data in a flatfile JSON backend.


- [phpjstore](#phpjstore)
  * [Requirements](#requirements)
  * [Installation/Setup](#installationsetup)
    + [Basic usage](#basic-usage)
    + [Globals](#globals)
  * [Admin](#admin)
    + [Scripts/styles](#scriptsstyles)
    + [Endpoint](#endpoint)
      - [Important](#important)
    + [Data schema](#data-schema)
    + [Forms](#forms)

## Requirements
Phpjstore iself doesn't require any dependencies.

The admin interface uses the Jeremy Dorn's [json-editor][je] (included in the package), [jquery][jq], and [Bootstrap 3][bs]. Both of these can be included directly in your page (using CDN)

[je]: https://github.com/jdorn/json-editor/
[jq]: https://github.com/jquery/jquery
[bs]: https://github.com/twbs/bootstrap

## Installation/Setup
Using composer:

	composer require tlucas/phpjstore

In your project file (e.g. `project.php`) you wish to use it in make sure you have

	require_once('vendor/autoload.php');
	use jstore\jstore;

Then you can instantiate a data store object with

	$store = new jstore('mydata');

Which will store all the data in the `mydata` directory (relative to your current script).

### Basic usage

To intereact with a data object, first you need to create a data object. This is done, using the `$store` object defined above, by calling the `get()` method:

	$data = $store->get('somekey');

This has now created an object `$data` containing the data stored using the `somekey` key. If that key doesn't yet exist, this will be an empty data object. 

Once you have the object in `$object` you can set some values using `set()`:

	$data->set(['firstkey' => 'firstvalue', 'secondkey' => 'secondvalue']);

This will only set the specified values (it will not affect the other values stored in the object) so if we then do:

	$data->set(['thirdkey' => 'thirdvalue']);

All three values will now be held on that object.

If we want to delete the first variable we set earlier:

	$data->delete('firstkey');

So now, our stored object looks like this (if we do `print_r($data->toArray());`):

	Array
	(
    	[secondkey] => secondvalue
    	[thirdkey] => thirdvalue
	)

	

At the moment, the object has only been modified in memory, so to save the changes permanently, just call:

	$data->save();

### Globals

Phpjstore includes some shortcuts to the above functions for storing and retrieving global variables:

To set a global variable:

	$store->setGlobal(['varname' => 'varvalue']);

(Note: this *will* immediately save the variable to the storage backend)

And to retrieve the variable we just set:

	$store->getGlobal('varname');

To list all Globals that are stored in this way:

	$store->getGlobals();

Which will return a list of the keys.

To delete one of these stored variables:

	$store->deleteGlobal('varname');

(Note: again, this *will* immediately save the variable to the storage backend)

## Admin

Phpjstore includes a data management interface based around jdorn's fantastic [json-editor][je].

Before using it, it requires a little setup.

### Scripts/styles

First, the interface uses a javascript file to be included once, along with jquery, somewhere before it is called. It also uses [Bootstrap 3][bs] for styling, so that must also be available on the page.

Phpjstore includes some helper fuctions to do these for you, if you don't already use them in your page:

	echo jstore::script();

Will include jsut the [json-editor][je] script. If you also need to include jquery, use:

	echo jstore::script($jquery=True);

To include Bootstrap:

	echo jstore::bootstrap3();

### Endpoint

In order to save the data provided in the form you will need to set an endpoint for submission. This is done by adding

	$store->registerEndpoint();

To the top of the file which is acting as your endpoint. This can be the same file, or a different file, but it must be placed before any output is sent.

If you placed this endpoing in a *separate* file (e.g. `/path/to/submit.php`), you will need to point your admin scripts to send their submissions to that file:

	$store->adminpost = '/path/to/submit.php';

#### Important
Be aware that ***anyone*** who has access to the endpoint you set here ***will be able to modify your data***, so make sure you protect it, either by putting the file in a restricted directory (Using `.htaccess` on Apache, for instance) or eclosing the method call in an authentication check, using whatever authentication system you choose):

	if( /* User is authenticated */ ){
		$store->registerEndpoint();
	}

[ex]: src/defaults/schema/example.json

### Data schema

The final step before adding the interface is setting up the schema for your data. While the *stored* data don't depend on any schema (you can just save variables straight to them, and it will create the fields as required). The [json-editor][je] interface requires a schema to build the apppropriate forms.

To set up a schema, simply go to your `mydata` directory (as defined when initialising the `jstore` instance, and inside the `schema` directory create a file called `somekey.json`. In that file, define a schema as defined [here][schema]. (When you first set up jstore, there will be an `example.json` schema already there, for reference).

[schema]: https://github.com/jdorn/json-editor/#json-schema-support

### Forms

Now we have done all that, we are ready to display the admin form!

To display the form for the `somekey` schema you created above:
	
	echo $store->admin('somekey');

(Or to use the example schema provided: `echo $store->admin('example');`)

And you're done! You should have a fully functional form to modify your stored data.

You can list all the schemas you have defined by running:
	
	$store->getSchemas();

So if you wanted an admin page which automatically gave you a form to allow you to edit all of your defined schemas, you could run:

	foreach($store->getSchemas() as $schema){
		echo $store->admin($schema);
	}
