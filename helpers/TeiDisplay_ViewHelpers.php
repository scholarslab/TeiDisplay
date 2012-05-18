<?php

class TeiDisplay_ViewHelpers
{
    /**
     * Creates and returns a configuration form
     *
     * @return Zend_Form
     */
    public static function makeConfigForm()
    {
        $form = new Zend_Form();
        TeiDisplay_ViewHelpers::makeConfigFields($form);
        return $form;
    }

    /**
     * Creates fields for the configutation form. If a form is passed in, the
     * fields are appended.
     *
     * @param Zend_Form|null $form The form to append fields
     *
     * @return array $fields An associative array mapping option names to fields
     */
    public static function makeConfigFields($form = null)
    {
        $xslFiles = TeiDisplay_File::getFiles();

        $fields = array();

        $fields[] = TeiDisplay_ViewHelpers::makeOptionField(
            $form, 'tei_display_type', 'Display Type', true
        );

        $fields[] = TeiDisplay_ViewHelpers::makeOptionField(
            $form, 'tei_default_stylesheet', 'Default Stylesheet', true
        );

        return $fields;
    }

    /**
     * Create a single option field for a form
     *
     * @param Zend_Form $form     Form to add element to
     * @param string    $name     Field name
     * @param string    $label    Field label
     * @param boolean   $required If the field is required
     * @param string    $desc     Description
     * @param string    $cls      Type of form element
     *
     * @return Zend_Form_Element
     */
    public static function makeOptionField(
        $form, $name, $label, $required, $desc = null,
        $cls = 'Zend_Form_Element_Text'
    ) {
        $field = new $cls($name, array(
            'label' => $label,
            'value' => get_option($name),
            'required' => $required
        ));
        if ($descr != null) {
            $field->setDescription($descr);
        }

        if ($form != null) {
            $form->addElement($field);
        }

        return $field;
    }
}
