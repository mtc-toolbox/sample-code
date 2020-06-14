<?php

namespace system;

class Configuration
{
    const CONFIGURATION_FILENAME = '/../config/config';

    const LOCAL_NAME_POSTFIX = '-local';

    protected $config;

    public function __construct(array $config = null)
    {
        $this->config = $config ?? $this->loadConfig(dirname(__FILE__) . static::CONFIGURATION_FILENAME);
    }

    /**
     * @param $filename
     *
     * @return array
     */
    public function loadConfig($filename)
    {
        return array_merge($this->loadGlobalConfig($filename), $this->loadLocalConfig($filename));
    }


    /**
     * @param $filename
     *
     * @return array
     */
    public function loadGlobalConfig($filename)
    {
        $fullFilename = $filename . '.php';
        if (!file_exists($fullFilename)) {
            return [];
        }

        return include $fullFilename;
    }

    /**
     * @param $filename
     *
     * @return array
     */
    public function loadLocalConfig($filename)
    {
        $fullFilename = $filename . static::LOCAL_NAME_POSTFIX . '.php';
        if (!file_exists($fullFilename)) {
            return [];
        }

        return include $fullFilename;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config ?? [];
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param string $sectionName
     *
     * @return array
     */
    public function getSection(string $sectionName)
    {
        return $this->getConfig()[$sectionName] ?? [];
    }

    /**
     * @param string $sectionName
     * @param string $valueName
     *
     * @return mixed|string
     */
    public function getValue(string $sectionName, string $valueName)
    {
        $section = $this->getSection($sectionName);

        return $section[$valueName] ?? '';
    }
}
