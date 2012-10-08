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

        // Stylesheet.
        $this->addElement('select', 'stylesheet', array(
            'label'         => __('Stylesheet'),
            'description'   => __('Select an XSLT stylesheet.'),
            'multiOptions'  => $this->getStylesheetsForSelect()
        ));

    }

    /**
     * Get the list of XSLT stylesheets.
     *
     * @return array $servers The server.
     */
    public function getStylesheetsForSelect()
    {

        // Get file table.
        $_db = get_db();
        $_stylesheets = $_db->getTable('TeiDisplayStylesheet');

        // Fetch.
        $records = $_stylesheets->findAll();

        // Build the array.
        $stylesheets = array();
        foreach($records as $record) {
            $stylesheets[$record->id] = $record->title;
        };

        return $stylesheets;

    }

}
