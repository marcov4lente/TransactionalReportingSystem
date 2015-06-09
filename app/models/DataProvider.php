<?php

namespace GenericCorp\TransactionalReportingSystem;

use PDO;
use DateTime;
use GenericCorp\TransactionalReportingSystem\CurrencyService;

/**
 * Class DataProvider
 * @package GenericCorp\TransactionalReportingSystem
 */
class DataProvider
{

    private $records = array();
    private $pdo = null;
    private $databaseName = 'transactions.sqlite';

    function __construct()
    {
        $pdo = new PDO('sqlite:'.$this->databaseName);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }

    /**
    * Fetch data from SQLITE database that satisfy the supplied
    * conditions. Note this is a chained method.
    *
    * @param String $dateWildCard, Int $merchant
    * @return Object
    */
    public function getTransactionalData($dateWildCard,$merchant)
    {

        $queryFieldData[':merchant'] = $merchant;
        $queryFieldData[':date'] = $dateWildCard.'%';

        $query = $this->pdo
            ->prepare(" SELECT * FROM transactions WHERE merchant = :merchant AND date LIKE :date ");
        $query->execute($queryFieldData);
        $records = $query->fetchAll(PDO::FETCH_ASSOC);

        $this->records = $records;
        return $this;

    }

    /**
    * Add the heading array to the beginning of the result set,
    * so that it may printed out as well. Note this is a chained method.
    *
    * @param null
    * @return Object
    */
    public function addHeadings()
    {
        $processedRecords= array();

        foreach($this->records as $row) {
            foreach($row as $cellTitle => $cell) {
                $processedRecords['heading'][$cellTitle] = $cellTitle;
            }
            break;
        }

        $processedRecords = array_merge($processedRecords,$this->records);
        $this->records = $processedRecords;
        return $this;
    }

    /**
    * Convert the amounts, contained in the amounts column to the,
    * currency specified. Note this is a chained method.
    *
    * @param String $to
    * @return Object
    */
    public function convert($to)
    {

        $processedRecords = array();
        $currencyService = new currencyService;

        foreach($this->records as $record) {

            if(!is_numeric($record['amount'])) {
                $processedRecords[] = $record;
                continue;
            }

            $record['amount'] = $currencyService->convert($record['currency'],$to,$record['amount']);
            $processedRecords[] = $record;

        }

        $this->records = $processedRecords;
        return $this;
    }

    /**
    * Format results to the daily format
    *
    * @param null
    * @return Object
    */
    public function byDay()
    {
        return $this;
    }

    /**
    * Format results to the monthly format
    *
    * @param null
    * @return Object
    */
    public function byMonth()
    {
        $processedRecords = array();

        foreach($this->records as $row => $record) {

            if(!is_numeric($record['amount'])) {
                $processedRecords[$row] = $record;
                continue;
            }

            $date = new DateTime($record['date']);
            if(!isset($processedRecords[$date->format('Y-m')])) {
                $processedRecords[$date->format('Y-m')] = $record;
                $processedRecords[$date->format('Y-m')]['date'] = $date->format('Y-m');
            } else {
                $processedRecords[$date->format('Y-m')]['amount'] = $processedRecords[$date->format('Y-m')]['amount'] + $record['amount'];
            }
        }

        $this->records = $processedRecords;
        return $this;
    }

    /**
    * Format results to the yearly format
    *
    * @param null
    * @return Object
    */
    public function byYear()
    {
        $processedRecords = array();

        foreach($this->records as $row => $record) {

            if(!is_numeric($record['amount'])) {
                $processedRecords[$row] = $record;
                continue;
            }

            $date = new DateTime($record['date']);
            if(!isset($processedRecords[$date->format('Y')])) {
                $processedRecords[$date->format('Y')] = $record;
                $processedRecords[$date->format('Y')]['date'] = $date->format('Y');
            } else {
                $processedRecords[$date->format('Y')]['amount'] = $processedRecords[$date->format('Y')]['amount'] + $record['amount'];
            }
        }

        $this->records = $processedRecords;
        return $this;
    }

    /**
    * Get and return results
    *
    * @param Array $fieldsToReturn
    * @return Array
    */
    public function get($fieldsToReturn = array())
    {
        $processedRecords = array();

        foreach($this->records as $rowNumber => $row) {

            foreach($row as $cellName => $cell) {
                if(in_array($cellName,$fieldsToReturn)) {
                    $processedRecords[$rowNumber][$cellName] = $cell;
                }

            }

        }

        $this->records = $processedRecords;
        return $this->records;
    }


}
