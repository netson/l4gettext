<?php

namespace Netson\L4gettext\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\ProcessBuilder;
use File;

class InstallCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'l4gettext:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the l4gettext module by publishing the config files, fetching all system installed locales and encodings and sets the defaults according to your local system';

    /**
     * object containing the symfony ProcessBuilder
     *
     * @var type ProcessBuilder
     */
    protected $procBuilder;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct (ProcessBuilder $procBuilder = null)
    {
        parent::__construct();

        // set process builder
        if (!is_null($procBuilder))
            $this->procBuilder = $procBuilder;
        else
            $this->procBuilder = new ProcessBuilder;

    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire ()
    {
        /**
         * sanity check - command not supported on windows
         */
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            throw new \Netson\L4gettext\InstallCommandNotSupportedOnWindowsException("The install command requires the cli command 'locale' to be available; this is not available on a windows system");

        /**
         * check if config has been published
         */
        $config_path         = app_path() . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "packages" . DIRECTORY_SEPARATOR . "netson" . DIRECTORY_SEPARATOR . "l4gettext";
        $locales_file        = $config_path . DIRECTORY_SEPARATOR . "locales.php";
        $encodings_file      = $config_path . DIRECTORY_SEPARATOR . "encodings.php";
        $config_file         = $config_path . DIRECTORY_SEPARATOR . "config.php";
        $locales_dist_file   = $config_path . DIRECTORY_SEPARATOR . "locales.dist";
        $encodings_dist_file = $config_path . DIRECTORY_SEPARATOR . "encodings.dist";
        $config_dist_file    = $config_path . DIRECTORY_SEPARATOR . "config.dist";

        // check if they exist
        if (!File::isDirectory($config_path) || !File::isFile($locales_file) || !File::isFile($encodings_file) || !File::isFile($locales_dist_file) || !File::isFile($encodings_dist_file) || !File::isFile($config_dist_file) || !File::isFile($config_file))
        {
            // inform user and publish config files
            $this->comment("  config files have not been published, publishing now");
            $this->call("config:publish", array("package" => "netson/l4gettext"));
        }
        else
            $this->comment("  config files have already been published");

        /**
         * check if the config files are writable
         */
        if (!File::isWritable($locales_file) || !File::isWritable($encodings_file) || !File::isWritable($config_file))
            throw new \Netson\L4gettext\ConfigFilesNotWritableException("the package config files are not writable; please check your file permissions and try again");
        else
            $this->comment("  config files are writable");

        /**
         * detect current system default locale and set it in the config file
         */
        $this->comment("  detecting locale and encondig for your system");
        $system_default = setlocale(LC_CTYPE, 0);

        // seperate locale and encoding
        $le    = explode(".", $system_default);
        $count = count($le);

        // check item for proper length
        if ($count != 1 && $count != 2)
            throw new \Netson\L4gettext\InvalidItemCountException("The system default [$item] contains more than 2 sections (separated by a dot)" . PHP_EOL . "I could not determine the locale and encoding of this item automatically");

        // check if it's just locale or also encoding
        // in either case, set both the key and the value to avoid duplicates
        if ($count == 1)
        {
            // just locale, but set an empty encoding to prevent php tripping over an undefined variable
            $default_locale   = $le[0];
            $default_encoding = "";
        }
        else
        {
            // locale and encoding
            $default_locale   = $le[0];
            $default_encoding = $le[1];
        }

        $this->comment("  creating the l4gettext config file with your system defaults");

        // delete existing config file
        File::delete($config_file);

        // copy dist file
        $config = File::get($config_dist_file);

        /**
         * generate contents of config file to be appended to dist file
         */
        $config = str_replace("{{default_locale}}", $default_locale, $config);
        $config = str_replace("{{default_encoding}}", $default_encoding, $config);

        // append to copied dist file
        File::put($config_file, $config);

        /**
         * fetch list of installed locales on current system
         */
        $this->call("l4gettext:fetch");

        /**
         * inform user of completion and list installed items
         */
        $this->info("  done installing the l4gettext module");
        $this->info("  - your current default settings are locale = [$default_locale] and encoding = [$default_encoding]");
        $this->info("  - use the l4gettext:list command to check the detected locales/encodings");
        $this->info("  - be sure to verify your default settings in the (published) config.php file");

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments ()
    {
        return array();

    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions ()
    {
        return array();

    }

}
