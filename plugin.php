<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * TeiDisplay plugin
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 * 
 */

//{{{constants

if (!defined('TEI_DISPLAY_DIRECTORY')) {
    define('TEI_DISPLAY_DIRECTORY', dirname(__FILE__));
}

if (!defined('TEI_DISPLAY_STYLESHEET_FOLDER')) {
    define('TEI_DISPLAY_STYLESHEET_FOLDER', TEI_DISPLAY_DIRECTORY . '/libraries/');
}

//}}}

require_once TEI_DISPLAY_DIRECTORY . '/TeiDisplayPlugin.php';

new TeiPlugin;
/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
