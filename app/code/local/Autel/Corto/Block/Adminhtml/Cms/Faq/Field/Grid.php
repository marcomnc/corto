<?php
class Autel_Corto_Block_Adminhtml_Cms_Faq_Field_Grid extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{    
    public function __construct()
    {           
        $this->addColumn('ask', array(
            'label' => Mage::helper('autelcorto')->__('Domanda'),
            'style' => 'width:auto',
            'type' => 'textarea',
            'class' => 'required-entry',
        ));
        
        $this->addColumn('response', array(
            'label' => Mage::helper('adminhtml')->__('Risposta'),
            'style' => 'width:auto',
            'type' => 'textarea',
            'class' => 'required-entry',
        ));
        
        $this->addColumn('order', array(
            'label' => Mage::helper('adminhtml')->__('Ordinamento'),
            'style' => 'width:50px',
        ));
        
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add FAQ');
        parent::__construct();
    }
    
    public function addColumn($name, $params) {
        parent::addColumn($name, $params);
        
        if (isset($params['type'])) {
            $this->_columns[$name]['type'] = $params['type'];
        }        
    }
    
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }

        if (isset($column['type']) && $column['type'] == 'textarea') {
            $html = '<textarea rows="2" cols="50"';
        } else {
            $html = '<input type="text" ';
        }
        $html.= ' name="' . $inputName;
        if (!isset($column['type']) || $column['type'] != 'textarea') {
            $html .= '" value="#{' . $columnName . '}" ';
        }
        $html .= ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '');
        
        if (isset($column['type']) && $column['type'] == 'textarea') {
            $html .= '/>#{' . $columnName . '}</textarea>';
        } else {
            $html .= '/>';
        }
        
        return $html;
    }
}
