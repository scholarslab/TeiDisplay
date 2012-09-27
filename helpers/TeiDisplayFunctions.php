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
 * Constructs a link to a stylesheet.
 *
 * @param string $text HTML for the text of the link.
 * @param array $props Attributes for the link tag. (optional)
 * @param string $action The action for the link. Default is 'show'.
 * @param $stylesheet TeiDisplayStylesheet|null A record to work on.
 *
 * @return string The link markup.
 */
function link_to_stylesheet(
    $text=null, $props=array(), $action='show', $stylesheet = null)
{

    if (is_null($stylesheet)) $stylesheet = get_current_stylesheet();
    $text = $text ? $text : strip_formatting(stylesheet('title', $stylesheet));

    $route = 'tei/stylesheets/' . $action . '/' . $stylesheet->id;
    $props['href'] = url($route);
    return '<a ' . tag_attributes($props) . '>' . $text . '</a>';

}

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
 * Sets the current stylesheet on the view.
 *
 * @param NeatlineExhibit|null The stylesheet.
 *
 * @return void
 */
function set_current_stylesheet($stylesheet = null)
{
    get_view()->tei_display_stylesheet = $stylesheet;
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
