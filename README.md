# phpjstore

Phpjstore provides a class to save and access schemaless data in a flatfile JSON backend.

## Requirements
Phpjstore iself doesn't require any dependencies.

The admin interface uses the Jeremy Dorn's [json-editor][je] (included in the package), [jquery][jq], and [Bootstrap 3][bs]. Both of these can be included directly in your page (using CDN

[je]: https://github.com/jdorn/json-editor/
[jq]: https://github.com/jquery/jquery
[bs]: https://github.com/twbs/bootstrap

## Installation/Usage
Using composer:

	composer require tlucas/phpjstore

In your project file (e.g. `project.php`) you wish to use it in make sure you have

	require_once('vendor/autoload.php');
	use jstore\jstore;

Then you can instantiate a data store object with

	$store = new jstore('mydata');

Which will store all the data in the `mydata` directory (relative to your current script).