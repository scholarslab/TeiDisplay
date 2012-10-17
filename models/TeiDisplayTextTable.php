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
     * Get all texts for an item.
     *
     * @param Item $item The item.
     *
     * @return array TeiDisplayText $texts The texts.
     */
    public function findByItem($item)
    {
        $select = $this->getSelect()->where('item_id=?', $item->id);
        return $this->fetchObjects($select);
    }

}
