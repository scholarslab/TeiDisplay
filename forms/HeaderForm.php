<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * TEI->DC mapping form. Displayed in plugin configuration interface.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplay_Form_Header extends Omeka_Form
{

    /**
     * Build the Dublin Core -> TEI form.
     *
     * @return void.
     */
    public function init()
    {

        parent::init();

        $_db = get_db();
        $this->setMethod('post');
        $this->setAttrib('id', 'text-form');
        $this->addElementPrefixPath('TeiDisplay', dirname(__FILE__));

        // Get DC elements.
        $elements = $_db->getTable('Element')->findBySet('Dublin Core');

        // Construct elements.
        foreach ($elements as $element) {
            $this->addElement('text', $element->name, array(
                'label' => __($element->name),
                'value' => get_tei_mapping(strtolower($element->name))
            ));
        }

        // Reser.
        $this->addElement('checkbox', 'reset', array(
            'label'     => 'Restore defalts'
        ));

    }

}
