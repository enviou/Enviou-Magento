<?php
class AndrewMarques_Enviou_Block_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('enviou/adminhtml/instalar');

        $hasToken = Mage::getStoreConfig('andrewmarquesenviou/configuracoes/token');
        $label = $hasToken ? 'Loja instalada' : 'Instalar';
        $disable = $hasToken ? true : false;

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel($label)
            ->setOnClick("setLocation('$url')")
            ->setDisabled($disable)
            ->toHtml();

        return $html;
    }
}
