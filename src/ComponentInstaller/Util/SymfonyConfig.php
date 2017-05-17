<?php

namespace ComponentInstaller\Util;

use Composer\Composer;

class SymfonyConfig
{
    /**
     * @var SymfonyConfig
     */
    private static $instance;

    /**
     * Composer configuration
     * @var \Composer\Config
     */
    private $config;

    private $extra;

    /**
     * The temporary download directory
     * @var string
     */
    private $vendorDir;

    /**
     * The source directory for components
     * @var string
     */
    private $componentDir;

    private function __construct(Composer $composer)
    {
        $this->config = $composer->getConfig();
        $this->extra = $composer->getPackage()->getExtra();
        $this->setComponentDir();
        $this->setVendorDir();
    }

    public static function getInstance(Composer $composer)
    {
        if (self::$instance == null) {
            self::$instance = new SymfonyConfig($composer);
        }
        return self::$instance;
    }

    private function setComponentDir()
    {
        $componentDir = 'components';
        if($this->config->has('component-dir')) {
            $componentDir = $this->config->get('component-dir');
        }
        if (isset($this->extra["symfony-web-dir"])) {
            $componentDir = $this->extra["symfony-web-dir"].DIRECTORY_SEPARATOR."components";
        }
       $this->componentDir = $componentDir;
    }

    private function setVendorDir()
    {
        $useBaseVendorDir = $this->config->get("component-use-base-vendor-dir");
        if (isset($useBaseVendorDir) && $useBaseVendorDir) {
            $this->vendorDir = rtrim($this->config->get('vendor-dir'), '/');
        } else {
            $this->vendorDir = rtrim(dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR."tmp", '/');
        }
    }

    /**
     * @return bool
     */
    public function isDeleting()
    {
        return $this->config->has('component-remove-tmp') ? $this->config->get('component-remove-tmp') : true;
    }

    /**
     * @return bool
     */
    public function isFullyBuilt()
    {
        return $this->config->has('component-full-build') ? $this->config->get('component-full-build') : false;
    }

    /**
     * @return bool
     */
    public function getFileRegex()
    {
        return $this->config->has('component-file-regex') ? $this->config->get('component-file-regex') : '#^((?!(slim|map)).)*\.min\.(js|css)$#';
    }

    /**
     * @return string
     */
    public function getVendorDir()
    {
        return $this->vendorDir;
    }

    /**
     * @return string
     */
    public function getComponentDir()
    {
        return $this->componentDir;
    }
}
