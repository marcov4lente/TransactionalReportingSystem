<?php

namespace GenericCorp\TransactionalReportingSystem;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Trait ValidationManagement
 * @package GenericCorp\TransactionalReportingSystem
 */
trait ValidationManagement
{

    /**
    * Validate merchat ID.
    *
    * @param Int $merchant
    * @return Boolean
    */
    protected function validateMerchantInput($merchant)
    {

        $merchant = intval($merchant);

        if($merchant == 0) {
            return false;
        }

        return true;
    }

    /**
    * Validate the date and interval option combination.
    *
    * @param String $date, String $interval
    * @return Boolean
    */
    protected function dateIntervalInput($date,$interval)
    {
        if($interval != 'd' && $interval != 'm'  && $interval != 'y' ) {
            return false;
        }

        // if interval is daily, confirm date format
        if($interval == 'd') {
            $date = explode('-',$date);

            if(count($date) != 3 ) {
                return false;
            }

            if(!is_numeric($date[0]) || !is_numeric($date[1]) || !is_numeric($date[2]) ) {

                return false;
            }

        }

        // if interval is monthly, confirm date format
        if($interval == 'm') {
            $date = explode('-',$date);

            if(count($date) != 2 ) {
                return false;
            }

            if(!is_numeric($date[0]) || !is_numeric($date[1]) ) {
                return false;
            }
        }

        // if interval is yearly, confirm date format
        if($interval == 'y') {
            $date = explode('-',$date);

            if(count($date) != 1 ) {
                return false;
            }

            if(!is_numeric($date[0]) ) {
                return false;
            }
        }

        return true;
    }

    /**
    * Validate that the currency code supplied is in a valid format, and is
    * indeed supported.
    *
    * @param String $currency, Array $supportedCurrencies
    * @return Boolean
    */
    protected function validateCurrencyCodeFormat($currency,$supportedCurrencies)
    {

        if(!in_array($currency,$supportedCurrencies)) {
            return false;
        }

        return true;
    }


}
