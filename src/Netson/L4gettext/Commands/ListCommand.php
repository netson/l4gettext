<?php
namespace Netson\L4gettext\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use L4gettext;

class ListCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'l4gettext:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all locales and encodings supported by the application (read from config file)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct ()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire ()
    {
        /**
         * lists
         */
        $locales = Config::get("l4gettext::locales.list");
        $encodings = Config::get("l4gettext::encodings.list");

        /**
         * list locales
         */
        $this->info("  listing supported locales: [" . count($locales) . "]");

        // loop through locales
        foreach ($locales as $l)
            $this->comment("  - " . $l);

        /**
         * list encodings
         */
        $this->info("  listing supported encodings: [" . count($encodings) . "]");

        // loop through encodings
        foreach ($encodings as $e)
            $this->comment("  - " . $e);

        /**
         * list current default settings
         */
        $this->info("  current default setting: [" . L4gettext::getLocaleAndEncoding() . "]");
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