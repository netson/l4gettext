<?php
namespace Netson\L4gettext\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use File;
use L4shell;

class ExtractCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'l4gettext:extract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts all gettext translation strings from the Blade template files';

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
         * check if any php files exist in the input folder
         */
        $input_folder = app_path() . DIRECTORY_SEPARATOR . $this->option('input_folder') . DIRECTORY_SEPARATOR;
        $templates = File::glob($input_folder . "*.php");

        // determine number of files in input folder
        $i = count($templates);

        // sanity check
        if ($i < 1)
        {
            // throw exception
            throw new \Netson\L4gettext\NoFilesToExtractFromException("The given input folder [$input_folder] does not contain any compiled templates");

            /* CODE TO BE USED ONCE UNIT TESTING OF INTERACTIVE COMMANDS IS WORKING
            // print error
            $this->error("  the given input folder [$input_folder] does not contain any php files to parse");

            // ask if compiler should be run
            if ($this->confirm("  would you like to run the compiler using the default settings? [y|n]"))
            {
                $this->line(""); // add empty line
                $this->call('l4gettext:compile'); // call compile command
                // re-check
                $templates = File::glob($input_folder . "*.php");

                // determine number of files in input folder
                $i = count($templates);
            }
            else
            {
                $this->line(""); // add empty line
                return;
            }
            */
        }

        // add info
        $this->comment("  [$i] files found in input folder [$input_folder]");

        /**
         * Fetch path to xgettext binary
         */
        $path = ($this->option('binary_path') == "") ? null : $this->option('binary_path') . DIRECTORY_SEPARATOR;

        /**
         * set base xgettext command and arguments array
         */
        $xgettext_command = "xgettext";
        $xgettext_arguments = array();
        $xgettext_output_file = storage_path() . DIRECTORY_SEPARATOR . $this->option('output_folder') . DIRECTORY_SEPARATOR . Config::get("l4gettext::config.textdomain") . ".pot";

        /**
         * add language argument
         */
        if ($this->option('language'))
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "--language=" . $this->option('language');
        }

        /**
         * add comments argument
         */
        if ($this->option('comments'))
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "--add-comments=" . $this->option('comments');
        }

        /**
         * add force_po argument
         */
        if ($this->option('force_po') == true)
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "--force-po";
        }

        /**
         * add output folder argument
         */
        if ($this->option('output_folder'))
            $xgettext_command .= " -o " . $xgettext_output_file;

        /**
         * add from code argument
         */
        if ($this->option('from_code'))
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "--from-code=" . $this->option('from_code');
        }

        /**
         * add copyright holder argument
         */
        if ($this->option('copyright_holder'))
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "--copyright-holder=\"" . $this->option('copyright_holder') . "\"";
        }

        /**
         * add package name argument
         */
        if ($this->option('package_name'))
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "--package-name=\"" . $this->option('package_name') . "\"";
        }

        /**
         * add package version argument
         */
        if ($this->option('package_version'))
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "--package-version=\"" . $this->option('package_version') . "\"";
        }

        /**
         * add email address option
         */
        if ($this->option('email_address'))
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] .= "--msgid-bugs-address=\"" . $this->option('email_address') . "\"";
        }

        /**
         * add keyword options
         */
        $keyword_list = array_merge($this->option('keywords'), $this->option('additional_keywords'));

        // loop through all keywords
        foreach ($keyword_list as $k)
        {
            $xgettext_command .= " %s";
            $xgettext_arguments[] = "-k$k"; // using the shorthand xgettext notation for the keywords: -k%k
        }

        // add info
        $this->comment("  [" . count($keyword_list) . "] keywords found");

        /**
         * add input folder argument
         */
        if ($this->option('input_folder'))
            $xgettext_command .= " " . app_path() . DIRECTORY_SEPARATOR . $this->option('input_folder') . DIRECTORY_SEPARATOR . "*.php";

        /**
         * create l4shell command and execute
         * the setAllowedCharacters method is used to prevent the *-sign from being escaped
         */
        $command = L4shell::setCommand($xgettext_command)
                ->setExecutablePath($path)
                ->setArguments($xgettext_arguments)
                ->sendToDevNull()
                ->setAllowedCharacters(array("*"));

        $command->execute();

        $this->info("  POT file located in [$xgettext_output_file]");
        $this->info("  xgettext successfully executed");

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
        /**
         * set defaults
         */
        $defaults = array(
            'binary_path'      => Config::get("l4gettext::config.xgettext.binary_path"),
            'language'         => Config::get("l4gettext::config.xgettext.language"),
            'comments'         => Config::get("l4gettext::config.xgettext.comments"),
            'force_po'         => Config::get("l4gettext::config.xgettext.force_po"),
            'input_folder'     => Config::get("l4gettext::config.xgettext.input_folder"),
            'output_folder'    => Config::get("l4gettext::config.xgettext.output_folder"),
            'from_code'        => Config::get("l4gettext::config.xgettext.from_code"),
            'copyright_holder' => Config::get("l4gettext::config.xgettext.copyright_holder"),
            'package_name'     => Config::get("l4gettext::config.xgettext.package_name"),
            'package_version'  => Config::get("l4gettext::config.xgettext.package_version"),
            'email_address'    => Config::get("l4gettext::config.xgettext.email_address"),
            'keywords'         => Config::get("l4gettext::config.xgettext.keywords"),
        );

        /**
         * return the options array
         */
        return array(
            array('binary_path', 'p', InputOption::VALUE_REQUIRED, 'The path to your xgettext binary, without a trailing slash', $defaults['binary_path']),
            array('language', 'l', InputOption::VALUE_REQUIRED, 'The script/programming language of the files to be scanned', $defaults['language']),
            array('comments', 'c', InputOption::VALUE_REQUIRED, 'The docbloc text to scan for', $defaults['comments']),
            array('force_po', 'f', InputOption::VALUE_REQUIRED, 'Forces the creation of a .pot file regardless of any translation strings found (bool)', $defaults['force_po']),
            array('input_folder', 'i', InputOption::VALUE_REQUIRED, 'The input folder to scan for .php files, relative to the app/ folder', $defaults['input_folder']),
            array('output_folder', 'o', InputOption::VALUE_REQUIRED, 'The output folder to scan for .php files, relative to the app/storage folder', $defaults['output_folder']),
            array('from_code', 'e', InputOption::VALUE_REQUIRED, 'The encoding of the source files', $defaults['from_code']),
            array('copyright_holder', 'a', InputOption::VALUE_REQUIRED, 'The copyright holder/author of the source translations', $defaults['copyright_holder']),
            array('package_name', 'y', InputOption::VALUE_REQUIRED, 'The package name', $defaults['package_name']),
            array('package_version', 'x', InputOption::VALUE_REQUIRED, 'The package version', $defaults['package_version']),
            array('email_address', 'm', InputOption::VALUE_REQUIRED, 'The email address of the author', $defaults['email_address']),
            array('keywords', 'k', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The keywords to search for in the source files', $defaults['keywords']),
            array('additional_keywords', 'z', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Keywords in addition to the default keywords'),
        );

    }

}