<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Row class for XML text.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplayText extends Omeka_Record_AbstractRecord
{


    /**
     * The parent item.
     * int(10) unsigned NOT NULL
     */
    public $item_id;

    /**
     * The current file.
     * int(10) unsigned NOT NULL
     */
    public $file_id;

    /**
     * The current stylesheet.
     * int(10) unsigned NOT NULL
     */
    public $sheet_id;


    /**
     * Set the parent item.
     *
     * @param Item $item The parent item.
     *
     * @return void.
     */
    public function __construct($item=null)
    {
        parent::__construct();
        if ($item) $this->item_id = $item->id;
    }

    /**
     * Get the parent item.
     *
     * @return Item: The parent item.
     */
    public function getItem()
    {
        $_itemsTable = $this->getTable('Item');
        return $_itemsTable->find($this->item_id);
    }

    /**
     * Get the parent file.
     *
     * @return File: The parent file.
     */
    public function getFile()
    {
        $_filesTable = $this->getTable('File');
        return $_filesTable->find($this->file_id);
    }

    /**
     * Get the parent stylesheet.
     *
     * @return TeiDisplayStylesheet: The parent sheet.
     */
    public function getSheet()
    {
        $_sheetsTable = $this->getTable('TeiDisplayStylesheet');
        return $_sheetsTable->find($this->sheet_id);
    }

    /**
     * Render XML->HTML.
     *
     * @return string The generated markup.
     */
    public function render()
    {

        // Get tei and xslt.
        $tei = $this->getFile();
        $xsl = $this->getSheet();

        // Create documents.
        $teiDoc = new DOMDocument();
        $xslDoc = new DOMDocument();

        // Load content.
        $teiDoc->load($tei->getWebPath('original'));
        $xslDoc->loadXml($xsl->xslt);

        // XSLT processor.
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        // Render.
        // return $proc->transformToXml($teiDoc);
        return htmlspecialchars($xsl->xslt);

    }

}
