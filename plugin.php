<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Plugin runner.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


if (!defined('TEI_PLUGIN_VERSION'))
    define('TEI_PLUGIN_VERSION', get_plugin_ini('TeiDisplay', 'version'));

if (!defined('TEI_PLUGIN_DIR'))
    define('TEI_PLUGIN_DIR', dirname(__FILE__));

// Load assets.
require_once TEI_PLUGIN_DIR . '/TeiDisplayPlugin.php';
require_once TEI_PLUGIN_DIR . '/helpers/TeiDisplayFunctions.php';
require_once TEI_PLUGIN_DIR . '/forms/StylesheetForm.php';
require_once TEI_PLUGIN_DIR . '/forms/TextForm.php';

// Set file transfer adapter.
$adapter = new Zend_File_Transfer_Adapter_Http();
Zend_Registry::set('adapter', $adapter);

// Run.
$tei = new TeiDisplayPlugin();
$tei->setUp();
