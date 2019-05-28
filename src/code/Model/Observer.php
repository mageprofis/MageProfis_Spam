<?php

class MageProfis_Spam_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * 
     * @param Varien_Event_Observer $event
     */
    public function penalty($event)
    {
        if($this->_generalCheck())
        {
            $this->throw403();
        }

        $ip = Mage::helper('core/http')->getRemoteAddr(false);
        if (Mage::helper('mpspam')->isPenalty($ip))
        {
            $this->throw403();
        }

        if (Mage::helper('mpspam')->isOnXbl($ip))
        {
            // set an higher result
            Mage::helper('mpspam')->setPenaltyRequest($ip, 99);
            $this->throw403();
        }
        
        if($this->_sessionCheck())
        {
            $this->throw403();
        }

        Mage::helper('mpspam')->setPenaltyRequest($ip);
    }

    public function cron()
    {
        Mage::helper('mpspam')->removeOld();
    }

/**
     * check on contact page
     * 
     * @return void
     */
    public function controllerActionPredispatchContactsIndexPost($observer)
    {
        if($this->_generalCheck())
        {
            $this->throw403();
        }

        $ip = Mage::helper('core/http')->getRemoteAddr(false);
        if (Mage::helper('mpspam')->isPenalty($ip))
        {
            $this->throw403();
        }

        // check on Name values
        $checkNames = array(
            'name', // @ default mostly only this field
            'firstname',
            'middlename',
            'lastname',
            'telephone' // @ default mostly only this field
        );
        foreach ($checkNames as $_name)
        {
            $value = Mage::app()->getRequest()->getParam($_name);
            if (!empty($value) && (stristr($value, 'http://') || stristr($value, 'https://')))
            {
                $this->throw403();
            }
        }

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

        if (Mage::helper('mpspam')->isOnXbl($ip))
        {
            // set an higher result
            Mage::helper('mpspam')->setPenaltyRequest($ip, 99);
            $this->throw403();
        }
        Mage::helper('mpspam')->setPenaltyRequest($ip);
    }

    /**
     * check on customer register
     * 
     * @return void
     */
    public function controllerActionPredispatchCustomerAccountCreatepost($observer)
    {
        if($this->_generalCheck())
        {
            $this->throw403();
        }

        $ip = Mage::helper('core/http')->getRemoteAddr(false);
        if (Mage::helper('mpspam')->isPenalty($ip))
        {
            $this->throw403();
        }

        // check on Name values
        $checkNames = array(
            'firstname',
            'middlename',
            'lastname'
        );
        foreach ($checkNames as $_name)
        {
            $value = Mage::app()->getRequest()->getParam($_name);
            if (!empty($value) && (stristr($value, 'http://') || stristr($value, 'https://')))
            {
                $this->throw403();
            }
        }

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
        Mage::helper('mpspam')->setPenaltyRequest($ip);
    }

    /**
     * check on customer register
     * 
     * @return void
     */
    public function controllerActionPredispatchProductReviewCreatepost($observer)
    {
        if($this->_generalCheck())
        {
            $this->throw403();
        }

        $ip = Mage::helper('core/http')->getRemoteAddr(false);
        if (Mage::helper('mpspam')->isPenalty($ip))
        {
            $this->throw403();
        }

        // check on Name values
        $checkNames = array(
            'nickname',
            'title'
        );
        foreach ($checkNames as $_name)
        {
            $value = Mage::app()->getRequest()->getParam($_name);
            if (!empty($value) && (stristr($value, 'http://') || stristr($value, 'https://')))
            {
                $this->throw403();
            }
        }

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
        Mage::helper('mpspam')->setPenaltyRequest($ip);
    }

    /**
     * check search result
     * 
     * @return void
     */
    public function controllerActionPredispatchCatalogsearchResultIndex($observer)
    {
        $query = Mage::app()->getRequest()->getParam('q', null);
        $query = mb_strtolower($query, 'UTF-8'); // everything to lower
        $query = preg_replace('!\s+!', ' ', $query); // may double spaces!
        if (strstr($query, 'union select'))
        {
            $this->throw403();
        }
        if (strstr($query, 'unhex(hex(version()))'))
        {
            $this->throw403();
        }
    }

    /**
     * simple check on current request
     * 
     * @return bool
     */
    protected function _generalCheck()
    {
        // some perl scripts use 1.0 as default
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0')
        {
            return true;
        }
        // is the UA is empty, its not an customer
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        if ((!isset($_SERVER['HTTP_USER_AGENT']) || empty($ua)))
        {
            return true;
        }
        // most spammers used "//" in some cases :)
        $url = str_replace(array('https://', 'http://'), '', Mage::helper('core/url')->getCurrentUrl());
        if (strstr($url, '//'))
        {
            return true;
        }
        // check general ua list
        if (isset($_SERVER['HTTP_USER_AGENT']) && !Mage::helper('mpspam')->checkUserAgent($_SERVER['HTTP_USER_AGENT']))
        {
            return true;
        }
        return false;
    }
    
    protected function _sessionCheck()
    {
        $session = Mage::getSingleton('core/session');
        if(!$session->getMageProfisSpamSimpleCheck())
        {
            return true;
        }
        if(!$session->getMageProfisSpamAjaxCheck())
        {
            return true;
        }
        return false;
    }

    /**
     * throw an simple 403
     *   so other server tools may be able to block them fast
     *   like fail2ban
     * 
     * @return void
     */
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
