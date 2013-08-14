# Gettext for Laravel 4

This package adds gettext functionality to the Laravel 4 framework which allows you to easily translate your application using tools such as PoEdit. Although Laravel 4 comes with a built-in translation engine, I prefer using tools like PoEdit, which takes away the need of maintaining arrays with text. This is my first Laravel experience/package, so if you come across any errors or have suggestions for improvements, let me know.

Written by: **Rinck Sonnenberg (Netson)**


## How does it work?

This package simply utilizes existing functionality to allow you to use gettext from within the Laravel Blade templates. You can use the following functions in your templates:

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


## Dependencies

Aside from some of the laravel 4 components, there are only logical dependencies:
* gettext library
* xgettext (should be installed when installing the gettext library)
* php-gettext
* netson/l4shell package (already set in the composer.json file)

**NOTE**: *This package has only been tested on linux (Ubuntu Server 12.04 LTS).*


## Installation

**Installation using composer:**

* add the netson/l4gettext as a required package:

``` $ php composer.phar require "netson/l4gettext:1.0.x" ```

* update composer:

``` $ php composer.phar update ```

* add the l4gettext service provider to the laravel app/config/app.php file, in array key 'provider': 'Netson\L4gettext\L4gettextServiceProvider'

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

Before changing any of the configuration options, be sure to publish the package config:

``` $ php artisan config:publish netson/l4gettext ```

Now, make sure you set the proper **copyright holder**, **package name**, **package version** and **email address** in the file ``` app/config/packages/netson/l4gettext ```

You are now good to go!


## Command line options

There are 3 artisan commands for this package:

* **l4gettext:compile**: compiles all template files to a specific folder (not the default cache folder)
* **l4gettext:extract**: extracts all the language strings from the compiled templates
* **l4gettext:list**: lists the locales/encodings supported by the application (not the system locales) and prints the default settings

These commands use the options as set in the config file, but most can be overwritten at runtime by providing the appropriate parameters on the CLI. Check out the help for these commands for more info:

``` $ php artisan l4gettext:compile -h ```

``` $ php artisan l4gettext:extract --help ```

**NOTE**: *When using the default settings, running the l4gettext:extract command without calling the l4gettext:compile command first will issue a warning and ask if the compile command should be executed first.*

## Supported locales

Gettext requires that the locales that you want to use are installed on your system. If you attempt to set a locale that is valid, but not installed, an exception will be thrown.
You can check which locales are installed on your system by executing the following command on the (linux) CLI:

``` $ locale -a ```

Or, use the following command to generate a new locale:

``` $ sudo locale-gen nl_NL.UTF-8 ```


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

**NOTE**: *If you attempt to set a locale or enconding that is not installed on your system, an exception will be thrown.*


## Integration

If you would like to integrate this package into your own module/package, here is a list of the most important methods:

* L4gettext::setLocale($locale) - *sets locale*
* L4gettext::getLocale() - *returns (string) locale, or throws LocaleNotSetException if locale has not been set*
* L4gettext::hasLocale() - *return (bool) true if locale has been set, false otherwise*
* L4gettext::setEncoding($encoding) - *sets encoding*
* L4gettext::getEncoding() - *returns (string) encoding, or throws EncodingNotSetException if encoding has not been set*
* L4gettext::hasEncoding() - *return (bool) true if encoding has been set, false otherwise*
* L4gettext::getLocaleAndEncoding() - *returns (string) locale.encoding*


## More information
* [Laravel website](http://laravel.com)
* [Gettext documentation](http://www.gnu.org/software/gettext/)
* [XGettext documentation](http://www.gnu.org/software/gettext/manual/html_node/xgettext-Invocation.html)
* [PHP Gettext manual](http://php.net/manual/en/book.gettext.php)
* [PoEdit](http://www.poedit.net/)
