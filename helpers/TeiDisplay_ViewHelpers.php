<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * View Helpers for the TeiDisplay plugin
 *
 * @category  Plugin
 * @package   Omeka
 * @author    Scholars' Lab
 * @copyright 2011 The Board and Visitors of the University of Virginia
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @link      https://github.com/scholarslab/TeiDisplay
 */
class TeiDisplay_ViewHelpers
{
    /**
     * Creates and returns a configuration form
     *
     * @return Zend_Form
     */
    public static function makeConfigForm()
    {
        $xslFiles = TeiDisplay_File::getFiles();

        $form = new Zend_Form();
        $form->setMethod('post');

        $form->addElement(
            'select',
            'tei_display_type',
            array(
                'label' => 'Display Type:',
                'value' => get_option('tei_display_type'),
                'multiOptions' => array(
                    'entire' => 'Entire Document',
                    'segmental' => 'Segmental'
                )
            )
        );

        $form->addElement(
            'select',
            'tei_default_stylesheet',
            array(
                'label' => 'Default Stylesheet',
                'value' => get_option('tei_default_stylesheet'),
                'multiOptions' => $xslFiles
            )
        );

        $form->addElement(
            'submit',
            'submit',
            array(
                'label' => 'Save Changes'
            )
        );

        return $form;
    }

}
