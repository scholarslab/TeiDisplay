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
require_once dirname(__FILE__) . '/mocks/StylesheetFormMock.php';
require_once dirname(__FILE__) . '/mocks/FileElementMock.php';

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

        // Run plugin.
        $tei = new TeiDisplayPlugin;
        $tei->setUp();

        // Configure helper.
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $pluginHelper->setUp('TeiDisplay');

    }


    /**
     * Testing helpers.
     */


    /**
     * Create a stylesheet.
     *
     * @param string $title The title.
     * @param string $xslt The xslt.
     *
     * @return TeiDisplayStylesheet $stylesheet.
     */
    public function __stylesheet($title='Test Title', $xslt='xslt')
    {

        $stylesheet = new TeiDisplayStylesheet;
        $stylesheet->title = $title;
        $stylesheet->xslt = $xslt;
        $stylesheet->save();

        return $stylesheet;

    }

    /**
     * Create an item.
     *
     * @return Omeka_record $item The item.
     */
    public function __item()
    {
        $item = new Item;
        $item->save();
        return $item;
    }

    /**
     * Mock an empty file input on the stylesheet add/edit form.
     *
     * @return Omeka_record $item The item.
     */
    public function __setEmptyStylesheetFileUpload()
    {
        $_FILES = array('xslt' => array(
            'name' =>       '',
            'type' =>       null,
            'tmp_name' =>   '',
            'error' =>      4,
            'size' =>       ''
        ));
    }

}
