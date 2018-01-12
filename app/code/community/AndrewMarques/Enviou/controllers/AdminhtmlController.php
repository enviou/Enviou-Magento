<?php

class AndrewMarques_Enviou_AdminhtmlController extends Mage_Adminhtml_Controller_Action {

    public function instalarAction()
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
        $resp = json_decode($resp);
        if($resp->IsOK && $resp->Token != '' && $resp->UrlRedirectToLogin != ''){
            $link = $resp->UrlRedirectToLogin;

            Mage::getConfig()->saveConfig('andrewmarquesenviou/configuracoes/token', $resp->Token, 'default', $store_id);
            Mage::getConfig()->saveConfig('andrewmarquesenviou/configuracoes/codigo_js', $resp->HtmlJS, 'default', $store_id);

            Mage::getSingleton('adminhtml/session')->addSuccess('Loja instalada com sucesso! Você pode se logar no painel através do link: <a href="'. $link .'" target="_blank">'. $link .'</a>');
            return $this->_redirectReferer();
        }else{
            Mage::getSingleton('adminhtml/session')->addError('Erro ao cadastrar loja, verifique os campos informados! Error: <b>'. $resp->Code .'</b>');
            return $this->_redirectReferer();
        }
    }

    public function emailAction(){
        $token = Mage::helper('andrewmarquesenviou')->getToken(Mage::app()->getStore()->getId());
        if($token != ''){
            $this->_redirectUrl('https://enviou.com.br/login?token=' . $token);
        }else{
            $this->_redirect('adminhtml/system_config/edit/section/andrewmarquesenviou');
        }
    }

    public function carrinhoAction(){
        $token = Mage::helper('andrewmarquesenviou')->getToken(Mage::app()->getStore()->getId());
        if($token != ''){
            $this->_redirectUrl('https://enviou.com.br/login?token=' . $token . '&isCarrinho=True');
        }else{
            $this->_redirect('adminhtml/system_config/edit/section/andrewmarquesenviou');
        }
    }

}
