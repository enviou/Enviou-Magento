<?php

class AndrewMarques_Enviou_Model_Observer
{
    public function updateConfig()
    {
        $store_id = Mage::app()->getStore()->getId();

        $configuracoes = Mage::getStoreConfig('andrewmarquesenviou/configuracoes', $store_id);
        $fields = [];
        foreach ($configuracoes as $key => $value) {
            if($key == 'logotipo'){
                $value = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'enviou/' . $value;
                $key = 'url_logotipo';
            }
            $fields[$key] = $value;
        }

        $fields['secretToken'] = '101982fds44449a-Magento';
        $fields['url_loja'] = Mage::getUrl();
        $fields['url_xml'] = Mage::getUrl('enviou/products');

        $curl = curl_init();
        $url = 'https://api.enviou.com.br/plataforma/magento';

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => count($fields),
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $fields
        ]);

        if (!$resp = curl_exec($curl)) {
            Mage::log( 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl) );
            die('error!');
        }

        curl_close($curl);
    }

}
