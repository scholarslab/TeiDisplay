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
        'after_save_item',
        'config_form',
        'config'
    );

    // Filters.
    protected $_filters = array(
        'admin_navigation_main',
        'admin_items_form_tabs',
        'action_contexts',
        'response_contexts'
    );

    // XML mime types.
    protected $_xmlMimeTypes = array(
        'application/xml',
        'text/xml'
    );

    /**
     * Get tables.
     *
     * @return void.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_texts = $this->_db->getTable('TeiDisplayText');
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
     * Render configuration form.
     *
     * @return void.
     */
    public function hookConfigForm()
    {
        $form = new TeiDisplay_Form_Header();
        $form->removeDecorator('form');
        echo $form;
    }

    /**
     * Save configuration form.
     *
     * @return void.
     */
    public function hookConfig($args)
    {
        $this->_texts->saveTeiMappings($args['post']);
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

        // If post is defined.
        if ($args['post']) { // TODO: wtf?

            // Create or update the text.
            $text = $this->_texts->createOrUpdate($args['record'],
                (int) $args['record']['teistylesheet'],
                (int) $args['record']['teitext']
            );

            // Import header.
            if ((bool) $args['record']['teiimport']) {
                $this->_texts->importTeiHeader($args['record']);
            }

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

        // Construct the form.
        $item = get_current_record('item');
        $form = new TeiDisplay_Form_Text(array('item' => $item));
        $form->removeDecorator('form');

        // If the item exists.
        if ($item->exists()) {

            // Try to get a text.
            $text = $this->_texts->findByItem($item);

            // Populate fields.
            if ($text) $form->populate(array(
                'teitext' => $text->file_id,
                'teistylesheet' => $text->sheet_id
            ));

        }

        // Add tab.
        $tabs['TEI'] = $form;
        return $tabs;

    }

    /**
     * Register `tei` action context.
     *
     * @param array $contexts The action contexts.
     *
     * @return array $contexts The modified array.
     */
    public function filterActionContexts($contexts)
    {
        $contexts['show'][] = 'tei';
        return $contexts;
    }

    /**
     * Register the `tei` response context.
     *
     * @param array $context The current context.
     *
     * @return array $context The modified context.
     */
    public function filterResponseContexts($contexts)
    {

        $contexts['tei'] = array(
            'suffix' => 'tei',
            'headers' => array('Content-Type' => 'text/html')
        );

        return $contexts;

    }

}
