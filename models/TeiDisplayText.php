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


}
