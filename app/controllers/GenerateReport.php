<?php namespace

GenericCorp\TransactionalReportingSystem;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use GenericCorp\TransactionalReportingSystem\DataProvider;
use GenericCorp\TransactionalReportingSystem\ASCITable;

use Mandrill;


/**
 * Class GenerateReport
 * @package GenericCorp\TransactionalReportingSystem
 */
class GenerateReport extends command
{
    use \GenericCorp\TransactionalReportingSystem\ConfigurationManagement;
    use \GenericCorp\TransactionalReportingSystem\LoggingManagement;
    use \GenericCorp\TransactionalReportingSystem\ValidationManagement;

    private $output = null;
    private $input = null;

    /**
    * Define command properties.
    *
    * @return null
    */
    protected function configure()
    {
        $this
            ->setName('reports:generate')
            ->setDescription('Generate a detailed transaction report for a specific user, for a specified time interval and type.')
            ->addOption(
                'merchant',
                'm',
                InputOption::VALUE_REQUIRED,
                'Specify the user ID to generate the analysis report against.'
            )
            ->addOption(
                'date',
                'd',
                InputOption::VALUE_REQUIRED,
                'Specify the day, month or year for which the system is to generate the report for, accepted format: daily =  yyyy-mm-dd, monthly = yyyy-mm, yearly = yyyy.'
            )
            ->addOption(
                'interval',
                'i',
                InputOption::VALUE_REQUIRED,
                'Report interval type: d = daily, m = monthly, y = yearly.'
            )
            ->addOption(
                'currency',
                'c',
                InputOption::VALUE_REQUIRED,
                'Specify the currency code for which the system is to output the generated report in, format: GBP.'
            );

        // Initialise configuration
        $this->initialiseConfiguration();

        // Initialise loggers
        $infoLogFile = $this->getConfigValue('infoLogger');
        $errorLogFile = $this->getConfigValue('errorLogger');

        $this->initialiseLogger($infoLogFile,$errorLogFile);

    }


    /**
    * execute the command, and print out the data.
    *
    * @param Object $input, Object $output
    * @return null
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        // display
        $this->event('Starting the report generator...','info');

        // process input data
        $date = $input->getOption('date');
        $merchant = $input->getOption('merchant');
        $currency = $input->getOption('currency');
        $interval = $input->getOption('interval');

        // input validation
        if(!$this->validateMerchantInput($merchant)) {
            $this->event('Invalid merchant id supplied. Exiting!','error');
            return;
        }

        if(!$this->dateIntervalInput($date,$interval)) {
            $this->event('Invalid date and interval combination supplied. Exiting!','error');
            return;
        }

        if(!$this->validateCurrencyCodeFormat($currency,$this->getConfigValue('supportedCurrencies'))) {
            $this->event('Invalid currency code supplied. Exiting!','error');
            return;
        }

        // decipher interval method
        if($interval == 'd') {
            $intervalMethod = 'byDay';
        }

        if($interval == 'm') {
            $intervalMethod = 'byMonth';
        }

        if($interval == 'y') {
            $intervalMethod = 'byYear';
        }

        // display
        $this->event('Compiling data, and converting all amounts to '.$currency.'...','info');

        // fetch data
        $DataProvider = new DataProvider;
        $transactionalData = $DataProvider
            ->getTransactionalData($date,$merchant)
            ->addHeadings()
            ->convert($currency)
            ->$intervalMethod()
            ->get(array('merchant','date','amount'));


        // ASCI table builder
        $ASCITable = new ASCITable($transactionalData);

        // display
        $this->event( $ASCITable->outputHr());

        // output rows
        foreach($transactionalData as $row) {

            $column = 0;
            $rowOutput = '';

            foreach($row as $cellData) {
                $rowOutput .= $ASCITable->outputCell($cellData,$column);
                $column++;
            }

            $this->event( $rowOutput );

            if(!isset($headerPrinted)) {
                $this->event( $ASCITable->outputHr());
                $headerPrinted = true;
            }

        }

        // display
        $this->event( $ASCITable->outputHr());
        $this->event('Report generated succesfully, '.(count($transactionalData)-1).' records found. have a lovely day!', 'info');

    }


}
