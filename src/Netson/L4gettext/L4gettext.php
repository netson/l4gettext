<?php namespace Netson\L4gettext;

use Config;
use Session;
use File;

class L4gettext {

    /**
     * variable holds the current locale
     *
     * @var string
     */
    protected $locale = null;

    /**
     * variable holds the current encoding
     *
     * @var string
     */
    protected $encoding = null;

    /**
     * constructor method
     * accepts an optional $locale, otherwise the default will be used
     *
     * @param string $locale
     */
    public function __construct ()
    {
        // check if a locale is present in the session
        // otherwise use default
        $session_locale = Session::get('l4gettext_locale', null);
        $locale = (is_null($session_locale)) ? Config::get("l4gettext::config.default_locale") : $session_locale;
        Session::forget('l4gettext_locale');

        // check if an encoding is present in the session
        $session_encoding = Session::get('l4gettext_encoding', null);
        $encoding = (is_null($session_encoding)) ? Config::get("l4gettext::config.default_encoding") : $session_encoding;
        Session::forget('l4gettext_encoding');

        // set the encoding and locale
        $this->setEncoding($encoding)->setLocale($locale);

        // determine and set textdomain
        $textdomain = Config::get("l4gettext::config.textdomain");
        $path = Config::get("l4gettext::config.path_to_mo");
        $this->setTextDomain($textdomain, $path);

    }

    /**
     * method to set the encoding
     *
     * @param string $encoding
     * @return \Netson\L4gettext\L4gettext
     * @throws InvalidEncodingException
     */
    public function setEncoding ($encoding)
    {
        // fetch encodings list
        $encodings = Config::get('l4gettext::encodings.list');

        // sanity check
        if (!in_array($encoding, $encodings))
            throw new InvalidEncodingException("The provided encoding [$encoding] does not exist in the list of valid encodings [config/encodings.php]");

        // set encoding, transform to uniform syntax
        $this->encoding = $encoding;

        // save locale to session
        Session::put('l4gettext_encoding', $this->encoding);

        // return - allow object chaining
        return $this;
    }

    /**
     * method to set the locale
     *
     * @param string $locale
     * @return \Netson\L4gettext\L4gettext
     * @throws InvalidLocaleException
     */
    public function setLocale ($locale)
    {
        // fetch locales list
        $locales = Config::get('l4gettext::locales.list');

        // sanity check
        if (!in_array($locale, $locales))
            throw new InvalidLocaleException("The provided locale [$locale] does not exist in the list of valid locales [config/locales.php]");

        // set locale in class
        $this->locale = $locale;

        // get localecodeset
        $localecodeset = $this->getLocaleAndEncoding();

        // set environment variable
        if (!putenv('LC_ALL=' . $localecodeset))
            throw new EnvironmentNotSetException("The given locale [$localecodeset] could not be set as environment [LC_ALL] variable; it seems it does not exist on this system");

        if (!putenv('LANG=' . $localecodeset))
            throw new EnvironmentNotSetException("The given locale [$localecodeset] could not be set as environment [LANG] variable; it seems it does not exist on this system");

        // set locale - the exception is only thrown in case the app is NOT run from the command line
        // ignoring the cli creates a chicken and egg problem when attempting to fetch the installed locales/encodings,
        // since the ServiceProvider will always attempt to load the locale/encoding before the config files can even be
        // published; thus not allowing the user to change the default settings which should prevent the exception in the first place
        if (!setlocale(LC_ALL, $localecodeset) && !\App::runningInConsole())
            throw new LocaleNotFoundException("The given locale [$localecodeset] could not be set; it seems it does not exist on this system");

        // save locale to session
        Session::put('l4gettext_locale', $this->locale);

        // return - allow object chaining
        return $this;

    }

    /**
     * method to merge the locale and encoding into a single string
     *
     * @return string
     */
    public function getLocaleAndEncoding ()
    {
        // windows compatibility - use only the locale, not the encoding
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            return $this->getLocale();
        else
            return $this->getLocale() . "." . $this->getEncoding();

    }

    /**
     * method to fetch the set encoding
     *
     * @return string
     * @throws EncodingNotSetException
     */
    public function getEncoding ()
    {
        // sanity check
        if (!$this->hasEncoding())
            throw new EncodingNotSetException("The encoding needs to be set before calling L4gettext::getEncoding()");

        // return encoding
        return $this->encoding;

    }

    /**
     * method to fetch the set locale
     *
     * @return string
     * @throws LocaleNotSetException
     */
    public function getLocale ()
    {
        // sanity check
        if (!$this->hasLocale())
            throw new LocaleNotSetException("The locale needs to be set before calling L4gettext::getLocale()");

        // return locale
        return $this->locale;

    }

    /**
     * method to check if an encoding has been set
     *
     * @return boolean
     */
    public function hasEncoding ()
    {
        // check if encoding has been set
        if (isset($this->encoding) && !is_null($this->encoding))
            return true;
        else
            return false;

    }

    /**
     * method to check if a locale has been set
     *
     * @return boolean
     */
    public function hasLocale ()
    {
        // check if locale has been set
        if (isset($this->locale) && !is_null($this->locale))
            return true;
        else
            return false;

    }

    /**
     * method to set the text domain
     *
     * @param string $textdomain
     * @param string $path
     * @return \Netson\L4gettext\L4gettext
     */
    public function setTextDomain ($textdomain, $path)
    {
        // full path to localization messages
        $full_path = app_path() . DIRECTORY_SEPARATOR . $path;

        // sanity check - path must exist relative to app/ folder
        if (!File::isDirectory($full_path))
            $this->createFolder($path);

        // bind text domain
        bindtextdomain($textdomain, $full_path);

        // set text domain
        textdomain($textdomain);

        // allow object chaining
        return $this;

    }

    /**
     * method which auto creates the LC_MESSAGES folder for each set locale, if they do not exist yet
     *
     * @param string $path
     * @return \Netson\L4gettext\L4gettext
     * @throws LocaleNotSetException
     */
    public function createFolder ($path)
    {
        // set full path
        $full_path = app_path() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $this->getLocale() . DIRECTORY_SEPARATOR . 'LC_MESSAGES';

        // check if the folder exists
        if (!File::isDirectory($full_path))
        {
            // folder does not exist, attempt to create it
            // throws an ErrorException when failed
            if (!File::makeDirectory($full_path, 0755, true))
                throw new LocaleFolderCreationException("The locale folder [$full_path] does not exist and could not be created automatically; please create the folder manually");
        }

        // allow object chaining
        return $this;

    }

    /**
     * method to dump the current locale/encoding settings to string
     *
     * @return string
     */
    public function __toString ()
    {
        return (string) $this->getLocaleAndEncoding();

    }

}

?>