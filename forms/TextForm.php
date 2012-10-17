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

        // Text.
        $this->addElement('select', 'text', array(
            'label'         => __('TEI Texts'),
            'description'   => __('Select an active text.')
        ));

        // Stylesheet.
        $this->addElement('select', 'stylesheet', array(
            'label'         => __('Stylesheet'),
            'description'   => __('Select an XSLT stylesheet.'),
            'multiOptions'  => $this->getStylesheetsForSelect()
        ));

        // Import.
        $this->addElement('checkbox', 'import', array(
            'label'         => 'Import TEI Header?',
            'description'   => 'Map TEI header data to Dublin Core when the Item form is saved.'
        ));

    }

    /**
     * Get the list of texts.
     *
     * @param Item $item The parent item.
     *
     * @return void.
     */
    public function setTextsForSelect($item)
    {

        $_db = get_db();
        $_texts = $_db->getTable('TeiDisplayText');

        // Fetch texts.
        $records = $_texts->findByItem($item);

        // Build the array.
        $texts = array();
        foreach($records as $record) {
            $texts[$record->id] = $record->getFileName();
        };

        // Set the options.
        $this->getElement('text')->setMultiOptions($texts);

    }

    /**
     * Get the list of XSLT stylesheets.
     *
     * @return array $servers The server.
     */
    public function getStylesheetsForSelect()
    {

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
