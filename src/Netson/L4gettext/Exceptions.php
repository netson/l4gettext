<?php namespace Netson\L4gettext;

// include exception namespace
use Exception;

/*
 * thrown when the given encoding does not exist
 */

class InvalidEncodingException extends Exception {}

/*
 * thrown when the given locale does not exist
 */

class InvalidLocaleException extends Exception {}

/**
 * thrown when the given path does not exist
 */
class LocaleFolderCreationException extends Exception {}

/**
 * thrown when calling a method that requires the locale to be set, but that is not the case
 */
class EncodingNotSetException extends Exception {}

/**
 * thrown when calling a method that requires the locale to be set, but that is not the case
 */
class LocaleNotSetException extends Exception {}

/**
 * thrown when locale is not installed on the system
 */
class LocaleNotFoundException extends Exception {}

/**
 * thrown when an environment variable could not be set
 */
class EnvironmentNotSetException extends Exception {}

/**
 * thrown when 0 templates could be found to compile in the given path
 */
class NoTemplatesToCompileException extends Exception {}

/**
 * thrown when 0 templates could be found to compile in the given path
 */
class NoFilesToExtractFromException extends Exception {}

/**
 * thrown when exec() function does not exist or is unavailable
 */
class NoExecFunctionException extends Exception {}

/**
 * thrown when xgettext was not successfully executed
 */
class XgettextException extends Exception {}

/**
 * thrown when fetching the system installed locales/encodings and an item is found with inconsistent formatting
 */
class InvalidItemCountException extends Exception {}

/**
 * thrown if attempting to auto generate locales/encodings files and config files are not writable
 */
class ConfigFilesNotWritableException extends Exception {}

/**
 * thrown if attempting to auto generate locales/encodings files and config files are not writable
 */
class ConfigFilesNotPublishedException extends Exception {}

/**
 * thrown if the cli command fetch could not retrieve the installed locales
 */
class CannotFetchInstalledLocalesException extends Exception {}

/**
 * thrown if fetch command is run on windows
 */
class FetchCommandNotSupportedOnWindowsException extends Exception {}

/**
 * thrown if fetch command is run on windows
 */
class InstallCommandNotSupportedOnWindowsException extends Exception {}
?>