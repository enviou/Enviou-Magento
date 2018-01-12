<?php

class AndrewMarques_Enviou_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getToken($store_id)
    {
        return Mage::getStoreConfig('andrewmarquesenviou/configuracoes/token', $store_id);
    }

}
