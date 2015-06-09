<?php

namespace GenericCorp\TransactionalReportingSystem;

/**
 * Class CurrencyService
 * @package GenericCorp\TransactionalReportingSystem
 */
class CurrencyService
{

    private $currencies = array('GBP','EUR','USD');
    private $baseCurrency = 'GBP';
    private $conversionMultilplier = array();

    /**
    * Initialise service, by fetching the rate conversion multipliers
    * from the various third party providers.
    *
    * @param null
    * @return null
    */
    function __construct()
    {
        $this->pretendCurrencyApiToThirdPartyProvider();
    }

    /**
    * Convert a supplied values from the supplied currency
    * to the target currency.
    *
    * @param String $to, String $from, Float $amount
    * @return Float
    */
    public function convert($from,$to,$amount)
    {

        // not a supported currency code
        if(!in_array($from,$this->currencies)) {
            return $amount;
        }

        // convert to base
        $mulitplierToBase = $this->conversionMultilplier[$from];
        $baseValue = $amount * $mulitplierToBase;

        // convert to target
        $mulitplierToTarget = $this->conversionMultilplier[$to];
        $convertedValue = $baseValue * $mulitplierToTarget;

        $convertedValue = number_format($convertedValue, 2,'.','');
        return $convertedValue;

    }

    /**
    * The API connector method that connect to a pretend service
    *
    * @param null
    * @return null
    */
    public function pretendCurrencyApiToThirdPartyProvider()
    {
        $conversionMultilplier = array();

        foreach($this->currencies as $currency) {
            $conversionMultilplier[$currency] = (rand(1,10)/10);
        }

        $this->conversionMultilplier = $conversionMultilplier;
    }

}
