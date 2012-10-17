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
require_once dirname(__FILE__) . '/mocks/TransferAdapterMock.php';

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

        // Get plugin tables.
        $this->sheetsTable = $this->db->getTable('TeiDisplayStylesheet');
        $this->textsTable = $this->db->getTable('TeiDisplayText');

        // Copy XML mock to Omeka.
        copy('mocks/winters-tale.xml',
          '../../../files/original/winters-tale.xml');

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
     * Create a text.
     *
     * @param Item $item The parent item.
     * @param File $file The parent file.
     *
     * @return TeiDisplayText $text.
     */
    public function __text($item=null, $file=null, $active=true)
    {

        // Create item.
        if (is_null($item)) $item = $this->__item();

        // Create file.
        if (is_null($file)) $file = $this->__file($item);

        $text = new TeiDisplayText;
        $text->item_id = $item->id;
        $text->file_id = $file->id;
        $text->active = true;
        $text->save();

        return $text;

    }

    /**
     * Create a file, using mocks/winters-tale.xml as the source.
     *
     * @param Item $item The parent item.
     * @param string $originalFilename The filename.
     *
     * @return File $file.
     */
    public function __file($item=null, $originalFilename='winters-tale.xml')
    {

        // Create item.
        if (is_null($item)) $item = $this->__item();

        $this->db->insert('File', array(
            'item_id' => $item->id,
            'size' => 310112,
            'has_derivative_image' => 0,
            'filename' => 'winters-tale.xml',
            'original_filename' => 'winters-tale.xml',
            'metadata' => '[]',
        ));

        // Get the File table.
        $_fileTable = $this->db->getTable('File');

        $newId = $_fileTable->lastInsertId();
        return $_fileTable->find($newId);

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

        // Mock $_FILES.
        $_FILES = array('xslt' => array(
            'name' =>       '',
            'type' =>       null,
            'tmp_name' =>   '',
            'error' =>      4,
            'size' =>       ''
        ));

        // Set mock adapter.
        Zend_Registry::set('adapter', new TransferAdapterMock());

    }

    /**
     * Mock an empty file input on the stylesheet add/edit form.
     *
     * @param string $xslt The mock xslt content.
     *
     * @return Omeka_record $item The item.
     */
    public function __setStylesheetFileUpload($xslt = 'xslt mock')
    {

        // Write mock tmp file.
        $tmpDir = sys_get_temp_dir();
        file_put_contents($tmpDir . '/mock.xslt', $xslt);

        // Mock $_FILES.
        $_FILES = array('xslt' => array(
            'name' =>       'mock.xslt',
            'tmp_name' =>   'mock.xslt',
            'type' =>       'application/octet-stream',
            'error' =>      0,
            'size' =>       '10'
        ));

        // Set mock adapter.
        Zend_Registry::set('adapter', new TransferAdapterMock());

    }

}
