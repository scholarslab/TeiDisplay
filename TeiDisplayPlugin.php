<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Plugin manager class.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplayPlugin extends Omeka_Plugin_AbstractPlugin
{


    // Hooks.
    protected $_hooks = array(
        'install',
        'uninstall',
        'define_routes',
        'before_save_file',
        'after_save_file'
    );

    // Filters.
    protected $_filters = array(
        'admin_navigation_main',
        'admin_items_form_tabs'
    );

    // XML mime types.
    protected $_xmlMimeTypes = array(
        'application/xml',
        'text/xml'
    );


    /**
     * Initialize registry key to track new XML uploads.
     *
     * @return void.
     */
    public function __construct()
    {
        parent::__construct();
        Zend_Registry::set('teiDisplay:NewXml', false);
    }


    // ------
    // Hooks.
    // ------


    /**
     * Create tables.
     *
     * @return void.
     */
    public function hookInstall()
    {

        // Stylesheets table.
        $tableName = $this->_db->TeiDisplayStylesheet;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (

            `id`        int(10) unsigned NOT NULL auto_increment,
            `title`     tinytext collate utf8_unicode_ci,
            `xslt`      TEXT COLLATE utf8_unicode_ci NULL,
            `modified`  TIMESTAMP NULL,

             PRIMARY KEY (`id`)

        ) ENGINE=innodb DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $this->_db->query($sql);

        // Texts table.
        $tableName = $this->_db->TeiDisplayText;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (

            `id`        int(10) unsigned NOT NULL auto_increment,
            `item_id`   int(10) unsigned NOT NULL,
            `file_id`   int(10) unsigned NOT NULL,

             PRIMARY KEY (`id`)

        ) ENGINE=innodb DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $this->_db->query($sql);

    }

    /**
     * Drop tables.
     *
     * @return void.
     */
    public function hookUninstall()
    {
        $tableName = $this->_db->TeiDisplayStylesheet;
        $sql = "DROP TABLE IF EXISTS `$tableName`";
        $this->_db->query($sql);
    }

    /**
     * Register routes.
     *
     * @param object $router Router passed in by the front controller.
     *
     * @return void.
     */
    public function hookDefineRoutes($args)
    {

        // Default stylesheets routes.
        $args['router']->addRoute(
            'teiDisplayStylesheetsDefault',
            new Zend_Controller_Router_Route(
                'tei/stylesheets/:action',
                array(
                    'module'        => 'tei-display',
                    'controller'    => 'stylesheets',
                    'action'        => 'browse'
                )
            )
        );

        // Stylesheet-specific routes.
        $args['router']->addRoute(
            'teiDisplayStylesheetsId',
            new Zend_Controller_Router_Route(
                'tei/stylesheets/:action/:id',
                array(
                    'module'        => 'tei-display',
                    'controller'    => 'stylesheets'
                ),
                array(
                    'id'            => '\d+'
                )
            )
        );

    }

    /**
     * Listen for new XML file uploads.
     *
     * @param File $file The new file record.
     *
     * @return void.
     */
    public function hookBeforeSaveFile($args)
    {

        // Break if file exists.
        if ($args['record']->exists()) return;

        // Check for XML.
        $mimeType = $args['record']->getMimeType();
        if (in_array($mimeType, $this->_xmlMimeTypes)) {
            Zend_Registry::set('teiDisplay:NewXml', true);
        }

    }

    /**
     * Create new text.
     *
     * @param File $file The new file record.
     *
     * @return void.
     */
    public function hookAfterSaveFile($args)
    {

        // Check for new file.
        if (Zend_Registry::get('teiDisplay:NewXml')) {

            // Create text.
            $text = new TeiDisplayText;
            $text->item_id = $args['record']->item_id;
            $text->file_id = $args['record']->id;
            $text->save();

            // Reset tracker.
            Zend_Registry::set('teiDisplay:NewXml', false);

        }

    }


    // --------
    // Filters.
    // --------


    /**
     * Add TEI tab to main admin menu bar.
     *
     * @param array $tabs This is an array of label => URI pairs.
     *
     * @return array The tabs array with the TEI tab.
     */
    public function filterAdminNavigationMain($tabs)
    {
        $tabs['TEI'] = url('tei/stylesheets');
        return $tabs;
    }

    /**
     * Add TEI tab to item add/edit form.
     *
     * @param array $tabs This is an array of label => URI pairs.
     *
     * @return array The tabs array with the TEI tab.
     */
    public function filterAdminItemsFormTabs($tabs)
    {
        $tabs['TEI'] = 'test';
        return $tabs;
    }

}
