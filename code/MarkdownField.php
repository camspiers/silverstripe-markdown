<?php

class MarkdownField extends TextareaField
{

    function saveInto(DataObjectInterface $dataObject) {
        $fieldName = $this->name;
        if ($dataObject->$fieldName->hasMethod('setMarkdown')) {
            $dataObject->$fieldName->setMarkdown($this->Value(), true);
        }
    }
    
    function setValue($val) {

        if(is_array($val)) {
            $this->value = $val['Markdown'];
        } elseif($val instanceof Markdown) {
            $this->value = $val->getMarkdown();
        }

        return $this;
    }

}