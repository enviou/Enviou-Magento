<?php

class AndrewMarques_Enviou_ProductsController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);

        $store_id = Mage::app()->getStore()->getId();
        $token = Mage::helper('andrewmarquesenviou')->getToken($store_id);

        if ($token != '') {
            $_products = [];
            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addStoreFilter($store_id);

            foreach($products as $_product){
                $categoryName = '';
                $categories = $_product->getCategoryIds();
                if(count($categories) >= 1){
                    $categoryId = $categories[0];
                    $category = Mage::getModel('catalog/category')->load($categoryId);
                    $categoryName = $category->getName();
                }

                if(!isset($_products[ $_product->getId() ])){
                    $_products[ $_product->getId() ] = [];
                }
                $_products[ $_product->getId() ]['id'] = $_product->getId();
                $_products[ $_product->getId() ]['titulo'] = $_product->getName();
                $_products[ $_product->getId() ]['url'] = $_product->getProductUrl();
                $_products[ $_product->getId() ]['valor_de'] = 'R$ ' . number_format($_product->getPrice(),2,',','.');
                $_products[ $_product->getId() ]['valor_atual'] = 'R$ ' . number_format($_product->getFinalPrice(),2,',','.');
                $_products[ $_product->getId() ]['data_modificacao'] = $_product->getUpdatedAt();
                $_products[ $_product->getId() ]['imagem'] = $_product->getImageUrl();
                $_products[ $_product->getId() ]['categorias'] = $categoryName;
            }

            $jsonArray = array(
                'success' => true,
                'products' => $_products
            );

            $this->getResponse()->setBody(json_encode($jsonArray));
        }else{
            $jsonArray = array(
                'error' => true,
                'message' => 'Loja não instalada',
            );
            $this->getResponse()->setBody(json_encode($jsonArray));
        }
    }
}
