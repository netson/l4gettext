<?php
/**
 * if you wish to alter this configuration file, make sure you issue the publish command and change the published version:
 * # php artisan config:publish netson/l4gettext
 */
return array(
    /**
     * set the preferred default locale according to [2letterlanguagecode_2LETTERCOUNTRYCODE]
     * the default is [en_US]
     */
    'default_locale'   => 'en_US',
    /**
     * set preferred default encoding
     * the default is [utf8]
     */
    'default_encoding' => 'utf8',
    /**
     * determines the text domain; this should be unique for each application
     * the default is [messages]
     */
    'textdomain'       => 'messages',
    /**
     * the path to your mo files
     * this should be relative to the laravel app/ dir
     * do not add starting ot trailing slashes
     * l4gettext will automatically attempt to create the proper directory structure
     * the default is [locale]
     */
    'path_to_mo'       => 'locale',
    /**
     * compiler settings
     */
    'compiler'         => array(
        /**
         * set the folder to scan for blade templates
         * the path is relative to the /app folder and should not contain any leading ot trailing slashes
         * the default is [views]
         */
        'input_folder'  => 'views',
        /**
         * set the folder where the compiled templates are stored
         * the path is relative to the /app/storage folder and should not contain and leading or trailing spaces
         * the default is [l4gettext]
         */
        'output_folder' => 'l4gettext',
        /**
         * determines the number of subdirectories that will be scanned for blade templates
         * starting from the input_folder directory
         * the default is [10]
         */
        'levels'        => 10,
    ),
    /**
     * xgettext configuration
     */
    'xgettext'         => array(
        /**
         * In case your xgettext binary/executable is not called 'xgettext', you can change it here.
         * The default value is [xgettext]
         */
        'binary'           => "xgettext",
        /**
         * In case the path to your xgettext binary is not in your PATH, you can use the following
         * config option to manually set it. This path will then be added to the xgettext command.
         * If you leave this empty, it will be assumed that the xgettext binary is in the PATH of the OS user executing the command
         * Example:  /usr/bin
         *
         * Please do NOT add a trailing slash
         * The default value is []
         */
        'binary_path'      => "",
        /**
         * sets the script language for the xgettext command
         * you should never have to change this option
         * the default is [PHP]
         */
        'language'         => 'PHP',
        /**
         * determines which docbloc comments are included as a remarks for translators
         * the default is [TRANSLATORS]
         */
        'comments'         => 'TRANSLATORS',
        /**
         * force creation of .po(t) file, even if no strings were found
         * the default is [true]
         */
        'force_po'         => true,
        /**
         * input folder where php files to be scanned are located
         * the path is relative to the /app folder and should not contain any leading or trailing slashes
         * the default is [storage/l4gettext]
         */
        'input_folder'     => 'storage/l4gettext',
        /**
         * output folder where the po(t) file will be stored
         * this path is relative to the /app/storage folder and should not contain leading or trailing slashes
         * the default is [l4gettext]
         */
        'output_folder'    => 'l4gettext',
        /**
         * set the encoding of the files
         * if left empty, ASCII will be used
         * the default is [utf8]
         */
        'from_code'        => 'utf8',
        /**
         * copright holder
         * this will be added to the .po(t) file to ensure your texts remain your property
         */
        'copyright_holder' => 'Netson',
        /**
         * the package name that will be included in the .po(t) file
         */
        'package_name'     => 'L4gettext',
        /**
         * the package version that will be included in the .po(t) file
         */
        'package_version'  => 'v1.0.0',
        /**
         * the email address that will be included in the .po(t) file
         */
        'email_address'    => 'r.sonnenberg@netson.nl',
        /**
         * specify the keywords used to scan the source files for strings
         * you can add your own shorthand/alternative keywords here which will then be scanned by xgettext
         * make sure the corresponding php function exists when you do
         * the defaults are [_, gettext, dgettext:2, dcgettext:2, ngettext:1,2, dngettext:2,3, dcngettext:2,3, _n:1,2]
         */
        'keywords'         => array(
            '_', // shorthand for gettext
            'gettext', // the default php gettext function
            'dgettext:2', // accepts plurals, uses the second argument passed to dgettext as a translation string
            'dcgettext:2', // accepts plurals, uses the second argument passed to dcgettext as a translation string
            'ngettext:1,2', // accepts plurals, uses the first and second argument passed to ngettext as a translation string
            'dngettext:2,3', // accepts plurals, used the second and third argument passed to dngettext as a translation string
            'dcngettext:2,3', // accepts plurals, used the second and third argument passed to dcngettext as a translation string
            '_n:1,2', // a custom l4gettext shorthand for ngettext (supports plurals)
        ),
    ),
);

?>