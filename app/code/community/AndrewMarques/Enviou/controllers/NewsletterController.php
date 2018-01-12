<?php

class AndrewMarques_Enviou_NewsletterController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);

        $store_id = Mage::app()->getStore()->getId();
        $token = Mage::helper('andrewmarquesenviou')->getToken($store_id);

        if ($token != '' && $this->getRequest()->getHeader('token') == $token) {
            $_subscribers = ['subscribers' => []];
            $subscribers = Mage::getModel('newsletter/subscriber')
                ->getCollection()
                ->addFieldToFilter('store_id', $store_id);
            foreach ($subscribers as $subscriber) {
                if($customerId = $subscriber->getCustomerId() != '0'){
                    $customer =  Mage::getModel('customer/customer')->load($customerId);
                    if($customer && $customer->getName() != ''){
                        $_subscribers['subscribers'][$subscriber->getId()]['nome'] = $customer->getName();
                    }
                }

                $_subscribers['subscribers'][$subscriber->getId()]['email'] = $subscriber->getEmail();
            }

            return $this->getResponse()->setBody(json_encode($_subscribers));
        }else{
            $jsonArray = array(
                'error' => true,
                'message' => 'not authorized',
            );
            $this->getResponse()->setBody(json_encode($jsonArray));
        }
    }
}
