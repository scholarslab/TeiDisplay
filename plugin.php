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
define('TEI_DISPLAY_P4_STYLESHEET', TEI_DISPLAY_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'tei_p4.xsl');

add_plugin_hook('install', 'tei_display_install');
add_plugin_hook('uninstall', 'tei_display_uninstall');
add_plugin_hook('after_save_item', 'tei_display_after_save_item');
add_plugin_hook('config_form', 'tei_display_config_form');
add_plugin_hook('config', 'tei_display_config');

function tei_display_install()
{
	$db = get_db();

	try {
		$xh = new XSLTProcessor; // we check for the ability to use XSLT
		//add TEI Lite XML item type to list
		$db->insert('item_types', array('name'=>'TEI XML',
										'description'=>'Text Encoding Initiative-compatible XML file'));
	
		set_option('tei_display_type', 'entire');
	} catch (Exception $e) {
		throw new Zend_Exception("This plugin requires XSLT support");
	}
}

function tei_display_uninstall(){
	$db = get_db();
	$itemType = get_db()->getTable('ItemType')->findByName('TEI XML');
	$itemType->delete();
	
	//delete option
	delete_option('tei_display_type');
}

function tei_display_after_save_item($item)
{
	if ($item->Files &&  item('Item Type Name') == 'TEI XML'){
		//get TEI file
		$db = get_db();
		$fileId = $db->getTable('File')->findBySql('item_id = ?', array($item['id']));
		$teiFile = WEB_ROOT . '/files/display/' . $fileId[0]->id . '/fullsize';
		$doc = simplexml_load_file($teiFile);
		
		//get title
		$title = $doc->xpath('//teiHeader/fileDesc/titleStmt/title');
		
	}
}

function solr_search_admin_navigation($tabs)
{
    if (get_acl()->checkUserPermission('TeiDisplay', 'index')) {
        $tabs['Upload TEI File'] = uri('tei-display/upload/');        
    }
    return $tabs;
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
    
    
    return $form;
}

/******************************
 * Public plugin functions
 ******************************/
	
function render_tei_file($item_id, $stylesheet=TEI_DISPLAY_P4_STYLESHEET){
	$xp = new XsltProcessor();
	// create a DOM document and load the XSL stylesheet
	$xsl = new DomDocument;
	$xsl->load($stylesheet);
  
	// import the XSL styelsheet into the XSLT process
	$xp->importStylesheet($xsl);
	//set query parameter to pass into stylesheet
	$xp->setParameter('', 'display', get_option('tei_display_type'));
	
	// create a DOM document and load the XML data
	$xml_doc = new DomDocument;
	
	$db = get_db();
	$fileId = $db->getTable('File')->findBySql('item_id = ?', array($item_id));
	
	$teiFile = WEB_ROOT . '/files/display/' . $fileId[0]->id . '/fullsize';
	
	$xml_doc->load($teiFile);
	
	try { 
		if ($doc = $xp->transformToXML($xml_doc)) {			
			echo $doc;
		}
	} catch (Exception $e){
		$this->view->error = $e->getMessage();
	}
}