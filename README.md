# phpjstore

Phpjstore provides a class to save and access schemaless data in a flatfile JSON backend.

## Requirements
Phpjstore iself doesn't require any dependencies.

The admin interface uses the Jeremy Dorn's [json-editor][je] (included in the package), [jquery][jq], and [Bootstrap 3][bs]. Both of these can be included directly in your page (using CDN

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

At the moment, the object has only been modified in memory, so to save the changes permanently, just call:

	$data->save();