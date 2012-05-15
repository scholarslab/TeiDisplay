<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * CsvImport_Import - represents a csv import event
 * 
 * @version $Id$ 
 * @package TeiDisplay
 * @author Ethan Gruber
 * @copyright Scholars' Lab
 *
 **/
class TeiDisplay_Config extends Omeka_Record { 
	public static function getConfig($currentPage)
	{
		$db = get_db();
		$et = $db->getTable('TeiDisplay_Config');
		$entries = $et->findBy(array('id'=>'id'), 10, $currentPage);
        return $entries;
	}
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
