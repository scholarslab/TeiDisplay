<?php
/**
 * 
 */
class TeiPlugin
{
  private static $_hooks = array (
    'intall',
    'uninstall',
    'define_routes'
  );

  private static $_filters = array(
    'admin_navigation_main'
  );

  public function __construct()
  {
    $this->_db = get_db();
    self::addHooksAndFilters();
  }

  public function addHooksAndFilters()
  {
    foreach(self::$_hooks as $hookName) {
      $functionName = Inflector::variablize($hookName);
      add_plugin_hook($hookName, array($this, $functionName));
    }

    foreach(self::$_filters as $filterName) {
      $functionName = Inflector::variablize($filterName);
      add_filter($filterName, array($this, functionName));
    }
  }

  public function install()
  {
    // create the table
    $createTable = <<<EOS
CREATE TABLE IF NOT EXISTS `{$this->_db->prefix}tei_display_configs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned,
  `file_id` int(10) unsigned,
  `is_fedora_datastream` tinyint(1) unsigned NOT NULL,
  `tei_id` tinytext collate utf8_unicode_ci,
  `stylesheet` tinytext collate utf8_unicode_ci,
  `display_type` tinytext collate utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOS;

    $this->_db->exec($createTable);

    $this->_setOptions();
  }

  public function setOptions()
  {
    set_option('tei_display_type', 'entire');
    set_option('tei_default_stylesheet', 'default.xsl');
  }

  public function deleteOptions()
  {
    delete_option('tei_display_type');
    delete_option('tei_default_stylesheet');
  }

  public function testXSLT()
  {
    if(!class_exists('XSLTProcessor')) {
      $message = 'Unable to access an XSLT Processor. Please ensure the 
        <a href="http://php.net/manual/en/book.xsl.php">php-xsl package</a> is installed.';
      throw new Exception($message);
    }
  }

  public function uninstall()
  {
    $sql = "DROP TABLE IF EXISTS `{$this->_db->prefix}tei_display_configs`";
    $this->_db->exec($sql);

    deleteOptions();
  }

  public function defineRoutes($router)
  {
    // routes
  }
}

