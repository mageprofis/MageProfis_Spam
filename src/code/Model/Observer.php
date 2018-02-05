<?php

class MageProfis_Spam_Model_Observer extends Mage_Core_Model_Abstract
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
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        if (!$penalty && (!isset($_SERVER['HTTP_USER_AGENT']) || empty($ua)))
        {
            $penalty = true;
        }
        if (!$penalty && isset($_SERVER['HTTP_USER_AGENT']) && !Mage::helper('mpspam')->checkUserAgent($_SERVER['HTTP_USER_AGENT']))
        {
            $penalty = true;
        }
        $ip = Mage::helper('core/http')->getRemoteAddr(false);
        if (!$penalty && Mage::helper('mpspam')->isPenalty($ip))
        {
            $penalty = true;
        }

        if (!$penalty && Mage::helper('mpspam')->isOnXbl($ip))
        {
            $penalty = true;
            // set an higher result
            Mage::helper('mpspam')->setPenaltyRequest($ip, 99);
        }

        if ($penalty)
        {
            $this->throw403();
        }
        Mage::helper('mpspam')->setPenaltyRequest($ip);
    }

    public function cron()
    {
        Mage::helper('mpspam')->removeOld();
    }

    public function controllerActionPredispatchCustomerAccountCreatepost($observer)
    {
        $penalty = false;
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0')
        {
            $penalty = true;
        }
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        if (!$penalty && (!isset($_SERVER['HTTP_USER_AGENT']) || empty($ua)))
        {
            $penalty = true;
        }
        if (!$penalty && isset($_SERVER['HTTP_USER_AGENT']) && !Mage::helper('mpspam')->checkUserAgent($_SERVER['HTTP_USER_AGENT']))
        {
            $penalty = true;
        }
        
        $checkNames = array(
            'firstname',
            'middlename',
            'lastname'
        );
        foreach ($checkNames as $_name)
        {
            if ($penalty)
            {
                break;
            }
            $value = Mage::app()->getRequest()->getParam($_name);
            if (!empty($value) && (stristr($value, 'https://') || stristr($value, 'https://')))
            {
                $penalty = true;
            }
        }

        if (!$penalty)
        {
            $mps_id = Mage::app()->getRequest()->getParam('mps_id');
            $split = explode("O", $mps_id);
            
            if(!$mps_id)
            {
                $this->throw403();
            }
            
            if(!is_array($split) || count($split)!=2)
            {
                $this->throw403();
            }
            
            if($split[1]!=Mage::helper('mpspam')->getNumberOfTheDay())
            {
                $this->throw403();
            }
        } else {
            $this->throw403();
        }
    }

    public function throw403($with_exit = true)
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
        echo '<script type="text/javascript">window.location.href = "' .
        Mage::getUrl('') . '"</script>';
        if($with_exit) exit;
    }

}
