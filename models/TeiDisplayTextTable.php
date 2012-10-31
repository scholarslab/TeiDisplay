<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Table class for texts.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplayTextTable extends Omeka_Db_Table
{

    /**
     * Get text for an item.
     *
     * @param Item $item The item.
     *
     * @return TeiDisplayText The text.
     */
    public function findByItem($item)
    {
        $select = $this->getSelect()->where('item_id=?', $item->id);
        return $this->fetchObject($select);
    }

    /**
     * Create text record for item or update existing record.
     *
     * @param Item $item The item.
     * @param integer $sheetId The id of the stylesheet.
     * @param integer $fileId The id of the XML file.
     *
     * @return array TeiDisplayText $texts The texts.
     */
    public function createOrUpdate($item, $sheetId, $fileId)
    {

        // Try to get an existing text.
        $text = $this->findByItem($item);

        // If no existing text, create one.
        if (!$text) $text = new TeiDisplayText($item);

        // Update and save.
        $text->file_id = $fileId;
        $text->sheet_id = $sheetId;
        return $text->save();

    }

    /**
     * Save DC->TEI mappings from config form.
     *
     * @param array $post The post.
     *
     * @return void.
     */
    public function saveTeiMappings($post)
    {

        // Get DC elements.
        $elements = $this->getTable('Element')->findBySet('Dublin Core');

        // Save mappings.
        foreach ($elements as $element) {

            // Get key and value.
            $key = 'tei:dc:'.strtolower($element->name);
            $val = $post[$element->name];

            // If resetting, delete.
            if ($post['reset'] == 1) {
                delete_option($key);
                continue;
            }

            // If different from default, set.
            if ($val && $val !== get_plugin_ini('TeiDisplay', $key))
                set_option($key, $val);

        }

    }

    /**
     * Write the TEI header onto the Dublin Core record.
     *
     * @param Item $item The item.
     *
     * @return void.
     */
    public function importTeiHeader($item)
    {

    }

}
