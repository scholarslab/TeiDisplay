<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * TeiDisplay Plugin
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class TeiDisplayPlugin
{
  private static $_hooks = array (
    'intall',
    'uninstall',
    'define_acl',
    'after_save_item',
    'before_delete_item',
    'config_form',
    'config',
    'admin_theme_header',
    'public_theme_header'
  );

  private static $_filters = array(
    'admin_navigation_main'
  );

  private static $_db;

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

  function tei_display_install()
  {
    $db = get_db();
    if (!class_exists('XSLTProcessor')) {
      throw new Exception('Unable to access XSLTProcessor class.  Make sure the php-xsl package is installed.');
    } else{
      $xh = new XSLTProcessor; // we check for the ability to use XSLT	
      set_option('tei_display_type', 'entire');
      set_option('tei_default_stylesheet', 'default.xsl');

      // create for facet mapping
      $db->exec("CREATE TABLE IF NOT EXISTS `{$db->prefix}tei_display_configs` (
        `id` int(10) unsigned NOT NULL auto_increment,
        `item_id` int(10) unsigned,
        `file_id` int(10) unsigned,
        `is_fedora_datastream` tinyint(1) unsigned NOT NULL,
        `fedoraconnector_id` int(10) unsigned,
        `tei_id` tinytext collate utf8_unicode_ci,
        `stylesheet` tinytext collate utf8_unicode_ci,	      
        `display_type` tinytext collate utf8_unicode_ci,	    
        PRIMARY KEY  (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

      //repopulate the tei_display_config table with existing TEI Document typed files upon plugin reinstallation
      $files = $db->getTable('File')->findBySql('mime_browser = ?', array('application/xml'));
      foreach ($files as $file){
        $xml_doc = new DomDocument;	
        $teiFile = $file->getWebPath('archive');
        $xml_doc->load($teiFile);
        $tei2 = $xml_doc->getElementsByTagName('TEI.2');
        foreach ($tei2 as $tei2){
          $tei_id = $tei2->getAttribute('id');
        }
        if ($tei_id != NULL && $tei_id != ''){
          $db->insert('tei_display_config', array('item_id'=>$file->item_id, 'file_id'=>$file->id, 'tei_id'=>trim($tei_id)));
        }
      }

      //repopulate the tei_display_config_table with existing TEI datastreams from Fedora if FedoraConnector is installed
      //change datastream from 'TEI' to another string, if applicable
      if (function_exists('fedora_connector_installed')){
        $datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('datastream = ?', array('TEI'));
        foreach ($datastreams as $datastream){
          $teiFile = fedora_connector_content_url($datastream);
          //get the TEI id
          $xml_doc = new DomDocument;									
          $xml_doc->load($teiFile);
          $xpath = new DOMXPath($xml_doc);

          $teiNode = $xml_doc->getElementsByTagName('TEI');
          $tei2Node = $xml_doc->getElementsByTagName('TEI.2');

          foreach ($teiNode as $teiNode){
            $p5_id = $teiNode->getAttribute('xml:id');
          } 				
          foreach ($tei2Node as $tei2Node){
            $p4_id = $tei2Node->getAttribute('id');
          }

          if (isset($p5_id)){
            $tei_id = $p5_id;
          } else if (isset($p4_id)){
            $tei_id = $p4_id;
          } else {
            $tei_id = NULL;
          }

          if ($tei_id != NULL){
            $teiData = array('item_id'=>$datastream->item_id, 'is_fedora_datastream'=>1, 'fedoraconnector_id'=>$datastream->id, 'tei_id'=>$tei_id);
            $db->insert('tei_display_configs', $teiData);
          }
        }
      }
    }
  }

  function tei_display_uninstall(){
    $db = get_db();
    $sql = "DROP TABLE IF EXISTS `{$db->prefix}tei_display_configs`";
    $db->query($sql);

    //delete option
    delete_option('tei_display_type');
    delete_option('tei_default_stylesheet');
  }

  function tei_display_after_save_item($item)
  {
    $db = get_db();
    $files = $item->Files;
    foreach ($files as $file){
      $mimeType = $file->mime_browser;
      if ($mimeType == 'application/xml' || $mimeType == 'text/xml'){
        //declare DomDocument and load the TEI file and declare xpath
        $xml_doc = new DomDocument;	
        $teiFile = $file->getWebPath('archive');
        $xml_doc->load($teiFile);
        $xpath = new DOMXPath($xml_doc);

        $teiNode = $xml_doc->getElementsByTagName('TEI');
        $tei2Node = $xml_doc->getElementsByTagName('TEI.2');

        foreach ($teiNode as $teiNode){
          $p5_id = $teiNode->getAttribute('xml:id');
        } 				
        foreach ($tei2Node as $tei2Node){
          $p4_id = $tei2Node->getAttribute('id');
        }

        if (isset($p5_id)){
          $tei_id = $p5_id;
        } else if (isset($p4_id)){
          $tei_id = $p4_id;
        } else {
          $tei_id = NULL;
        }

        if ($tei_id != NULL){
          //add the file to the tei_display_config table if it isn't already there
          $configs = $db->getTable('TeiDisplay_Config')->findAll();
          $configTeiIds = array();
          foreach ($configs as $config){
            $configTeiIds[] = $config['tei_id'];
          }
          if (!in_array(trim($tei_id), $configTeiIds)){
            $db->insert('tei_display_config', array('item_id'=>$item->id, 'file_id'=>$file->id, 'tei_id'=>trim($tei_id)));
          }

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
              $queries = array('//*[local-name() = "teiHeader"]/*[local-name() = "fileDesc"]/*[local-name() = "titleStmt"]/*[local-name() = "title"]');

            } elseif ($name == 'Creator'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="author"]');

            } elseif ($name == 'Subject'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="textClass"]/*[local-name()="keywords"]/*[local-name()="list"]/*[local-name()="item"]',
                '//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="textClass"]/*[local-name()="keywords"]/*[local-name()="term"]');

            } elseif ($name == 'Description'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="projectDesc"]',
                '//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="samplingDecl"]',
                '//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="editorialDecl"]',
                '//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="refsDecl"]'

              );

            } elseif ($name == 'Publisher'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="publisher"]',
                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="pubPlace"]',

              );

            } elseif ($name == 'Contributor'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="editor"]',
                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="funder"]',
                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="sponsor"]',
                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="principal"]',
                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="respStmt"]/*[local-name()="name"]'

              );
            } elseif ($name == 'Date'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="date"]'

              );
            } elseif ($name == 'Type'){
              //type, defined with Item Type Metadata dropdown
              $queries = array();				
            } elseif ($name == 'Format'){
              //format, added manually as text/*[local-name()="xml"] below
              $queries = array();
            } elseif ($name == 'Identifier'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="idno"]'

              );
            } elseif ($name == 'Source'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="biblFull"]/*[local-name()="publicationStmt"]/*[local-name()="publisher"]',
                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="biblFull"]/*[local-name()="publicationStmt"]/*[local-name()="pubPlace"]',
                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="biblFull"]/*[local-name()="publicationStmt"]/*[local-name()="date"]',

                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="bibl"]'
              );					
            } elseif ($name == 'Language'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="langUsage"]/*[local-name()="language"]'

              );
            } elseif ($name == 'Relation'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="seriesStmt"]/*[local-name()="title"]'

              );
            } elseif ($name == 'Coverage'){
              //skip coverage, there is no clear mapping from TEI Header to Dublin Core
              $queries = array();
            } 

            elseif ($name == 'Rights'){
              $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="availability"]'

              );
            }

            //get item element texts
            $ielement = $item->getElementByNameAndSetName($name, 'Dublin Core');
            $ielementTexts = $item->getTextsByElement($ielement);
            $itexts = array();
            foreach ($ielementTexts as $ielementText){
              $itexts[] = $ielementText['text'];
            }

            //get file element texts
            $felement = $file->getElementByNameAndSetName($name, 'Dublin Core');
            $felementTexts = $file->getTextsByElement($felement);
            $ftexts = array();
            foreach ($felementTexts as $felementText){
              $ftexts[] = $felementText['text'];
            }

            //set element texts for item and file
            foreach ($queries as $query){
              $nodes = $xpath->query($query);
              foreach ($nodes as $node){					
                //see if that text is already set and don't put in any blank or null fields
                $value = preg_replace('/\s\s+/', ' ', trim($node->nodeValue));

                //item
                if (!in_array(trim($value), $itexts) && trim($value) != '' && trim($value) != NULL){
                  $item->addTextForElement($ielement, trim($value));
                }

                //file
                if (!in_array(trim($value), $ftexts) && trim($value) != '' && trim($value) != NULL){
                  $file->addTextForElement($felement, trim($value));
                }
              }
            }

            //set element texts for file
          }
          //set TEI Document type on TEI XML file
          $element = $file->getElementByNameAndSetName('Type', 'Dublin Core');
          $elementTexts = $file->getTextsByElement($element);
          $texts = array();
          foreach ($elementTexts as $elementText){
            $texts[] = $elementText['text'];
          }
          if (!in_array('TEI Document', $texts)){
            $file->addTextForElement($element, 'TEI Document');
          }
          $item->saveElementTexts();
          $file->saveElementTexts();
        }
      }
    }
  }


  function tei_display_before_delete_item($item)
  {
    $db = get_db();
    $files = $db->getTable('TeiDisplay_Config')->findBySql('item_id = ?', array($item['id']));
    foreach ($files as $file){
      $file->delete();
    }
  }

  function defineAcl($acl)
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

  function adminThemeHeader($request)
  {
    if ($request->getModuleName() == 'tei-display') {
      echo '<link rel="stylesheet" href="' . html_escape(css('tei_display_main')) . '" />';
    }
  }

  function publicThemeHeader($request)
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
   */ 
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
   */
  function tei_display_installed(){
    return 'active';
  }

  function render_tei_files($item_id, $section){
    $db = get_db();
    $item = $db->getTable('Item')->find($item_id);
    $hasTeiFile = array();
    foreach ($item->Files as $file){
      if (trim(strip_formatting(item_file('Dublin Core', 'Type', $options, $file)) == 'TEI Document')){
        $hasTeiFile[] = 'true';
      }
    }
    if (in_array('true', $hasTeiFile)){
      $teiFiles = $db->getTable('TeiDisplay_Config')->findBySql('item_id = ?', array($item_id));
      foreach ($teiFiles as $teiFile){
        render_tei_file($teiFile->id, $section);
      }
    }
  }

  function render_tei_file($identifier, $section){
    $db = get_db();
    $teiRecord = $db->getTable('TeiDisplay_Config')->find($identifier);
    //initialize Dom xslt, xml documents
    $xp = new XsltProcessor();
    $xsl = new DomDocument;
    $xml_doc = new DomDocument;

    if ($teiRecord->file_id != NULL){
      $file_id = $teiRecord->file_id;
      $teiFile = $db->getTable('File')->find($file_id)->getWebPath('archive');
    } 
    //render TEI file from Fedora.
    if (function_exists('fedora_connector_installed')){
      if ($teiRecord->fedoraconnector_id != NULL){
        $pid = $teiRecord->fedoraconnector_id;
        $datastream = $db->getTable('FedoraConnector_Datastream')->find($pid);
        $teiFile = fedora_connector_content_url($datastream);		
      }
    }

    $stylesheet = tei_display_local_stylesheet($teiRecord->id);
    $displayType = tei_display_local_display($teiRecord->id);

    $xml_doc->load($teiFile);

    $xsl->load($stylesheet);
    $xp->importStylesheet($xsl);

    //set query parameter to pass into stylesheet
    $xp->setParameter('', 'display', $displayType);
    $xp->setParameter('', 'section', $section);

    try { 
      if ($doc = $xp->transformToXML($xml_doc)) {			
        echo $doc;
      }
    } catch (Exception $e){
      $this->view->error = $e->getMessage();
    }
  }

  function tei_display_get_title($id){
    $db = get_db();
    $teiFile = $db->getTable('TeiDisplay_Config')->find($id);

    if ($teiFile->file_id != NULL){
      $file = $db->getTable('File')->find($teiFile->file_id);
      return strip_formatting(item_file('Dublin Core', 'Title', $options, $file));
    }
    if ($teiFile->fedoraconnector_id != NULL){
      $item = $db->getTable('Item')->find($teiFile->item_id);
      return strip_formatting(item('Dublin Core', 'Title', $options, $item));
    }
  }

  function tei_display_local_stylesheet($id){
    $db = get_db();
    $teiFile = $db->getTable('TeiDisplay_Config')->find($id);
    if ($teiFile->stylesheet != NULL && $teiFile->stylesheet != ''){
      return TEI_DISPLAY_STYLESHEET_FOLDER . $teiFile->stylesheet;
    } else {
      return TEI_DISPLAY_STYLESHEET_FOLDER . get_option('tei_default_stylesheet');
    }

  }
  function tei_display_local_display($id){
    $db = get_db();
    $teiFile = $db->getTable('TeiDisplay_Config')->find($id);
    if ($teiFile->display_type != NULL && $teiFile->display_type != ''){
      return $teiFile->display_type;
    } else {
      return get_option('tei_display_type');
    }
  }
}
