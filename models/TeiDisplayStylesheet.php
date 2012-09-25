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
     * The name of the layer.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $xslt;

}
