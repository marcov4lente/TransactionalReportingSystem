<?php

namespace GenericCorp\TransactionalReportingSystem;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Trait LoggingManagement
 * @package GenericCorp\TransactionalReportingSystem
 */
trait LoggingManagement
{

    private $infoLogger = null;
    private $errorLogger = null;

    /**
    * Initialise the INFO and ERROR logging system
    *
    * @param String $infoLogFile, String $errorLogFile
    * @return null
    */
    protected function initialiseLogger($infoLogFile,$errorLogFile)
    {
        // instantiate info loggers
        $infoLogger = new Logger('noormal');
        $infoLogger->pushHandler(new StreamHandler($infoLogFile, Logger::INFO));

        $this->infoLogger = $infoLogger;

        // instantiate error loggers
        $errorLogger = new Logger('error');
        $errorLogger->pushHandler(new StreamHandler($errorLogFile, Logger::ERROR));

        $this->errorLogger = $errorLogger;
    }

    /**
    * Print and log event.
    *
    * @param String $message, String $type
    * @return null
    */
    protected function event($message, $type = 'default')
    {

        switch($type) {
            case 'default';
                $this->output->writeln($message);
                break;

            case 'info';
                $this->output->writeln('<info>INFO: '.$message.'</info>');
                $this->infoLogger->addInfo($message);
                break;

            case 'error';
                $this->output->writeln('<error>ERROR: '.$message.'</error>');
                $this->errorLogger->addError($message);
                break;
        }
    }

}
