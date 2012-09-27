<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Stylesheet add/edit form.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplay_Form_Stylesheet extends Omeka_Form
{

    private $_stylesheet;

    /**
     * Build the add/edit form.
     *
     * @return void.
     */
    public function init()
    {

        parent::init();

        $this->setMethod('post');
        $this->setAttrib('id', 'stylesheet-form');
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->addElementPrefixPath('TeiDisplay', dirname(__FILE__));

        // Title.
        $this->addElement('text', 'title', array(
            'label'         => __('Title'),
            'description'   => __('An identified used for internal content management.'),
            'size'          => 40,
            'value'         => $this->_stylesheet->title,
            'required'      => true,
            'validators'    => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array('messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => __('Enter a title.')
                    ))
                )
            )
        ));

        // XSLT.
        $this->addElement('file', 'xslt', array(
            'label'         => __('XSLT'),
            'description'   => __('Select the XSLT file.'),
            'attribs'       => array('class' => 'html-editor', 'rows' => '10')
        ));

        // Submit.
        $this->addElement('submit', 'submit', array(
            'label' => __('Save Stylesheet')
        ));

    }

    public function setStylesheet(TeiDisplayStylesheet $stylesheet)
    {
        $this->_stylesheet = $stylesheet;
    }

}
