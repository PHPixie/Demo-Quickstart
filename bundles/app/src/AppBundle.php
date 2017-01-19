<?php

namespace Project\App;
use PHPixie\DefaultBundle;
use PHPixie\BundleFramework\Builder as FrameworkBuilder;

/**
 * Default application bundle
 */
class AppBundle extends DefaultBundle
{
    /**
     * Build bundle builder
     * @param FrameworkBuilder $frameworkBuilder
     * @return AppBuilder
     */
    protected function buildBuilder($frameworkBuilder)
    {
        return new AppBuilder($frameworkBuilder);
    }
}