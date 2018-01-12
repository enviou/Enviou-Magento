<?php

class AndrewMarques_Enviou_CustomersController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);

        $store_id = Mage::app()->getStore()->getId();
        $token = Mage::helper('andrewmarquesenviou')->getToken($store_id);

        if ($token != '' && $this->getRequest()->getHeader('token') == $token) {
            $_customers = ['customers' => []];
            $customers = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('store_id', $store_id);
            foreach ($customers as $customer) {
                if(isset($_customers['customers'][$customer->getId()])){
                    $_customers['customers'][$customer->getId()] = [];
                }
                $_customers['customers'][$customer->getId()]['name'] = $customer->getName();
                $_customers['customers'][$customer->getId()]['email'] = $customer->getEmail();

                $gender = $customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender'));
                if($gender && $gender != ''){
                    $_customers['customers'][$customer->getId()]['gender'] = $gender;
                }

                if($customer->getDob() != ''){
                    $_customers['customers'][$customer->getId()]['dob'] = $customer->getDob();
                }

                if($address_id = $customer->getDefaultShipping()){
                    $address = Mage::getModel('customer/address')->load($address_id);

                    $street = $address->getStreet();
                    if(is_array($street) && count($street) >= 1){
                        $street = implode($street, ' - ');
                        $_customers['customers'][$customer->getId()]['street'] = $street;
                    }

                    if($address->getCity() != ''){
                        $_customers['customers'][$customer->getId()]['city'] = $address->getCity();
                    }

                    if($address->getRegion() != ''){
                        $_customers['customers'][$customer->getId()]['state'] = $address->getRegion();
                    }

                    if($address->getCountry() != ''){
                        $_customers['customers'][$customer->getId()]['country'] = $address->getCountry();
                    }
                }
            }

            return $this->getResponse()->setBody(json_encode($_customers));
        }else{
            $jsonArray = array(
                'error' => true,
                'message' => 'not authorized',
            );
            $this->getResponse()->setBody(json_encode($jsonArray));
        }
    }
}
