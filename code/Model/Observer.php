<?php
/**
 * Created by PhpStorm.
 * User: W10
 * Date: 15-01-2018
 * Time: 14:57
 */

class Digidennis_DkShipping_Model_Observer
{
    public function salesOrderShipmentSaveBefore(Varien_Event_Observer $observer)
    {
        $event = $observer;
    }

    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {
        $event = $observer;
    }
}