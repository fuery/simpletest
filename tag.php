<?php
    // $Id$
    
    /**
     *    HTML or XML tag.
     */
    class SimpleTag {
        var $_name;
        var $_attributes;
        var $_content;
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param string $name        Tag name.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleTag($name, $attributes) {
            $this->_name = $name;
            $this->_attributes = $attributes;
            $this->_content = "";
        }
        
        /**
         *    Check to see if the tag can have both start and
         *    end tags with content in between.
         *    @return boolean        True if content allowed.
         *    @public
         */
        function expectEndTag() {
            return true;
        }
        
        /**
         *    Appends string content to the current content.
         *    @param $content        Additional text.
         *    @public
         */
        function addContent($content) {
            $this->_content .= (string)$content;
        }
        
        /**
         *    Accessor for tag name.
         *    @return        Name as string.
         *    @public
         */
        function getTagName() {
            return $this->_name;
        }
        
        /**
         *    Form element indicator.
         *    @return boolean        False.
         *    @public
         */
        function isWidget() {
            return false;
        }
        
        /**
         *    Accessor for an attribute.
         *    @param $label      Attribute name.
         *    @return            Attribute value as string.
         *    @public
         */
        function getAttribute($label) {
            if (!isset($this->_attributes[$label])) {
                return false;
            }
            if ($this->_attributes[$label] === true) {
                return true;
            }
            return (string)$this->_attributes[$label];
        }
        
        /**
         *    Accessor for the whole content so far.
         *    @return        Content as big string.
         *    @public
         */
        function getContent() {
            return $this->_content;
        }
    }
    
    /**
     *    Page title.
     */
    class SimpleTitleTag extends SimpleTag {
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleTitleTag($attributes) {
            $this->SimpleTag('title', $attributes);
        }
    }
    
    /**
     *    Link.
     */
    class SimpleAnchorTag extends SimpleTag {
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleAnchorTag($attributes) {
            $this->SimpleTag('a', $attributes);
        }
    }
    
    /**
     *    Form element.
     */
    class SimpleWidget extends SimpleTag {
        var $_value;
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param string $name        Tag name.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleWidget($name, $attributes) {
            $this->SimpleTag($name, $attributes);
            $this->_value = false;
        }
        
        /**
         *    Form element indicator.
         *    @return boolean        True.
         *    @public
         */
        function isWidget() {
            return true;
        }
        
        /**
         *    Accessor for name submitted as the key in
         *    GET/POST varaibles haah.
         *    @return string        Parsed value.
         *    @public
         */
        function getName() {
            return $this->getAttribute('name');
        }
        
        /**
         *    Accessor for default value parsed with the tag.
         *    @return string        Parsed value.
         *    @public
         */
        function getDefault() {
            return '';
        }
        
        /**
         *    Accessor for currently set value or default if
         *    none.
         *    @return string      Value set by form or default
         *                        if none.
         *    @public
         */
        function getValue() {
            if ($this->_value === false) {
                return $this->getDefault();
            }
            return $this->_value;
        }
        
        /**
         *    Sets the current form element value.
         *    @param string $value        New value.
         *    @public
         */
        function setValue($value) {
            $this->_value = $value;
        }
        
        /**
         *    Resets the form element value back to the
         *    default.
         *    @public
         */
        function resetValue() {
            $this->_value = false;
        }
    }
    
    /**
     *    Text, password and hidden field.
     */
    class SimpleTextTag extends SimpleWidget {
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleTextTag($attributes) {
            $this->SimpleWidget('input', $attributes);
        }
        
        /**
         *    Tag contains no content.
         *    @return boolean        False.
         *    @public
         */
        function expectEndTag() {
            return false;
        }
        
        /**
         *    Accessor for starting value.
         *    @return string        Parsed value.
         *    @public
         */
        function getDefault() {
            return $this->getAttribute('value');
        }
    }
    
    /**
     *    Text, password and hidden field.
     */
    class SimpleSubmitTag extends SimpleWidget {
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleSubmitTag($attributes) {
            if (! isset($attributes['name'])) {
                $attributes['name'] = 'submit';
            }
            if (! isset($attributes['value'])) {
                $attributes['value'] = 'Submit';
            }
            $this->SimpleWidget('input', $attributes);
        }
        
        /**
         *    Tag contains no end element.
         *    @return boolean        False.
         *    @public
         */
        function expectEndTag() {
            return false;
        }
        
        /**
         *    Accessor for starting value.
         *    @return string        Parsed value.
         *    @public
         */
        function getDefault() {
            return $this->getAttribute('value');
        }
        
        /**
         *    Disables the setting of the button value.
         *    @param string $value        Ignored.
         *    @public
         */
        function setValue($value) {
        }
    }
    
    /**
     *    Content tag for text area.
     */
    class SimpleTextAreaTag extends SimpleWidget {
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleTextAreaTag($attributes) {
            $this->SimpleWidget('textarea', $attributes);
        }
        
        /**
         *    Accessor for starting value.
         *    @return string        Parsed value.
         *    @public
         */
        function getDefault() {
            return $this->getContent();
        }
    }
    
    /**
     *    Content tag for text area.
     */
    class SimpleFormTag extends SimpleTag {
        
        /**
         *    Starts with a named tag with attributes only.
         *    @param hash $attributes    Attribute names and
         *                               string values.
         */
        function SimpleFormTag($attributes) {
            $this->SimpleTag('form', $attributes);
        }
    }
    
    /**
     *    Form tag class to hold widget values.
     */
    class SimpleForm {
        var $_method;
        var $_action;
        var $_id;
        var $_buttons;
        var $_widgets;
        
        /**
         *    Starts with no held controls/widgets.
         *    @param $tag        Form tag to read.
         */
        function SimpleForm($tag) {
            $this->_method = $tag->getAttribute("method");
            $this->_action = $tag->getAttribute("action");
            $this->_id = $tag->getAttribute("id");
            $this->_buttons = array();
            $this->_widgets = array();
        }
        
        /**
         *    Accessor for form action.
         *    @return            Either get or post.
         *    @public
         */
        function getMethod() {
            return ($this->_method ? strtolower($this->_method) : 'get');
        }
        
        /**
         *    Relative URL of the target.
         *    @return            URL as string.
         *    @public
         */
        function getAction() {
            return $this->_action;
        }
        
        /**
         *    ID field of form for unique identification.
         *    @return            ID as integer.
         *    @public
         */
        function getId() {
            return $this->_id;
        }
        
        /**
         *    Adds a tag contents to the form.
         *    @param $tag        Input tag to add.
         *    @public
         */
        function addWidget($tag) {
            if ($tag->getAttribute("type") == "submit") {
                $this->_buttons[$tag->getName()] = &$tag;
            } else {
                $this->_widgets[$tag->getName()] = &$tag;
            }
        }
        
        /**
         *    Extracts current value from form.
         *    @param $name        Keyed by widget name.
         *    @return             Value as string or null
         *                        if not set.
         *    @public
         */
        function getValue($name) {
            if (isset($this->_widgets[$name])) {
                return $this->_widgets[$name]->getValue();
            }
            return null;
        }
        
        /**
         *    Sets a widget value within the form.
         *    @param $name     Name of widget tag.
         *    @param $value    Value to input into the widget.
         *    @return          True if value is legal, false
         *                     otherwise. f the field is not
         *                     present, nothing will be set.
         *    @public
         */
        function setField($name, $value) {
            if (isset($this->_widgets[$name])) {
                $this->_widgets[$name]->setValue($value);
                return true;
            }
            return false;
        }
        
        /**
         *    Reads the current form values as a hash
         *    of submitted parameters.
         *    @return          Hash of submitted values.
         *    @public
         */
        function getValues() {
            $values = array();
            foreach (array_keys($this->_widgets) as $name) {
                $values[$name] = $this->_widgets[$name]->getValue();
            }
            return $values;
        }
        
        /**
         *    Gets a button name from the label.
         *    @param $label    Button label to search for.
         *    @return          Name of button.
         *    @public
         */
        function getSubmitName($label) {
            foreach (array_keys($this->_buttons) as $name) {
                if ($this->_buttons[$name]->getValue() == $label) {
                    return $name;
                }
            }
        }
        
        /**
         *    Gets the submit values for a named button.
         *    @param $name     Button label to search for.
         *    @return          Hash of submitted values or false
         *                     if there is no such button in the
         *                     form.
         *    @public
         */
        function submitButton($name) {
            if (!isset($this->_buttons[$name])) {
                return false;
            }
            return array_merge(
                    array($name => $this->_buttons[$name]->getValue()),
                    $this->getValues());            
        }
        
        /**
         *    Gets the submit values for a button with a particular
         *    label.
         *    @param $label    Button label to search for.
         *    @return          Hash of submitted values or false
         *                     if there is no such button in the
         *                     form.
         *    @public
         */
        function submitButtonByLabel($label) {
            if ($name = $this->getSubmitName($label)) {
                return $this->submitButton($name);
            }
            return false;
        }
        
        /**
         *    Simply submits the form without the submit button
         *    value. Used when there is only one button or it
         *    is unimportant.
         *    @return            Hash of submitted values.
         *    @public
         */
        function submit() {
            return $this->getValues();            
        }
    }
?>