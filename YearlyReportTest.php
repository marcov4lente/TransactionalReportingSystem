<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use GenericCorp\TransactionalReportingSystem\GenerateReport;

class YearlyReportTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new GenerateReport());

        $command = $application->find('reports:generate');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--merchant' => '1',
            '--date' => '2010',
            '--interval' => 'y',
            '--currency' => 'GBP',
        ));

        $this->assertContains('Report generated succesfully', $commandTester->getDisplay(), '', true);

    }
}
