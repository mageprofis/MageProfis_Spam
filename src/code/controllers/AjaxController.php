<?php

class MageProfis_Spam_AjaxController extends Mage_Core_Controller_Front_Action
{
    function indexAction()
    {
        Mage::getSingleton('core/session')->setMageProfisSpamAjaxCheck(true);

        $result = array(
            'check' => 'success', 
        );
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setHeader('X-Robots-Tag', 'noindex, nofollow', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        return $this;
    }

}
