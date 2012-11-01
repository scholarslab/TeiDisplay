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
 * Render the TEI document associated with the current view item.
 *
 * @return string The transformed HTML.
 */
function render_tei_document()
{

    $_db = get_db();
    $_textsTable = $_db->getTable('TeiDisplayText');

    // Try to get a text record.
    $item = get_current_record('item');
    $text = $_textsTable->findByItem($item);
    return $text->render();

}

/**
 * Set default DC->TEI mappings.
 *
 * @param string $element The name of the Dublin Core element.
 *
 * @return void.
 */
function get_tei_mapping($elementName)
{

    // Construct the key.
    $key = 'tei:dc:'.strtolower($elementName);

    // Try to get a custom setting.
    $custom = get_option($key);
    if (!is_null($custom)) return $custom;

    // Revent to system default.
    return get_plugin_ini('TeiDisplay', $key);

}

/**
 * Run an xpath query on a document.
 *
 * @param string $uri The uri of the document.
 * @param string $xpath The XPath query.
 *
 * @return object|boolean The matching nodes, false if no result.
 */
function xpath_query($uri, $xpath)
{

  $xml = new DomDocument();

  try {

    $xml->load($uri);
    $query = new DOMXPath($xml);
    $result = $query->query($xpath);

  }

  catch (Exception $e) { $result = false; }
  return $result;

}

/**
 * Create a new element text for an item.
 *
 * @param Item $item The parent item.
 * @param Element $element The element.
 * @param string $text The text content.
 *
 * @return ElementText The new text.
 */
function create_element_text()
{
    $text = new ElementText;
    $text->record_id = $item->id;
    $text->record_type = 'Item';
    $text->element_id = $element->id;
    $text->html = 0;
    $text->text = $text;
    return $text->save();
}

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
