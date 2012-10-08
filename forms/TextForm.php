<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Text form. Displayed inside of item add/edit form.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplay_Form_Text extends Omeka_Form
{

    // private $_stylesheet;

    /**
     * Build the add/edit form.
     *
     * @return void.
     */
    public function init()
    {

        parent::init();

        $this->setMethod('post');
        $this->setAttrib('id', 'text-form');
        $this->addElementPrefixPath('TeiDisplay', dirname(__FILE__));

    }

    // public function setStylesheet(TeiDisplayStylesheet $stylesheet)
    // {
    //     $this->_stylesheet = $stylesheet;
    // }

}
