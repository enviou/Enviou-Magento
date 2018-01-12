<?php

class AndrewMarques_Enviou_Model_System_Config_Source_Idiomas {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'portugues', 'label' => Mage::helper('adminhtml')->__('Português')),
            array('value' => 'ingles', 'label' => Mage::helper('adminhtml')->__('Inglês')),
            array('value' => 'espanhol', 'label' => Mage::helper('adminhtml')->__('Espanhol'))
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'portugues' => Mage::helper('adminhtml')->__('Português'),
            'ingles' => Mage::helper('adminhtml')->__('Inglês'),
            'espanhol' => Mage::helper('adminhtml')->__('Espanhol')
        );
    }
}
