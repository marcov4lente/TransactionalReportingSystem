#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use GenericCorp\TransactionalReportingSystem\GenerateReport;

$application = new Application();
$application->add(new GenerateReport);
$application->run();
