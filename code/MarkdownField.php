<?php

class MarkdownField extends TextareaField
{

    function saveInto(DataObjectInterface $dataObject) {
        $fieldName = $this->name;
        if ($dataObject->$fieldName->hasMethod('setMarkdown')) {
            $dataObject->$fieldName->setMarkdown($this->Value());
        }
    }

}