<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Test runner.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

require_once dirname(__FILE__) . '/../TeiDisplayPlugin.php';

class TeiDisplay_Test_AppTestCase extends Omeka_Test_AppTestCase
{

    /**
     * Bootstrap the plugin.
     *
     * @return void.
     */
    public function setUp()
    {

        parent::setUp();

        // Authenticate and set the current user.
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);

        // Set up Neatline.
        $pluginBroker = get_plugin_broker();
        $pluginBroker->setCurrentPluginDirName('TeiDisplay');
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $pluginHelper->setUp('TeiDisplay');

        // Run plugin.
        $tei = new TeiDisplayPlugin;
        $tei->setUp();

    }

}
