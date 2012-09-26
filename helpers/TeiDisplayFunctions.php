<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Miscellaneous helpers.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Check for sytlesheet records on the view.
 *
 * @return boolean
 */
function has_stylesheets_for_loop()
{
    $view = get_view();
    return ($view->stylesheets and count($view->stylesheets));
}
