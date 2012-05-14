<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */


//require_once(TEIDISPLAY_PLUGIN_DIR . '/TeiDisplayPlugin.php');

echo TEIDISPLAY_PLUGIN_DIR . "/TeiDisplayPlugin\n";

class TeiDisplay_Test_AppTestCase extends Omeka_Test_AppTestCase
{
    private $_dbHelper;

    public function setUpPlugin()
    {
        parent::setUp();

        // crreate an authenticate user
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);

        $plugin_broker = get_plugin_broker();
        $this->_addHooksAndFilters($plugin_broker, 'TeiDisplay');
        $plugin_helper = new Omeka_Test_Helper_Plugin;
        $plugin_helper->setup('TeiDisplay');

        // database helper
        $this->_dbHelper = Omeka_Test_Helper_Db::factory($this->core);

        // get tables
    }

    /**
     * Install TeiDisplay
     *
     * @param string $plugin_broker Plugin broker
     * @param string $plugin_name   Plugin name
     *
     * @return void
     */
    private function _addHooksAndFilters($plugin_broker, $plugin_name)
    {
        $plugin_broker->setCurrentPluginDirName($plugin_name);
        new TeiDisplayPlugin();
    }


}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
