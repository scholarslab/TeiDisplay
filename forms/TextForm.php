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

    private $_item;

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
        $this->addElement('select', 'teitext', array(
            'label'         => __('TEI Texts'),
            'description'   => __('Select an active text.'),
            'multiOptions'  => $this->getTextsForSelect()
        ));

        // Stylesheet.
        $this->addElement('select', 'teistylesheet', array(
            'label'         => __('Stylesheet'),
            'description'   => __('Select an XSLT stylesheet.'),
            'multiOptions'  => $this->getStylesheetsForSelect()
        ));

        // Import.
        $this->addElement('checkbox', 'teiimport', array(
            'label'         => 'Import TEI Header?',
            'description'   => 'Map TEI header data to Dublin Core when the Item form is saved.'
        ));

    }

    /**
     * Get the list of texts.
     *
     * @return void.
     */
    public function getTextsForSelect()
    {

        $_db = get_db();
        $_files = $_db->getTable('File');

        // Fetch texts.
        $records = $_files->findByItem($this->_item->id);

        // Build the array.
        $texts = array();
        foreach($records as $record) {
            if ($record->mime_type == 'application/xml') {
                $texts[$record->id] = $record->original_filename;
            }
        };

        return $texts;

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

    /**
     * Set the parent item.
     *
     * @return void.
     */
    public function setItem(Item $item)
    {
        $this->_item = $item;
    }

}
