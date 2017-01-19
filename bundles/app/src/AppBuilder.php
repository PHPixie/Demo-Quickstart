<?php

namespace Project\App;

use \PHPixie\DefaultBundle\Builder;

/**
 * App bundle builder
 */
class AppBuilder extends Builder
{
    /**
     * Build Processor for HTTP requests
     * @return HTTP
     */
    protected function buildHttpProcessor()
    {
        return new HTTP($this);
    }

    /**
     * Build ORM Wrappers
     * @return ORM
     */
    protected function buildORMWrappers()
    {
        return new ORM($this);
    }

    /**
     * Build Console command registry
     * @return Console
     */
    protected function buildConsoleProvider()
    {
        return new Console($this);
    }

    /**
     * Get bundle root directory
     * @return string
     */
    protected function getRootDirectory()
    {
        return realpath(__DIR__.'/../');
    }

    /**
     * Get bundle name
     * @return string
     */
    public function bundleName()
    {
        return 'app';
    }
}