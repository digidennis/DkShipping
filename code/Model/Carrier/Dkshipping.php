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
        $rate->setCarrierTitle('Afhentning');
        $rate->setMethod('pickup_taastrup');
        $rate->setMethodTitle('Tåstrup');
        $rate->setMethodDescription('Hverdage 10:00-16:00<br>Du får besked på email når din ordre er klar.');
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }

    protected function getPickupGanloese()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle('Afhentning');
        $rate->setMethod('pickup_ganloese');
        $rate->setMethodTitle('Ganløse');
        $rate->setMethodDescription('man-tor 8:00-15:00<br>fredag 8:00-12:00<br>Du får besked på email når din ordre er klar.');
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
        $weight = $request->getPackageWeight();
        //$parcelprice = $this->getConfigData('postnordprice');

        //ABOVE POST NORD MAX USE FREIGHTER
        if( $weight < $this::POSTNORD_WEIGHT_MAX)
        {
            //CHEAPEST
            $price = $this::POSTNORD_PRICE_MAX5;
            if( $weight > 5 )
            {
                $price = $this::POSTNORD_PRICE_MAX10;
                if( $weight > 10 )
                {
                    $price = $this::POSTNORD_PRICE_MAX20;
                }
            }
            $price += $this::POSTNORD_PRICE_ATHOME;
            $rate->setCarrierTitle('PostNord');
            $rate->setMethod('postnord');
            $rate->setMethodTitle('Home');
            $rate->setMethodDescription('Levering til døren med PostNord');
            $rate->setPrice($price);
            $rate->setCost($price);
        }
        else {
            $rate->setCarrierTitle('Fragtmand');
            $rate->setMethod('fragtmand');
            $rate->setMethodTitle('Til Kantsten');
            $rate->setMethodDescription('Levering til kantsten med fragtmand, i dagtimerne.');
            $rate->setPrice(450);
            $rate->setCost(450);
        }

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