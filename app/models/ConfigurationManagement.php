<?php

namespace GenericCorp\TransactionalReportingSystem;

/**
 * Trait ConfigurationManagement
 * @package GenericCorp\TransactionalReportingSystem
 */
trait ConfigurationManagement
{

    private $configuration = null;

    /**
    * Initialise the configuration data, contained in the config file.
    *
    * @param null
    * @return null
    */
    protected function initialiseConfiguration()
    {
        // check if preset exists
        if(!file_exists(__DIR__.'/../config/app.php')) {
            print 'Configuration file missing aborting now!!'."\n";
            exit;
        }

        // import the preset data
        $configuration = require(__DIR__.'/../config/app.php');
        $this->configuration = $configuration;
    }

    /**
    * Initialise the configuration data, contained in the config file.
    *
    * @param String $key
    * @return mixed
    */
    protected function getConfigValue($key)
    {
        return $this->configuration[$key];
    }
}
