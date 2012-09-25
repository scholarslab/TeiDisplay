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
        'define_routes'
    );

    // Filters.
    protected $_filters = array(
        'admin_navigation_main'
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
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->_db->prefix}stylesheets` (
            `id`    int(10) unsigned not null auto_increment,
            `xslt`  TEXT COLLATE utf8_unicode_ci NULL,
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
        $sql = "DROP TABLE IF EXISTS `{$this->_db->prefix}stylesheets`";
        $this->_db->query($sql);
    }

    /**
     * Register routes.
     *
     * @param object $router Router passed in by the front controller.
     *
     * @return void
     */
    public function hookDefineRoutes($args)
    {

    }


    // --------
    // Filters.
    // --------


    /**
     * Add link to main admin menu bar.
     *
     * @param array $tabs This is an array of label => URI pairs.
     *
     * @return array The tabs array with the Neatline Maps tab.
     */
    public function filterAdminNavigationMain($tabs)
    {
        $tabs['TEI'] = url('tei');
        return $tabs;
    }

}
