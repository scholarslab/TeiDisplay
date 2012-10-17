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
     * The parent file.
     * int(10) unsigned NOT NULL
     */
    public $file_id;

    /**
     * True if the text is active.
     * tinyint(1) NOT NULL
     */
    public $active;


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
     * Manage `active` uniqueness.
     *
     * @return void.
     */
    public function beforeSave()
    {

        // Get the current active text.
        $_textsTable = $this->getTable('TeiDisplayText');
        $activeText = $_textsTable->getActiveText($this->getItem());

        // Is the new text set to active?
        if ($this->active == 1) {

            // Is the current active non-self?
            if ($activeText && $activeText->id !== $this->id) {
                $activeText->active = 0;
                $activeText->save();
            }

        }

    }

}
