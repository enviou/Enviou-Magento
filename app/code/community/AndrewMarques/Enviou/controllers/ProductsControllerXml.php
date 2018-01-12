<?php

class AndrewMarques_Enviou_ProductsController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);

        $store_id = Mage::app()->getStore()->getId();
        $token = Mage::helper('andrewmarquesenviou')->getToken($store_id);

        if ($token != '') {
            $file = Mage::getBaseDir('media') . '/enviou/products.xml';
            if(file_exists($file)){ unlink($file); }

            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addStoreFilter($store_id);

            $doc = new DOMDocument();
            $doc->formatOutput = true;

            $enviou = $doc->createElement('enviou');
            $doc->appendChild( $enviou );

            $productsX = $doc->createElement('produtos');
            $enviou->appendChild( $productsX );

            foreach($products as $_product){
                $product = $doc->createElement('produto');

                    // id
                    $id = $doc->createElement('id');
                    $id->appendChild(
                        $doc->createTextNode($_product->getId())
                    );
                    $product->appendChild( $id );

                    // titulo
                    $titulo = $doc->createElement('titulo');
                    $titulo->appendChild(
                        $doc->createTextNode($_product->getName())
                    );
                    $product->appendChild( $titulo );

                    // url
                    $url = $doc->createElement('url');
                    $url->appendChild(
                        $doc->createTextNode($_product->getProductUrl())
                    );
                    $product->appendChild( $url );

                    // valor_de
                    $valor_de = $doc->createElement('valor_de');
                    $valor_de->appendChild(
                        $doc->createTextNode($_product->getPrice())
                    );
                    $product->appendChild( $valor_de );

                    // valor_atual
                    $valor_atual = $doc->createElement('valor_atual');
                    $valor_atual->appendChild(
                        $doc->createTextNode($_product->getFinalPrice())
                    );
                    $product->appendChild( $valor_atual );

                    // data_modificacao
                    $data_modificacao = $doc->createElement('data_modificacao');
                    $data_modificacao->appendChild(
                        $doc->createTextNode($_product->getUpdatedAt())
                    );
                    $product->appendChild( $data_modificacao );

                    // imagem
                    $imagem = $doc->createElement('imagem');
                    $imagem->appendChild(
                        $doc->createTextNode($_product->getImageUrl())
                    );
                    $product->appendChild( $imagem );

                    // categorias
                    $categoryName = '';
                    $categories = $_product->getCategoryIds();
                    if(count($categories) >= 1){
                        $categoryId = $categories[0];
                        $category = Mage::getModel('catalog/category')->load($categoryId);
                        $categoryName = $category->getName();
                    }

                    $categorias = $doc->createElement('categorias');
                    $categorias->appendChild(
                        $doc->createTextNode($categoryName)
                    );
                    $product->appendChild( $categorias );

                $productsX->appendChild($product);
            }


            try {
                file_put_contents($file,$doc->saveXML(),FILE_APPEND);
                $jsonArray = array(
                    'success' => true,
                    'message' => 'XML gerado com sucesso!',
                    'link' => Mage::getUrl() . 'media/enviou/products.xml'
                );
            } catch (Exception $e) {
                $jsonArray = array(
                    'success' => false,
                    'message' => 'Erro ao gerar o XML!'
                );
            }

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
