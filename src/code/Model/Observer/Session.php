<?php

class MageProfis_Spam_Model_Observer_Session extends Mage_Core_Model_Abstract
{
    public function simpleCheck($observer)
    {
        try {
            $router = Mage::app()->getRequest()->getRouteName();

            if ($router != 'newsletter' && !Mage::getSingleton('core/session')->getMageProfisSpamSimpleCheck())
            {
                Mage::getSingleton('core/session')->setMageProfisSpamSimpleCheck(true);
            }
        } catch (Exception $ex) {
            Mage::log('Cant set simple session check...: ' . $ex->getMessage(), null, 'mageprofis_spam.log', true);
        }
    }

}
