<?php
if (!($omekaDir = getenv('OMEKA_DIR'))) {
    $omekaDir = dirname(dirname(dirname(dirname(__FILE__))));
}

if (!defined('TEIDISPLAY_PLUGIN_DIR')) {
    define('TEIDISPLAY_PLUGIN_DIR', dirname(dirname(__FILE__)));
}

require_once $omekaDir . '/application/tests/bootstrap.php';
require_once 'TeiDisplay_Test_AppTestCase.php';
