<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Row class for XSLT stylesheet.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplayStylesheet extends Omeka_Record_AbstractRecord
{

    /**
     * The XSLT stylesheet.
     * tinytext collate utf8_unicode_ci
     */
    public $title;

    /**
     * The XSLT stylesheet.
     * TEXT COLLATE utf8_unicode_ci NULL
     */
    public $xslt;

}
