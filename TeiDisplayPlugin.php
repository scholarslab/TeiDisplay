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
        'after_save_item'
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
            `sheet_id`  int(10) unsigned NOT NULL,

             PRIMARY KEY (`id`)

        ) ENGINE=innodb DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $this->_db->query($sql);

        // Whitelist XML extension.
        $extensions = get_option('file_extension_whitelist');
        if (strpos($extensions, 'xml') === false) {
            set_option('file_extension_whitelist',
              get_option('file_extension_whitelist').',xml');
        }

        // Whitelist XML mime type.
        $mimes = get_option('file_mime_type_whitelist');
        if (strpos($mimes, 'application/xml') === false) {
            set_option('file_mime_type_whitelist',
              get_option('file_mime_type_whitelist').',application/xml');
        }

    }

    /**
     * Drop tables.
     *
     * @return void.
     */
    public function hookUninstall()
    {

        // Drop stylesheets.
        $tableName = $this->_db->TeiDisplayStylesheet;
        $sql = "DROP TABLE IF EXISTS `$tableName`";
        $this->_db->query($sql);

        // Drop texts.
        $tableName = $this->_db->TeiDisplayText;
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
     * Process Item add/edit TEI tab.
     *
     * @param array $args The hook arguments, with keys 'record'
     * and 'post'.
     *
     * @return void.
     */
    public function hookAfterSaveItem($args)
    {

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
        $tabs[] = array('label' => 'TEI', 'uri' => url('tei/stylesheets'));
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

        // Construct the form, strip the <form> tag.
        $form = new TeiDisplay_Form_Text();
        $form->removeDecorator('form');

        // Get the item.
        $item = get_current_record('item');

        // If the item is saved.
        if (!is_null($item->id)) {

            // Set texts for the item.
            $form->setTextsForSelect($item);

            // // Try to get a datastream.
            // $object = $this->_objects->findByItem($item);

            // // Populate fields.
            // if ($object) {
            //     $form->populate(array(
            //         'server' => $object->server_id,
            //         'pid' => $object->pid,
            //         'saved-dsids' => $object->dsids
            //     ));
            // }

        }

        // Add tab.
        $tabs['TEI'] = $form;
        return $tabs;

    }

}
