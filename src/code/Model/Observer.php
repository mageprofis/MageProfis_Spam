<?php

class MageProfis_Spam_Model_Observer
extends Mage_Core_Model_Abstract
{
    /**
     * 
     * @param Varien_Event_Observer $event
     */
    public function penalty($event)
    {
        $penalty = false;
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0')
        {
            $penalty = true;
        }
        $ip = Mage::helper('core/http')->getRemoteAddr(false);
        if(Mage::helper('mpspam')->isPenalty($ip))
        {
            $penalty = true;
        }

        if ($penalty)
        {
            $proto = 'HTTP/1.0';
            if (isset($_SERVER['SERVER_PROTOCOL']))
            {
                $proto = $_SERVER['SERVER_PROTOCOL'];
            }
            header($proto . ' 403 Forbidden');
            $message = Mage::helper('mpspam')->__('Your request is not allowed');
            echo $message;
            Mage::getSingleton('core/session')->addError($message);
            echo '<script type="text/javascript">window.location.href = "'.
                    Mage::getUrl('').'"</script>';
            exit;
        }
        Mage::helper('mpspam')->setPenaltyRequest($ip);
    }
    
    public function cron()
    {
        Mage::helper('mpspam')->removeOld();
    }
}