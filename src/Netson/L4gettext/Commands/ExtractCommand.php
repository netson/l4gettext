<?php

namespace Netson\L4gettext\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\ProcessBuilder;
use Config;
use File;

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
    public function __construct (ProcessBuilder $procBuilder = null)
    {
        parent::__construct();

        // set symfony process builder
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
         * check if any php files exist in the input folder
         */
        $input_folder       = app_path() . DIRECTORY_SEPARATOR . $this->option('input_folder') . DIRECTORY_SEPARATOR;
        $views_folder       = app_path() . DIRECTORY_SEPARATOR . $this->option('views_folder') . DIRECTORY_SEPARATOR;
        $additional_folders = base_path() . DIRECTORY_SEPARATOR;

        /**
         * determine file pattern to search for
         */
        $pattern  = getGlobPattern($this->option("levels"));
        $phpFiles = $views_folder . "{" . $pattern . "}*.php";

        /**
         * Merge the php files in compiled folder as well as views folder
         * The array_filter weeds out the blade templates from view since they are already
         * in the compiled folder
         */
        $templates = array_merge(File::glob($input_folder . "*.php"),
                array_filter(File::glob($phpFiles, GLOB_BRACE), function ($path) {
                    return (substr_count($path, 'blade.php') === 0);
                }));

        /**
         * sanity check
         */
        if (!is_array($this->option("additional_input_folders")))
            throw new \Netson\L4gettext\AdditionalInputFoldersNotArrayException("The additional_input_folders option should be an array, [" . gettype($this->option("additional_input_folders")) . "] given");

        /**
         * fetch any additional input folders from the command line or config option
         */
        foreach ($this->option("additional_input_folders") as $additional_folder)
        {
            // sanity check
            if (!File::isDirectory($additional_folder))
                throw new \Netson\L4gettext\AdditionalInputFolderNotFoundException("The additional input folder [$additional_folder] does not exist; please check your configuration");

            // generate the pattern for this folder
            $compiler_additional_pattern = $additional_folders . $additional_folder . DIRECTORY_SEPARATOR . "{" . $pattern . "}*.php";

            // merge with additional input folders
            $templates = array_merge($templates, File::glob($compiler_additional_pattern, GLOB_BRACE));
        }

        // determine number of files in input folder
        $i = count($templates);

        // sanity check
        if ($i < 1)
        {
            // throw exception
            throw new \Netson\L4gettext\NoFilesToExtractFromException("No templates found");

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
        $this->comment("  [$i] files found in input folder [$input_folder], views folder [$views_folder]");

        /**
         * array containging all xgettext parameters
         */
        $xgettext_arguments = array();

        /**
         * fetch path to xgettext binary and set binary
         */
        $path                 = ($this->option('binary_path') == "") ? "" : $this->option('binary_path') . DIRECTORY_SEPARATOR;
        $xgettext_arguments[] = $path . $this->option('binary');

        /**
         * add language argument
         */
        if ($this->option('language'))
            $xgettext_arguments[] = "--language=" . $this->option('language');

        /**
         * add comments argument
         */
        if ($this->option('comments'))
            $xgettext_arguments[] = "--add-comments=" . $this->option('comments');

        /**
         * add force_po argument
         */
        if ($this->option('force_po') == true)
            $xgettext_arguments[] = "--force-po";

        /**
         * add output folder argument
         */
        $xgettext_output_file = storage_path() . DIRECTORY_SEPARATOR . $this->option("output_folder") . DIRECTORY_SEPARATOR . Config::get("l4gettext::config.textdomain") . ".pot";
        if ($this->option('output_folder'))
            $xgettext_arguments[] = "--output=" . $xgettext_output_file;

        /**
         * add from code argument
         */
        if ($this->option('from_code'))
            $xgettext_arguments[] = "--from-code=" . $this->option('from_code');

        /**
         * add copyright holder argument
         */
        if ($this->option('copyright_holder'))
            $xgettext_arguments[] = "--copyright-holder=" . $this->option('copyright_holder');

        /**
         * add package name argument
         */
        if ($this->option('package_name'))
            $xgettext_arguments[] = "--package-name=" . $this->option('package_name');

        /**
         * add package version argument
         */
        if ($this->option('package_version'))
            $xgettext_arguments[] = "--package-version=" . $this->option('package_version');

        /**
         * add email address option
         */
        if ($this->option('email_address'))
            $xgettext_arguments[] = "--msgid-bugs-address=" . $this->option('email_address');

        /**
         * add keyword options
         */
        $keyword_list = array_merge($this->option('keywords'), $this->option('additional_keywords'));

        // loop through all keywords
        foreach ($keyword_list as $k)
        {
            $xgettext_arguments[] = "--keyword=$k"; // using the shorthand xgettext notation for the keywords: -k%k
        }
        // add info
        $this->comment("  [" . count($keyword_list) . "] keywords found");
        
        /**
         * add join-existing argument
         */
        if ($this->option('join-existing')) { 
            $this->comment($this->option('join-existing'));
            $xgettext_arguments[] = "-j";  
        }
        /**
         * add input folder argument
         */
        if ($this->option('input_folder'))
        {
            foreach ($templates as $t)
            {
                $xgettext_arguments[] = $t;
            }
        }

        /**
         * create symfony process and execute
         */
        $builder = $this->procBuilder;
        $builder->setArguments($xgettext_arguments);

        // fetch and execute process
        $process = $builder->getProcess();
        $process->run();

        /**
         * check if process completed successfully
         */
        if (!$process->isSuccessful())
            throw new \Netson\L4gettext\XgettextException("The xgettext command could not be executed:" . PHP_EOL .
            "[" . $process->getExitCode() . "] " . $process->getExitCodeText() . PHP_EOL . PHP_EOL .
            "Attempted to execute the following command:" . PHP_EOL . $process->getCommandLine());

        /**
         * add output info for user
         */
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
        $binary = Config::get("l4gettext::config.xgettext.binary");

        $defaults = array(
            'binary'                   => (isset($binary) == true) ? $binary : "xgettext",
            'binary_path'              => Config::get("l4gettext::config.xgettext.binary_path"),
            'language'                 => Config::get("l4gettext::config.xgettext.language"),
            'comments'                 => Config::get("l4gettext::config.xgettext.comments"),
            'force_po'                 => Config::get("l4gettext::config.xgettext.force_po"),
            'input_folder'             => Config::get("l4gettext::config.xgettext.input_folder"),
            'views_folder'             => Config::get("l4gettext::config.compiler.input_folder"),
            'additional_input_folders' => Config::get("l4gettext::config.xgettext.additional_input_folders"),
            'output_folder'            => Config::get("l4gettext::config.xgettext.output_folder"),
            'from_code'                => Config::get("l4gettext::config.xgettext.from_code"),
            'copyright_holder'         => Config::get("l4gettext::config.xgettext.copyright_holder"),
            'package_name'             => Config::get("l4gettext::config.xgettext.package_name"),
            'package_version'          => Config::get("l4gettext::config.xgettext.package_version"),
            'email_address'            => Config::get("l4gettext::config.xgettext.email_address"),
            'keywords'                 => Config::get("l4gettext::config.xgettext.keywords"),
            'levels'                   => Config::get("l4gettext::config.compiler.levels"),
        );

        /**
         * return the options array
         */
        return array(
            array('binary', 'b', InputOption::VALUE_REQUIRED, 'The name of your xgettext binary', $defaults['binary']),
            array('binary_path', 'p', InputOption::VALUE_REQUIRED, 'The path to your xgettext binary, without a trailing slash', $defaults['binary_path']),
            array('language', 'l', InputOption::VALUE_REQUIRED, 'The script/programming language of the files to be scanned', $defaults['language']),
            array('comments', 'c', InputOption::VALUE_REQUIRED, 'The docbloc text to scan for', $defaults['comments']),
            array('force_po', 'f', InputOption::VALUE_REQUIRED, 'Forces the creation of a .pot file regardless of any translation strings found (bool)', $defaults['force_po']),
            array('input_folder', 'i', InputOption::VALUE_REQUIRED, 'The input folder to scan for .php files, relative to the app/ folder', $defaults['input_folder']),
            array('views_folder', 'w', InputOption::VALUE_REQUIRED, 'The views folder to scan for .php files, relative to the app/ folder', $defaults['views_folder']),
            array('additional_input_folders', 'd', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Additional input folders to scan for php files, relative to the application root [/] folder',
                $defaults['additional_input_folders']),
            array('output_folder', 'o', InputOption::VALUE_REQUIRED, 'The output folder to scan for .php files, relative to the app/storage folder', $defaults['output_folder']),
            array('from_code', 'e', InputOption::VALUE_REQUIRED, 'The encoding of the source files', $defaults['from_code']),
            array('copyright_holder', 'a', InputOption::VALUE_REQUIRED, 'The copyright holder/author of the source translations', $defaults['copyright_holder']),
            array('package_name', 'y', InputOption::VALUE_REQUIRED, 'The package name', $defaults['package_name']),
            array('package_version', 'x', InputOption::VALUE_REQUIRED, 'The package version', $defaults['package_version']),
            array('email_address', 'm', InputOption::VALUE_REQUIRED, 'The email address of the author', $defaults['email_address']),
            array('keywords', 'k', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The keywords to search for in the source files', $defaults['keywords']),
            array('additional_keywords', 'z', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Keywords in addition to the default keywords'),
            array('levels', 's', InputOption::VALUE_REQUIRED, 'The number of subdirectories to scan for templates', $defaults['levels']),
            array('join-existing', 'j', null, 'Join with existing results'),
        );

    }

}
