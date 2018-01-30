<?php

class Digidennis_DkShipping_Block_Pickupinfo extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('digidennis/dkshipping/pickupinfo.phtml');
    }

    public function getAddress()
    {
        if( $this->getOrder()->getShippingMethod() == 'dkshipping_pickup_taastrup' )
        {
            return "Roskildvej 332A<br/>2630 Taastrup<br/>Hverdage 10:00-16:00";
        }
        elseif ( $this->getOrder()->getShippingMethod() == 'dkshipping_pickup_ganloese' )
        {
            return "Ringbakken 14<br/>3660 Stenløse<br/>man-tor 8:00-15:00<br/>fre 8:00-12:00";
        }
        return '';
    }

    public function hasPickup()
    {
        if( $this->getOrder()->getShippingMethod() == 'dkshipping_pickup_taastrup' ||
            $this->getOrder()->getShippingMethod() == 'dkshipping_pickup_ganloese' )
            return true;
        return false;
    }
}