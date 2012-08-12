<?php

use dflydev\markdown\MarkdownParser;

class Markdown extends DBField implements CompositeDBField
{

    protected $markdown = null;
    protected $markdownCompiled = null;

    static $composite_db = array(
        "Markdown" => "Text",
        "MarkdownCompiled" => 'HTMLText'
    );

    public function getMarkdown()
    {
        return $this->markdown;
    }

    public function getMarkdownCompiled()
    {
        return $this->markdownCompiled;
    }

    function compositeDatabaseFields() {
        return self::$composite_db;
    } 

    function requireField() {
        $fields = $this->compositeDatabaseFields();
        if($fields) foreach($fields as $name => $type){
            DB::requireField($this->tableName, $this->name.$name, $type);
        }
    }

    function writeToManipulation(&$manipulation) {
        $markdown = $this->getMarkdown();
        if($markdown) {
            $parser = new MarkdownParser;
            $manipulation['fields'][$this->name.'Markdown'] = $this->prepValueForDB($markdown);
            $manipulation['fields'][$this->name.'MarkdownCompiled'] = $this->prepValueForDB($parser->transformMarkdown($markdown));
        } else {
            $manipulation['fields'][$this->name.'Markdown'] = DBField::create_field('Text', $this->getMarkdown())->nullValue();
            $manipulation['fields'][$this->name.'MarkdownCompiled'] = DBField::create_field('HTMLText', '')->nullValue();
        }

    }
    
    function addToQuery(&$query) {
        parent::addToQuery($query);
        $query->selectField(sprintf('"%Markdown"', $this->name));
        $query->selectField(sprintf('"%MarkdownCompiled"', $this->name));
    }

    public function setMarkdown($markdown, $markChanged = true) {
        $this->markdown = (string)$markdown;
        if($markChanged) $this->isChanged = true;
    }

    public function setMarkdownCompiled($markdownCompiled, $markChanged = true) {
        $this->markdownCompiled = (string)$markdownCompiled;
        if($markChanged) $this->isChanged = true;
    }

    function setValue($value, $record = null, $markChanged = true) {
        if ($value instanceof Markdown && $value->exists()) {
            $this->setMarkdown($value->getMarkdown(), $markChanged);
            $this->setMarkdownCompiled($value->getMarkdownCompiled(), $markChanged);
            if($markChanged) $this->isChanged = true;
        } else if($record && isset($record[$this->name . 'Markdown']) && isset($record[$this->name . 'MarkdownCompiled'])) {
            $this->setMarkdown($record[$this->name . 'Markdown']);
            $this->setMarkdownCompiled($record[$this->name . 'MarkdownCompiled']);
            if($markChanged) $this->isChanged = true;
        } else if (is_array($value)) {
            if (array_key_exists('Markdown', $value)) {
                $this->setMarkdown($value['Markdown'], $markChanged);
            }
            if (array_key_exists('MarkdownCompiled', $value)) {
                $this->setMarkdownCompiled($value['MarkdownCompiled'], $markChanged);
            }
            if($markChanged) $this->isChanged = true;
        }
    }

    /**
     * @return string
     */
    function Nice($options = array()) {
        return $this->getMarkdownCompiled();
    }
    
    /**
     * @return boolean
     */
    function exists() {
        return $this->getMarkdown();
    }
    
    function isChanged() {
        return $this->isChanged;
    }
    
    /**
     * Returns a CompositeField instance used as a default
     * for form scaffolding.
     *
     * Used by {@link SearchContext}, {@link ModelAdmin}, {@link DataObject::scaffoldFormFields()}
     * 
     * @param string $title Optional. Localized title of the generated instance
     * @return FormField
     */
    public function scaffoldFormField($title = null) {
        return new MarkdownField($this->name);
    }
    
}