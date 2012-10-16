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
     * Get the current active text for an item.
     *
     * @param Item $item The item.
     *
     * @return TeiDisplayText $text The active text.
     */
    public function getActiveText($item)
    {
        $select = $this->getSelect()->
            where('active=1 AND item_id=?', $item->id);
        return $this->fetchObject($select);
    }

}
