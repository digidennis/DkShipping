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
            return "Roskildvej 332A<br/>2630 Tåstrup<br/><strong>Hverdage 10:00-16:00</strong>";
        }
        elseif ( $this->getOrder()->getShippingMethod() == 'dkshipping_pickup_ganloese' )
        {
            return "Ringbakken 14<br/>3660 Stenløse<br/><strong>man-tor 8:00-15:00<br/>fre 8:00-12:00</strong>";
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

    public function getNotSentShipmentsOnOrder($order)
    {
        if( !is_object($order) )
            $order = Mage::getModel('sales/order')->load($order);

        $notsentshipments = array();
        foreach($order->getShipmentsCollection() as $shipment)
        {
            if(!$shipment->getEmailSent())
                $notsentshipments[] = $shipment;
        }
        return $notsentshipments;
    }

    public function collectAllTracksOnOnder()
    {
        $tracks = array();
        foreach($this->getOrder()->getShipmentsCollection() as $shipment)
        {
            foreach ($shipment->getTracksCollection() as $track )
            {
                $tracks[] = $track;
            }
        }
        return $tracks;
    }

}