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
    return ($view->tei_display_stylesheets and
      count($view->tei_display_stylesheets));
}

/**
 * Get the current stylesheet on the view.
 *
 * @return TeiDisplayStylesheet|null The stylesheet.
 */
function get_current_stylesheet()
{
    return get_view()->tei_display_stylesheet;
}

/**
 * Get field on stylesheet record.
 *
 * @param string $fieldname The attribute.
 * @param TeiDisplayStylesheet $stylesheet A record to work on.
 *
 * @return string The field value.
 */
function stylesheet($fieldname, $stylesheet = null)
{
    if (is_null($stylesheet)) $stylesheet = get_current_stylesheet();
    return $stylesheet->$fieldname;
}
