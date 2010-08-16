<?php

/**
 * TeiDisplay plugin
 *
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    $Id:$
 * @package TeiDisplay
 * @author Ethan Gruber - ewg4x at virginia.edu
 **/

define('TEI_DISPLAY_DIRECTORY', dirname(__FILE__));
define('TEI_DISPLAY_STYLESHEET_FOLDER', TEI_DISPLAY_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR);

add_plugin_hook('install', 'tei_display_install');
add_plugin_hook('uninstall', 'tei_display_uninstall');
add_plugin_hook('after_save_item', 'tei_display_after_save_item');
add_plugin_hook('before_delete_item', 'tei_display_before_delete_item');
add_plugin_hook('config_form', 'tei_display_config_form');
add_plugin_hook('config', 'tei_display_config');
add_plugin_hook('define_acl', 'tei_display_define_acl');
add_plugin_hook('admin_theme_header', 'tei_display_admin_header');
add_plugin_hook('public_theme_header', 'tei_display_public_header');
add_filter('admin_navigation_main', 'tei_display_admin_navigation');

function tei_display_install()
{
	$db = get_db();
	try {
		$xh = new XSLTProcessor; // we check for the ability to use XSLT
		//add TEI Lite XML item type to list
		$db->insert('item_types', array('name'=>'TEI XML',
										'description'=>'Text Encoding Initiative-compatible XML file'));
	
		set_option('tei_display_type', 'entire');
		set_option('tei_default_stylesheet', 'default.xsl');
		
		// create for facet mapping
		$db->exec("CREATE TABLE IF NOT EXISTS `{$db->prefix}tei_display_configs` (
			`id` int(10) unsigned NOT NULL auto_increment,
			`item_id` int(10) unsigned,
			`tei_id` tinytext collate utf8_unicode_ci,
			`stylesheet` tinytext collate utf8_unicode_ci,	      
			`display_type` tinytext collate utf8_unicode_ci,	    
	       PRIMARY KEY  (`id`)
	       ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
	} catch (Exception $e) {
		throw new Zend_Exception("This plugin requires XSLT support");
	}
}

function tei_display_uninstall(){
	$db = get_db();
	$itemType = get_db()->getTable('ItemType')->findByName('TEI XML');
	$itemType->delete();
	$sql = "DROP TABLE IF EXISTS `{$db->prefix}tei_display_configs`";
	$db->query($sql);
	
	//delete option
	delete_option('tei_display_type');
	delete_option('tei_default_stylesheet');
}

function tei_display_after_save_item($item)
{
	$db = get_db();
	$itemTypeId = $db->getTable('ItemType')->findByName('TEI XML')->id;
	if ($item->Files && $item['item_type_id'] == $itemTypeId){
	
		//declare DomDocument and load the TEI file and declare xpath
		$xml_doc = new DomDocument;
		$file = $db->getTable('File')->findBySql('item_id = ?', array($item['id']));	
		$teiFile = $file[0]->getWebPath('archive');
		$xml_doc->load($teiFile);
		$xpath = new DOMXPath($xml_doc);
		
		
		//get element_ids
		$dcSetId = $db->getTable('ElementSet')->findByName('Dublin Core')->id;
		$dcElements = $db->getTable('Element')->findBySql('element_set_id = ?', array($dcSetId));
		$dc = array();
		
		//write DC element names and ids to new array for processing
		foreach ($dcElements as $dcElement){
			$dc[] = $dcElement['name'];
		}
		
		//map TEI to DC
		//based on CDL encoding guidelines: http://www.cdlib.org/groups/stwg/META_BPG.html#d52e344
		foreach ($dc as $name){
			if ($name == 'Title'){
				$queries = array('//teiHeader/fileDesc/titleStmt/title');
			} elseif ($name == 'Creator'){
				$queries = array('//teiHeader/fileDesc/titleStmt/author');
			} elseif ($name == 'Subject'){
				$queries = array(	'//teiHeader/profileDesc/textClass/keywords/list/item');
			} elseif ($name == 'Description'){
				$queries = array(	'//teiHeader/encodingDesc/refsDecl',
									'//teiHeader/encodingDesc/projectDesc',
									'//teiHeader/encodingDesc/editorialDesc');
			} elseif ($name == 'Publisher'){
				$queries = array(	'//teiHeader/fileDesc/publicationStmt/publisher/publisher',
									'//teiHeader/fileDesc/publicationStmt/publisher/pubPlace');
			} elseif ($name == 'Contributor'){
				$queries = array(	'//teiHeader/fileDesc/titleStmt/editor',
									'//teiHeader/fileDesc/titleStmt/funder',
									'//teiHeader/fileDesc/titleStmt/sponsor',
									'//teiHeader/fileDesc/titleStmt/principle');
			} elseif ($name == 'Date'){
				$queries = array(	'//teiHeader/fileDesc/publicationStmt/date');
			} elseif ($name == 'Type'){
				//skip type, defined with Item Type Metadata dropdown
				$queries = array();				
			} elseif ($name == 'Format'){
				//skip format, added manually as text/xml below
				$queries == array();
			} elseif ($name == 'Identifier'){
				$queries = array(	'//teiHeader/fileDesc/publicationStmt/idno[@type="ARK"]');
			} elseif ($name == 'Source'){
				$queries = array(	'//teiHeader/sourceDesc/bibful/publicationStmt/publisher',
									'//teiHeader/sourceDesc/bibful/publicationStmt/pubPlace',
									'//teiHeader/sourceDesc/bibful/publicationStmt/date',
									'//teiHeader/sourceDesc/bibl');
			} elseif ($name == 'Language'){
				$queries = array(	'//teiHeader/profileDesc/langUsage/language');
			} elseif ($name == 'Relation'){
				$queries = array(	'//teiHeader/fileDesc/seriesStmt/title');
			} elseif ($name == 'Coverage'){
				//skip coverage, there is no clear mapping from TEI Header to Dublin Core
				$queries == array();
			} elseif ($name == 'Rights'){
				$queries == array('//teiheader/fileDesc/publicationStmt/availability');
			}
			
			$element = $item->getElementByNameAndSetName($name, 'Dublin Core');
			$elementTexts = $item->getTextsByElement($element);
			$texts = array();
			foreach ($elementTexts as $elementText){
				$texts[] = $elementText['text'];
			}
			
			foreach ($queries as $query){
				$nodes = $xpath->query($query);
				foreach ($nodes as $node){					
					//see if that text is already set and don't put in any blank or null fields
					$value = preg_replace('/\s\s+/', ' ', trim($node->nodeValue));
					if (!in_array(trim($value), $texts) && trim($value) != '' && trim($value) != NULL){
						$item->addTextForElement($element, trim($value));
					}
				}
			}
		}
		//add format as text/xml separately since it is not extracted from the TEI Header
		$element = $item->getElementByNameAndSetName('Format', 'Dublin Core');
		$elementTexts = $item->getTextsByElement($element);
		$texts = array();
		foreach ($elementTexts as $elementText){
			$texts[] = $elementText['text'];
		}
		if (!in_array('text/xml', $texts)){
			$item->addTextForElement($element, 'text/xml');
		}
		$item->saveElementTexts();
		$tei2 = $xml_doc->getElementsByTagName('TEI.2');
		foreach ($tei2 as $tei2){
			$tei_id = $tei2->getAttribute('id');
		}
		
		
		//finally, add the file to the tei_display_config table if it isn't already there
		$configs = $db->getTable('TeiDisplay_Config')->findAll();
		$configTeiIds = array();
		foreach ($configs as $config){
			$configTeiIds[] = $config['tei_id'];
		}
		if (!in_array(trim($tei_id), $configTeiIds)){
			$db->insert('tei_display_config', array('item_id'=>$item['id'], 'tei_id'=>trim($tei_id)));
		}
	}
}

function tei_display_before_delete_item($item)
{
	$db = get_db();
	$itemTypeId = $db->getTable('ItemType')->findByName('TEI XML')->id;
	if ($item->Files && $item['item_type_id'] == $itemTypeId){
		$files = $db->getTable('TeiDisplay_Config')->findBySql('item_id = ?', array($item['id']));
		foreach ($files as $file){
			$file->delete();
		}
	}
}

function tei_display_define_acl($acl)
{
    $acl->loadResourceList(array('TeiDisplay_Config' => array('browse', 'status')));
}

function tei_display_admin_navigation($tabs)
{
    if (get_acl()->checkUserPermission('TeiDisplay_Config', 'index')) {
        $tabs['TEI Config'] = uri('tei-display/config/');        
    }
    return $tabs;
}

function tei_display_admin_header($request)
{
	if ($request->getModuleName() == 'tei-display') {
		echo '<link rel="stylesheet" href="' . html_escape(css('tei_display_main')) . '" />';
    }
}

function tei_display_public_header($request)
{
	echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
	echo '<link rel="stylesheet" media="screen" href="' . WEB_PLUGIN . '/TeiDisplay/views/public/css/tei_display_public.css"/>';
	echo js('tei_display_toggle_toc');
}

//set TEI Display type
function tei_display_config_form()
{
	$form = tei_display_options();
	?>
	<style type="text/css">.zend_form>dd{ margin-bottom:20px; }</style>
	<div class="field">
		<h3>TEI Display Type</h3>
		<p class="explanation">There are two display types: Segmental or Entire Document.  
		The segmental display incorporates a table of contents with links to display sections of the TEI document 
		(generally div1 or div2).  This is perhaps the most appropriate mode for extremely large TEI documents 
		that consist of hundreds of pages, especially those that include references to figure images.  The Entire Document display 
		renders the entire TEI document in HTML form.</p>
		<? echo $form; ?>
	</div>
<?php
}

//post displable fields to index
function tei_display_config(){
	$form = tei_display_options();
    if ($form->isValid($_POST)) {    
    	//get posted values		
		$uploadedData = $form->getValues();
		
		//cycle through each checkbox
		foreach ($uploadedData as $k => $v){
			if ($k != 'submit'){
				set_option($k, $v);
			}		
		}
    }
}

/*********
 * Displayable element form
 *********/
function tei_display_options(){
	$xslFiles = TeiDisplay_File::getFiles();

    require "Zend/Form/Element.php";
    $form = new Zend_Form();  	
    $form->setMethod('post');
    $form->setAttrib('enctype', 'multipart/form-data');	
    
    $teiDisplay = new Zend_Form_Element_Select ('tei_display_type');
    $teiDisplay->setLabel('Display Type:');
    $teiDisplay->addMultiOption('entire', 'Entire Document');
	$teiDisplay->addMultiOption('segmental', 'Segmental');    
    $teiDisplay->setValue(get_option('tei_display_type'));
    $form->addElement($teiDisplay);
    
    //default stylesheet
    $stylesheet = new Zend_Form_Element_Select('tei_default_stylesheet');
    $stylesheet->setLabel('Default Stylesheet:');
	$stylesheet->setValue(get_option('tei_default_stylesheet'));
	
    foreach ($xslFiles as $xslFile) {
		$stylesheet->addMultiOption($xslFile, $xslFile);
    }
	$form->addElement($stylesheet);
    
    
    
    return $form;
}

/******************************
 * Public plugin functions
 ******************************/
	
function render_tei_file($item_id, $section){
	//query for file-specific stylesheet and display_type. use default from option table if NULL
	$stylesheet = tei_display_local_stylesheet($item_id);
	$displayType = tei_display_local_display($item_id);

	$xp = new XsltProcessor();
	// create a DOM document and load the XSL stylesheet
	$xsl = new DomDocument;

	// import the XSL styelsheet into the XSLT process
	$xsl->load($stylesheet);
	$xp->importStylesheet($xsl);
	
	//set query parameter to pass into stylesheet
	$xp->setParameter('', 'display', $displayType);
	$xp->setParameter('', 'section', $section);
	
	// create a DOM document and load the XML data
	$xml_doc = new DomDocument;
	
	$db = get_db();
	$file = $db->getTable('File')->findBySql('item_id = ?', array($item_id));
	$teiFile = $file[0]->getWebPath('archive');
	
	$xml_doc->load($teiFile);
	
	try { 
		if ($doc = $xp->transformToXML($xml_doc)) {			
			echo $doc;
		}
	} catch (Exception $e){
		$this->view->error = $e->getMessage();
	}
}

function tei_display_get_title($item_id){
	$item = get_item_by_id($item_id);
	return strip_formatting(item('Dublin Core', 'Title', $options, $item));
}

function tei_display_local_stylesheet($item_id){
	$db = get_db();
	$results = $db->getTable('TeiDisplay_Config')->findBySql('item_id = ?', array($item_id));
	if ($results[0]->stylesheet != NULL && $results[0]->stylesheet != ''){
		return TEI_DISPLAY_STYLESHEET_FOLDER . $results[0]->stylesheet;
	} else {
		return TEI_DISPLAY_STYLESHEET_FOLDER . get_option('tei_default_stylesheet');
	}
	
}
function tei_display_local_display($item_id){
	$db = get_db();
	$results = $db->getTable('TeiDisplay_Config')->findBySql('item_id = ?', array($item_id));
	if ($results[0]->display_type != NULL && $results[0]->display_type != ''){
		return $results[0]->display_type;
	} else {
		return get_option('tei_display_type');
	}
}