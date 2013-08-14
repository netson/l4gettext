<?php namespace Netson\L4gettext\Compilers;

use Illuminate\View\Compilers\BladeCompiler as LaravelBladeCompiler;
use Illuminate\Filesystem\Filesystem;

class BladeCompiler extends LaravelBladeCompiler {

    /**
     * new method which allows overwriting the cache path after initialization
     * instead of having to provide it to the constructor
     *
     * @param string $cachePath
     */
    public function setCachePath ($cachePath)
    {
        $this->cachePath = $cachePath;

    }

    /**
     * new method which returns the set cachePath
     *
     * @return string
     */
    public function getCachePath ()
    {
        return $this->cachePath;

    }

    /**
     * method to set the $files attribute after calling the constructor
     *
     * @param \Netson\L4gettext\Compilers\Filesystem $files
     */
    public function setFiles (Filesystem $files)
    {
        $this->files = $files;

    }

    /**
     * method just adds extension .php to parents' compiled path
     *
     * @param string $path
     * @return string
     */
    public function getCompiledPath ($path)
    {
        return parent::getCompiledPath($path) . ".php";

    }

    /**
     * method differs from parent method in that it returns the compiled path
     * this can be used by the calling function to do cleanup afterwards
     *
     * @param string $path
     * @return string
     */
    public function compile ($path)
    {
        $contents = $this->compileString($this->files->get($path));

        if (!is_null($this->cachePath))
        {
            $compiled_path = $this->getCompiledPath($path);
            $this->files->put($compiled_path, $contents);
        }

        return $compiled_path;

    }

}

?>