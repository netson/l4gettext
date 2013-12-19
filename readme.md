# Gettext for Laravel 4

This package adds gettext functionality to the Laravel 4 framework which allows you to easily translate your application using tools such as PoEdit. Although Laravel 4 comes with a built-in translation engine, I prefer using tools like PoEdit, which takes away the need of maintaining arrays with text. This is my first Laravel experience/package, so if you come across any errors or have suggestions for improvements, let me know. This package works with Laravel 4.0 and 4.1.

Written by: **Rinck Sonnenberg (Netson)**


## Installation

**Installation using composer:**

* add the netson/l4gettext as a required package:

``` $ php composer.phar require "netson/l4gettext:1.2.*" ```

* update composer:

``` $ php composer.phar update ```

* add the l4gettext service provider to the laravel app/config/app.php file, in array key 'provider':

```php
<?php
// app/config/app.php

return array(
        ..
	    'providers' => array(
                ..
                'Netson\L4gettext\L4gettextServiceProvider',
        ),
        ..
);
?>
```

Next, automatically detect which locales and encodings are installed by executing the following command:

``` $ php artisan l4gettext:fetch ```

*NOTE: only available on Linux/MacOS since this uses the ```locale``` command, which is not available on windows systems. This will automatically publish the config files if they haven't been published already.* 

Now, make sure you set the proper **copyright holder**, **package name**, **package version** and **email address** in the file ``` app/config/packages/netson/l4gettext/config.php ```

**Alternatively**, if you're on windows or prefer to take the manual route, publish the package config manually and edit the config files:

``` $ php artisan config:publish netson/l4gettext ```


## How does it work?

This package simply utilizes existing functionality to allow you to use gettext from within the Laravel Blade templates. 

**NOTE:** *Symfony contains a .po file reader; this is different from this implementation as this uses the actual gettext library on your system.*

You can use the following functions in your templates:

* _()   *shorthand for gettext*
* gettext()
* dgettext()
* dcgettext()
* ngettext()
* dngettext()
* dcngettext()
* _n()  *custom shorthand for ngettext*

If you have any custom functions you would like to use, check out the config.php file for more info! You can easily use these and any custom functions in your blade templates using the following syntax: 
``` {{ _('translate this') }} ``` or ``` {{ _n("There can be only one", "There can be more than 1", 5) }} ```

The default configuration should be fine for most systems, but you should at least change the **copyright holder**, **package name**, **package version** and **email address** settings.

Aside from those, you are free to change all options according to your wishes. **Make sure you publish the config for this package before changing options**, using the following command:

``` $ php artisan config:publish netson/l4gettext ```

See the comments in the config.php file for detailed documentation on each option.

When you are ready to start translating, use the commands provided by this package to compile your templates and extract the translation strings.
See the section on Command line options for more information.


## Getting started guide

To get started with this module and gettext, you can follow these basic steps:
* Follow the installation instructions in this document (see above) to install the module

* Make sure the installation was successful by seeing if the l4gettext commands are available: ```$ php artisan list ```; a section called l4gettext should appear somewhere in the list of commands

* Then, create (and publish) the config files using the fetch command:  ```$ php artisan l4gettext:fetch ``` (this command is only supported on Linux/UNIX/Mac based systems and won't work on Windows)

* To check which locales and encodings were detected on your system, execute the following command: ```$ php artisan l4gettext:list ```

* Next, create your laravel blade templates as you normally would, but now putting all translatable text in the proper gettext function. For example:  ```{{ _("Hello World!") }}``` - see the section *How does it work?* in this document for more information

* When you're done creating your templates, you need to compile them so that the translatable strings can be extracted. To compile your templates, execute the following command: ```$ php artisan l4gettext:compile```
 
* Next, you need to extract all the translatable strings from the compiled templates and view folders into a .POT file; do so by executing the following command: ```$ php artisan l4gettext:extract```

* Use POEdit to translate the .POT file and use it to create your .mo file. The more recent versions of POEdit will automatically compile the .mo file for you when you save your translations. - *See the important note under* **Command line options** *in case you're updating an existing translation!*

* Place this compiled .mo (or multiple if you have more than 1 translation) file in the appropriate locale folder (for example: app/locale/en_GB/LC_MESSAGES/messages.mo) - *Note that gettext is extremely finicky when it comes to folder conventions!*

* Now you should be good to go! Try out your new translated site/app by using the built-in routes: yoursite.ext/set-locale/en_GB (assuming you've translated your app to British English). 

If you run into any issues, check the troubleshooting section of this document and if that doesn't help, report any issues on GitHub! :)


## Dependencies

Aside from some of the laravel 4 components, there are only logical dependencies:
* gettext library (if not installed, run sudo apt-get install gettext)
* xgettext (automatically installed when installing the gettext library)
* php-gettext

**NOTE**: *This package has only been thoroughly tested on linux (Ubuntu Server 12.04 LTS).*

## Command line options

There are 4 artisan commands for this package:

* **l4gettext:compile**: compiles all blade template files to a specific folder (not the default cache folder)
* **l4gettext:extract**: extracts all the language strings from the compiled templates and views folder
* **l4gettext:list**: lists the locales/encodings supported by the application (not the system locales) and prints the default settings
* **l4gettext:fetch**: auto generates the locales.php and encodings.php config files with the locales and encodings installed on your OS; in case the config files have not been published yet, this command will do so first

These commands use the options as set in the config file, but most can be overwritten at runtime by providing the appropriate parameters on the CLI. Check out the help for these commands for more info:

``` $ php artisan l4gettext:compile -h ```

``` $ php artisan l4gettext:extract --help ```

**NOTE**: *Running the l4gettext:extract command extracts all language messages from compiled templates AND php files in the views folder.*

The extract command creates a .pot file in the output directory if translatable strings are found. Please use the POEdit tool (link at bottom) to open this file.
Once you enter your translation string(s) in poedit, hit the save button. This will automatically generate the compiled/indexed version of the translation file .mo. 
Place this compiled .mo file in the appropriate locale folder (ex: app/locale/en_GB/LC_MESSAGES/messages.mo). 
Note that gettext is extremely finicky when it comes to folder conventions!

**IMPORTANT**:
*Every time you run the extract command, a brand new .pot file is generated in the storage/l4gettext folder. So, if you already
have translations entered for a few of the strings, DO NOT run extract again as this will create a fresh .pot file overwriting
your edits. 
POEdit makes managing your .pot files very easy. For updating your pot file (in case you made last minute additions/changes to your templates), enter the paths to BOTH
your "app/views" and "app/storage/gettext" folders in the POEdit "catalog properties"/"source paths" dialog. Then, click on 
the "Update" button on the (POEdit) toolbar to merge in the updates.*
 
## Supported locales

Gettext requires that the locales that you want to use are installed on your system. If you attempt to set a locale that is valid, but not installed, an exception will be thrown.
You can check which locales are installed on your system by executing the following command on the (linux) CLI:

``` $ locale -a ```

Or, use the following command to generate a new locale:

``` $ sudo locale-gen nl_NL.UTF-8 ```

For locales available on Windows systems, check out [this link](http://msdn.microsoft.com/en-us/library/39cwe7zf%28v=vs.90%29.aspx). UTF-8 is not supported on Windows systems.

## Laravel routes

The package registers 2 new routes:

* your-site-url/**set-locale/{locale}**
* your-site-url/**set-encoding/{encoding}**

These routes allow you to easily switch between locales/encodings by letting your users click on a link.
The routes will automatically return the user to the address they came from (using the HTTP_REFERER), or, if that is unavailable, it will redirect to '/'.
The input provided to {locale} and {encoding} is checked against an array of valid locales/encodings, which can be changed using the configuration files.
The set locale/encoding is stored in a session.

So even if your system has 10 different locales installed, if your application only supports 3, simply keep only the selected 3 in the locales array.

* Check out the entire list of locales in: **vendor/netson/l4gettext/src/config/locales.php**
* Check out the entire list of encodings in: **vendor/netson/l4gettext/src/config/encodings.php**

**NOTE**: *If you attempt to set a locale or encoding that is not installed on your system, an exception will be thrown.*


## Integration

If you would like to integrate this package into your own module/package, here is a list of the most important methods:

* L4gettext::setLocale($locale) - *sets locale*
* L4gettext::getLocale() - *returns (string) locale, or throws LocaleNotSetException if locale has not been set*
* L4gettext::hasLocale() - *return (bool) true if locale has been set, false otherwise*
* L4gettext::setEncoding($encoding) - *sets encoding*
* L4gettext::getEncoding() - *returns (string) encoding, or throws EncodingNotSetException if encoding has not been set*
* L4gettext::hasEncoding() - *return (bool) true if encoding has been set, false otherwise*
* L4gettext::getLocaleAndEncoding() - *returns (string) locale.encoding*


## Troubleshooting ##

### Gettext location ###

This package assumes that the xgettext library is in the path of your webserver user. If this is not the case, you may receive a *"[127] Command not found found"* error message. To fix this, you have two options:

* Add the path to your xgettext binary to the config file (assuming /usr/bin is the location of your xgettext binary):

```php
<?php
	return array(
		// ...
		'xgettext' => array(
			'binary_path' => "/usr/bin",
			// ...
```

**Make sure** you publish the config file first:

``` $ php artisan config:publish netson/l4gettext ```

Then, edit the local config file in ``` app/config/packages/netson/l4gettext/config.php ```

* **OR**, Execute the following command on the CLI (assuming www-data is your webserver user):

```
$ su www-data
$ echo 'export PATH=$PATH:/path/to/your/xgettext/folder' >> ~/.bashrc
```

You are now good to go!

### Gettext caching ###

By design, gettext will cache the translated .mo file upon first usage. This sometimes leads to unwanted behavior where the changes to your .po/.mo file don't show up in your application. The easiest way to fix this is to give your webserver (ie. Apache) a restart. Other workarounds do exist (for example for development environments), but I suggest you Google those if you're interested.


## Changelog

#### Version 1.3.0
* Added support for Laravel 4.1
* Improved ExtractCommand: php files in the compiled folder are merged with php files in the view folder prior to extraction of messages - Thanks to [mnshankar](https://github.com/mnshankar)
* Improved documentation

#### Version 1.2.0
* Removed dependency for L4shell - replaced this with the Symfony Process component 


## More information
* [Laravel website](http://laravel.com)
* [Gettext documentation](http://www.gnu.org/software/gettext/)
* [XGettext documentation](http://www.gnu.org/software/gettext/manual/html_node/xgettext-Invocation.html)
* [PHP Gettext manual](http://php.net/manual/en/book.gettext.php)
* [PoEdit](http://www.poedit.net/)
