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

function tei_display_after_save_item($item,$stylesheet=TEI_DISPLAY_P4_STYLESHEET)
{
	$db = get_db();
	$itemTypeId = $db->getTable('ItemType')->findByName('TEI XML')->id;
	if ($item->Files && $item['item_type_id'] == $itemTypeId){
	
		//declare DomDocument and load the TEI file and declare xpath
		$xml_doc = new DomDocument;
		$fileId = $db->getTable('File')->findBySql('item_id = ?', array($item->id));	
		$teiFile = FILES_DIR . DIRECTORY_SEPARATOR . $fileId[0]->archive_filename;	
		$xml_doc->load($teiFile);
		$xpath = new DOMXPath($xml_doc);
		
		
		//get element_ids
		$dcSetId = $db->getTable('ElementSet')->findByName('Dublin Core')->id;
		$dcElements = $db->getTable('Element')->findBySql('element_set_id = ?', array($dcSetId));
		$dc = array();
		
		//write DC element names and ids to new array for processing
		foreach ($dcElements as $dcElement){
			$dc[$dcElement['name']] = $dcElement['id'];
		}
		
		//map TEI to DC
		//based on CDL encoding guidelines: http://www.cdlib.org/groups/stwg/META_BPG.html#d52e344
		foreach ($dc as $name=>$id){
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
				$queries = array(	'//teiHeader/@type');
			} elseif ($name == 'Format'){
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
				$queries == array();
			} elseif ($name == 'Rights'){
				$queries == array('//teiheader/fileDesc/publicationStmt/availability');
			}
			
			foreach ($queries as $query){
				$nodes = $xpath->query($query);
				foreach ($nodes as $node){
					//see if that text is already set
					$elementTexts = $db->getTable('ElementText')->findBySql('record_id = ? AND element_id = ?', array($item->id, $id));
					$texts = array();
					foreach ($elementTexts as $elementText){
						$texts[] = $elementText['text'];
					}
					$myFile = "/tmp/test.txt";
					$fh = fopen($myFile, 'a') or die("can't open file");
					fwrite($fh, $texts[0] . ' ' . trim($node->nodeValue) . '|');
					fclose($fh);
					
					if (!in_array(trim($node->nodeValue), $texts)){
						$db->insert('element_texts', array(	'record_id'=>$item['id'],
												'record_type_id'=>'2',
												'element_id'=>$id,
												'html'=>0,
												'text'=>trim($node->nodeValue)));
					}
				}
			}
		}
	}
}

/*function tei_node_value($name,$node){
	if ($name == 'Format'){
		return 'text/xml';
	} else {
		return $node->nodeValue;
	}
}*/

function tei_display_admin_navigation($tabs)
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
	$teiFile = FILES_DIR . DIRECTORY_SEPARATOR . $fileId[0]->archive_filename;
	
	$xml_doc->load($teiFile);
	
	try { 
		if ($doc = $xp->transformToXML($xml_doc)) {			
			echo $doc;
		}
	} catch (Exception $e){
		$this->view->error = $e->getMessage();
	}
}