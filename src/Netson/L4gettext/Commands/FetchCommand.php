<?php
namespace Netson\L4gettext\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\ProcessBuilder;
use File;

class FetchCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'l4gettext:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all system installed locales and encodings and writes them to the published config files';

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
            throw new \Netson\L4gettext\FetchCommandNotSupportedOnWindowsException("The fetch command requires the cli command 'locale' to be available; this is not available on a windows system");

        /**
         * check if config has been published
         */
        $config_path = app_path() . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "packages" . DIRECTORY_SEPARATOR . "netson" . DIRECTORY_SEPARATOR . "l4gettext";
        $locales_file = $config_path . DIRECTORY_SEPARATOR . "locales.php";
        $encodings_file = $config_path . DIRECTORY_SEPARATOR . "encodings.php";
        $locales_dist_file = $config_path . DIRECTORY_SEPARATOR . "locales.dist";
        $encodings_dist_file = $config_path . DIRECTORY_SEPARATOR . "encodings.dist";

        // check if they exist
        if (!File::isDirectory($config_path) || !File::isFile($locales_file) || !File::isFile($encodings_file) || !File::isFile($locales_dist_file) || !File::isFile($encodings_dist_file))
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
        if (!File::isWritable($locales_file) || !File::isWritable($encodings_file))
            throw new \Netson\L4gettext\ConfigFilesNotWritableException("the package config files are not writable; please check your file permissions and try again");
        else
            $this->comment("  config files are writable");

        /**
         * fetch list of installed locales on current system
         */
        // info
        $this->comment("  detecting installed locales and encodings");

        /**
         * use symfony process builder to build and execute command on cli
         */
        $builder = $this->procBuilder;
        $process = $builder->setPrefix('locale')
                ->setArguments(array('-a'))
                ->getProcess();

        // run process
        $process->run();

        // check for errors
        if (!$process->isSuccessful())
            throw new \Netson\L4gettext\CannotFetchInstalledLocalesException("Could not execute the locale command to retrieve installed locales on this system");

        // fetch list using command line and convert to array
        $list = explode("\n", $process->getOutput());

        // set empty list of locales and encodings
        $locales = array();
        $encodings = array();

        // loop through list and extract encodings/locales
        foreach ($list as $item)
        {
            // sanity check - skip empty items
            if (!$item)
                continue;

            // seperate locale and encoding
            $le = explode(".", $item);
            $count = count($le);

            // check item for proper length
            if ($count != 1 && $count != 2)
                throw new \Netson\L4gettext\InvalidItemCountException("The item [$item] contains more than 2 sections (separated by a dot)" . PHP_EOL . "I could not determine the locale and encoding of this item automatically");

            // check if it's just locale or also encoding
            // in either case, set both the key and the value to avoid duplicates
            if ($count == 1)
            {
                // just locale
                $locales[$le[0]] = $le[0];
            }
            else
            {
                // locale and encoding
                $locales[$le[0]] = $le[0];
                $encodings[$le[1]] = $le[1];
            }
        }

        /**
         * removes existing locales.php and encodings.php
         * and creates new ones based on the -dist files
         */
        $this->comment("  recreating the locales and encodings config files based on current system");

        // delete existing files
        File::delete($locales_file);
        File::delete($encodings_file);

        // copy dist files
        File::copy($locales_dist_file, $locales_file);
        File::copy($encodings_dist_file, $encodings_file);

        /**
         * generate contents of locales file to be appended to dist file
         */
        $locales_contents = 'return array(' . PHP_EOL . "\t" . '"list" => array(' . PHP_EOL;
        foreach ($locales as $l)
            $locales_contents .= "\t\t" . '"' . $l . '",' . PHP_EOL;
        $locales_contents .= "\t)\n);\n?>";

        // append to copied dist file
        File::append($locales_file, $locales_contents);

        /**
         * generate contents of locales file to be appended to dist file
         */
        $encodings_contents = 'return array(' . PHP_EOL . "\t" . '"list" => array(' . PHP_EOL;
        foreach ($encodings as $e)
            $encodings_contents .= "\t\t" . '"' . $e . '",' . PHP_EOL;
        $encodings_contents .= "\t)\n);\n?>";

        // append to copied dist file
        File::append($encodings_file, $encodings_contents);

        /**
         * inform user of completion and list installed items
         */
        $this->info("  done generating config files");
        $this->info("  - automatically detected [" . count($locales) . "] locales and [" . count($encodings) . "] encodings");
        $this->info("  - use the l4gettext:list command to check the detected locales/encodings");
        $this->info("  - be sure to verify your default settings in the config.php file");

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