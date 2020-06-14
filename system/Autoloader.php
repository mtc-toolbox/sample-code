<?php

namespace system;

/**
 * Class Autoloader (PSR standart)
 * @package system
 */
class Autoloader
{
    const OPTION_NAME_PREFIX   = 'prefix';
    const OPTION_NAME_BASE_DIR = 'base-dir';
    const OPTION_NAME_PREPEND  = 'prepend';

    /**
     * @var array
     */
    protected $prefixes = [];

    /**
     * Autoloader constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->register();

        foreach ($options as $option) {
            $prefix  = $option[static::OPTION_NAME_PREFIX];
            $baseDir = $option[static::OPTION_NAME_BASE_DIR];
            $prepend = $option[static::OPTION_NAME_PREPEND] ?? false;
            $this->addNamespace($prefix, $baseDir, $prepend);
        }
    }

    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * @param string $prefix
     * @param string $baseDir
     * @param bool   $prepend
     */
    public function addNamespace(string $prefix, string $baseDir, bool $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';

        $base_dir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';

        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = [];
        }

        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * @param string $class
     *
     * @return bool|string
     */
    public function loadClass(string $class)
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {

            $prefix = substr($class, 0, $pos + 1);

            $relativeClass = substr($class, $pos + 1);

            $mappedFile = $this->loadMappedFile($prefix, $relativeClass);
            if ($mappedFile) {
                return $mappedFile;
            }

            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }

    /**
     * @param string $prefix
     * @param string $relativeClass
     *
     * @return bool|string
     */
    protected function loadMappedFile(string $prefix, string $relativeClass)
    {
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        foreach ($this->prefixes[$prefix] as $baseDir) {

            $file = $baseDir
                . str_replace('\\', '/', $relativeClass)
                . '.php';

            if ($this->requireFile($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    protected function requireFile(string $file)
    {
        if (file_exists($file)) {
            require $file;

            return true;
        }

        return false;
    }
}
