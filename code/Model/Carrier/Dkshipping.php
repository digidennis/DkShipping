<?php


class Digidennis_Dkshipping_Model_Carrier_Dkshipping extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'dkshipping';
    const POSTNORD_PRICE_MAX5 = 60;
    const POSTNORD_PRICE_MAX10 = 90;
    const POSTNORD_PRICE_MAX20 = 150;
    const POSTNORD_WEIGHT_MAX = 20;
    const POSTNORD_PRICE_ATHOME = 20;

    public function collectRates( Mage_Shipping_Model_Rate_Request $request )
    {
        /* @var $result Mage_Shipping_Model_Rate_Result */
        $result = Mage::getModel('shipping/rate_result');

        if ($request->getFreeShipping()) {
            $freeShippingRate = $this->getFreeShippingRate();
            return $result->append($freeShippingRate);
        }

        $result->append($this->getPickupTaastrup());
        $result->append($this->getPickupGanloese());
        $result->append($this->getStandardShippingRate($request));
        return $result;
    }

    protected function getPickupTaastrup()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle('Afhentning, Tåstrup');
        $rate->setMethod('pickup_taastrup');
        $rate->setMethodTitle('Afhentning, Tåstrup');
        $rate->setMethodDescription('Hverdage 10:00-16:00');
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }

    protected function getPickupGanloese()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle('Afhentning, Ganløse');
        $rate->setMethod('pickup_ganloese');
        $rate->setMethodTitle('Afhentning, Ganløse');
        $rate->setMethodDescription('man-tor 8:00-15:00, fre 8:00-12:00');
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }
    protected function getStandardShippingRate(Mage_Shipping_Model_Rate_Request $request)
    {
        /**
         * Fields:
         * - carrier: ups
         * - carrierTitle: United Parcel Service
         * - method: 2day
         * - methodTitle: UPS 2nd Day Priority
         * - price: $9.40 (cost+handling)
         * - cost: $8.00
         */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        /**
         * getConfigData(config_key) returns the configuration value for the
         * carriers/[carrier_code]/[config_key]
         */
        $rate->setCarrierTitle('PostNord Home');

        $parcelprice = $this->getConfigData('postnordprice');
        $parcelmaxweight = floatval($this->getConfigData('postnordmaxweight'));
        $parcelcount = ceil($request->getPackageWeight() / $parcelmaxweight);
        $rate->setMethod('postnord');
        $rate->setMethodTitle('PostNord Home');
        $rate->setMethodDescription('Levering med Post Nord Pakkepost');
        $rate->setPrice($parcelprice*$parcelcount);
        $rate->setCost($parcelprice*$parcelcount);

        return $rate;
    }

    protected function getFreeShippingRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('free_shipping');
        $rate->setMethodTitle('Free Shipping (3 - 5 days)');
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }

    public function getAllowedMethods()
    {
        return array(
            'postnord' => 'PostNord Home',
            'pickup_taastrup' => 'Afhentning, Tåstrup',
            'pickup_ganloese' => 'Afhentning, Ganløse',
            'free_shipping' => 'Gratis Levering',
        );
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    public function getTrackingInfo($tracking)
    {
        $track = Mage::getModel('shipping/tracking_result_status');
        $track->setUrl('https://www.postnord.dk/track-trace#dynamicloading=true&shipmentid=' . $tracking)
            ->setTracking($tracking)
            ->setCarrierTitle('Postnord');
        return $track;
    }
}