<?php
/* vim: :set expandtab tabstop=4 shiftwidth=4 softtabstop=4 */

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
 * @category   Plugins
 * @package    Omeka
 * @subpackage TeiDisplay
 * @author     Scholars' Lab <>
 * @copyright  2011 The Board and Visitors of the University of Virginia
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @link       https://github.com/scholarslab/TeiDisplay
 */
class TeiDisplayPlugin
{
    private static $_hooks = array(
        'install',
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
        // 'admin_navigation_main'
    );

    private static $_db;

    /**
     * Initialize the TeiDsiplay plugin
     *
     * @return void
     */
    public function __construct()
    {
        $this->_db = get_db();
        self::addHooksAndFilters();
    }

    /**
     * Add hooks and filters to the plugin
     *
     * @return void
     */
    public function addHooksAndFilters()
    {
        foreach (self::$_hooks as $hookName) {
            $functionName = Inflector::variablize($hookName);
            add_plugin_hook($hookName, array($this, $functionName));
        }

        foreach (self::$_filters as $filterName) {
            $functionName = Inflector::variablize($filterName);
            add_filter($filterName, array($this, $functionName));
        }
    }

    /**
     * install the teidisplay plugin
     *
     * @return void
     */
    public function install()
    {
        // Flow for this:

        $ddl = <<<DDL
CREATE TABLE IF NOT EXISTS `{$this->_db->prefix}tei_display_configs` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `item_id` int(10) unsigned,
    `file_id` int(10) unsigned,
    `is_fedora_datastream` tinyint(1) unsigned NOT NULL,
    `fedoraconnector_id` int(10) unsigned,
    `tei_id` tinytext collate utf8_unicode_ci,
    `stylesheet` tinytext collate utf8_unicode_ci,
    `display_type` tinytext collate utf8_unicode_ci,	
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DDL;

        $this->_db->exec($ddl);

        if (!self::xsltExists()) {
            throw new Exception(
                "XSLT processor is missing. Please ensure the php-xsl package is installed"
            );
        }

        self::setOptions();

        self::batchAddDocs();
        self::addFedoraItems();

    }

    /**
     * Uninstall the plugin
     *
     * @return void
     */
    public function uninstall()
    {
        $sql = "DROP TABLE IF EXISTS `{$this->_db->prefix}tei_display_configs`";
        $this->_db->query($sql);
        self::deleteOptions();
    }

    /**
     * Transform the item after save
     * 
     * @param OmekaItem $item Item to save
     *
     * @return void
     *
     */
    public function afterSaveItem($item)
    {
        $files = $item->Files;
        foreach ($files as $file) {
            $mimeType = $file->mime_browser;
            if ($mimeType == 'application/xml' || $mimeType == 'text/xml') {
                //declare DomDocument and load the TEI file and declare xpath
                $xml_doc = new DomDocument;	
                $teiFile = $file->getWebPath('archive');
                $xml_doc->load($teiFile);
                $xpath = new DOMXPath($xml_doc);

                $teiNode = $xml_doc->getElementsByTagName('TEI');
                $tei2Node = $xml_doc->getElementsByTagName('TEI.2');

                foreach ($teiNode as $teiNode) {
                    $p5_id = $teiNode->getAttribute('xml:id');
                }

                foreach ($tei2Node as $tei2Node) {
                    $p4_id = $tei2Node->getAttribute('id');
                }

                if (isset($p5_id)) {
                    $tei_id = $p5_id;
                } else if (isset($p4_id)) {
                    $tei_id = $p4_id;
                } else {
                    $tei_id = null;
                }

                if ($tei_id != null) {
                    //add the file to the tei_display_config table if it isn't 
                    //already there
                    $configs = $this->_db->getTable('TeiDisplay_Config')->findAll();

                    $configTeiIds = array();
                    foreach ($configs as $config) {
                        $configTeiIds[] = $config['tei_id'];
                    }

                    if (!in_array(trim($tei_id), $configTeiIds)) {
                        $this->_db->insert(
                            'tei_display_config', 
                            array(
                                'item_id' => $item->id, 
                                'file_id'=>$file->id, 
                                'tei_id'=>trim($tei_id)
                            )
                        );
                    }

                    //get element_ids
                    $dcSetId = $this->_db->getTable('ElementSet')->findByName(
                        'Dublin Core'
                    )->id;

                    $dcElements = $this->_db->getTable('Element')->findBySql(
                        'element_set_id = ?',
                        array($dcSetId)
                    );

                    $dc = array();

                    //write DC element names and ids to new array for processing
                    foreach ($dcElements as $dcElement) {
                        $dc[] = $dcElement['name'];
                    }

                    //map TEI to DC
                    //based on CDL encoding guidelines: http://www.cdlib.org/groups/stwg/META_BPG.html#d52e344
                    foreach ($dc as $name) {
                        if ($name == 'Title') {
                            $queries = array('//*[local-name() = "teiHeader"]/*[local-name() = "fileDesc"]/*[local-name() = "titleStmt"]/*[local-name() = "title"]');

                        } elseif ($name == 'Creator') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="author"]');

                        } elseif ($name == 'Subject') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="textClass"]/*[local-name()="keywords"]/*[local-name()="list"]/*[local-name()="item"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="textClass"]/*[local-name()="keywords"]/*[local-name()="term"]');

                        } elseif ($name == 'Description') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="projectDesc"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="samplingDecl"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="editorialDecl"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="refsDecl"]'

                            );

                        } elseif ($name == 'Publisher') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="publisher"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="pubPlace"]',

                            );

                        } elseif ($name == 'Contributor') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="editor"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="funder"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="sponsor"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="principal"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="respStmt"]/*[local-name()="name"]'

                            );
                        } elseif ($name == 'Date') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="date"]'

                            );
                        } elseif ($name == 'Type') {
                            //type, defined with Item Type Metadata dropdown
                            $queries = array();				
                        } elseif ($name == 'Format') {
                            //format, added manually as text/*[local-name()="xml"] below
                            $queries = array();
                        } elseif ($name == 'Identifier') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="idno"]'

                            );
                        } elseif ($name == 'Source') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="biblFull"]/*[local-name()="publicationStmt"]/*[local-name()="publisher"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="biblFull"]/*[local-name()="publicationStmt"]/*[local-name()="pubPlace"]',
                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="biblFull"]/*[local-name()="publicationStmt"]/*[local-name()="date"]',

                                '//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="sourceDesc"]/*[local-name()="bibl"]'
                            );					
                        } elseif ($name == 'Language') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="langUsage"]/*[local-name()="language"]'

                            );
                        } elseif ($name == 'Relation') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="seriesStmt"]/*[local-name()="title"]'

                            );
                        } elseif ($name == 'Coverage') {
                            //skip coverage, there is no clear mapping from TEI Header to Dublin Core
                            $queries = array();
                        } elseif ($name == 'Rights') {
                            $queries = array('//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="availability"]'

                            );
                        }

                        //get item element texts
                        $ielement = $item->getElementByNameAndSetName($name, 'Dublin Core');
                        $ielementTexts = $item->getTextsByElement($ielement);
                        $itexts = array();
                        foreach ($ielementTexts as $ielementText) {
                            $itexts[] = $ielementText['text'];
                        }

                        //get file element texts
                        $felement = $file->getElementByNameAndSetName($name, 'Dublin Core');
                        $felementTexts = $file->getTextsByElement($felement);
                        $ftexts = array();
                        foreach ($felementTexts as $felementText) {
                            $ftexts[] = $felementText['text'];
                        }

                        //set element texts for item and file
                        foreach ($queries as $query) {
                            $nodes = $xpath->query($query);
                            foreach ($nodes as $node) {					
                                //see if that text is already set and don't put in any blank or null fields
                                $value = preg_replace('/\s\s+/', ' ', trim($node->nodeValue));

                                //item
                                if (!in_array(trim($value), $itexts) && trim($value) != '' && trim($value) != null) {
                                    $item->addTextForElement($ielement, trim($value));
                                }

                                //file
                                if (!in_array(trim($value), $ftexts) && trim($value) != '' && trim($value) != null) {
                                    $file->addTextForElement($felement, trim($value));
                                }
                            }
                        }

                        //set element texts for file
                    }
                    //set TEI Document type on TEI XML file
                    $element = $file->getElementByNameAndSetName(
                        'Type',
                        'Dublin Core'
                    );

                    $elementTexts = $file->getTextsByElement($element);
                    $texts = array();

                    foreach ($elementTexts as $elementText) {
                        $texts[] = $elementText['text'];
                    }

                    if (!in_array('TEI Document', $texts)) {
                        $file->addTextForElement($element, 'TEI Document');
                    }
                    $item->saveElementTexts();
                    $file->saveElementTexts();
                }
            }
        }
    }

    /**
     * Deletes XML files before the item is deleted
     *
     * @param OmekaItem $item Omeka item
     *
     * @return void
     */
    public function beforeDeleteItem($item)
    {
        $files = $this->_db->getTable('TeiDisplay_Config')->findBySql(
            'item_id = ?',
            array($item['id'])
        );

        foreach ($files as $file) {
            $file->delete();
        }
    }

    /**
     * Define the ACLs
     *
     * @param ACL $acl ACL list
     *
     * @return void
     */
    public function defineAcl($acl)
    {
        $acl->loadResourceList(
            array(
                'TeiDisplay_Config' => array(
                    'browse',
                    'status'
                )
            )
        );
    }

    /**
     * Admin navigation
     *
     * @param string $tabs Tabs to append to
     *
     * @return void
     */
    public function adminNavigation($tabs)
    {
        if (get_acl()->checkUserPermission('TeiDisplay_Config', 'index')) {
            $tabs['TEI Config'] = uri('tei-display/config/');
        }
        return $tabs;
    }

    /**
     * Add CSS to admin theme header
     *
     * @param request $request Omeka request object
     *
     * @return void
     */
    public function adminThemeHeader($request)
    {
        if ($request->getModuleName() == 'tei-display') {
            queue_css('tei_display_main');
            //echo '<link rel="stylesheet" href="' . html_escape(css('tei_display_main')) . '" />';
        }
    }

    /**
     * Add to public header
     *
     * @param request $request Omeka request object
     *
     * @return void
     */
    public function publicThemeHeader($request)
    {
        //echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
        //echo '<link rel="stylesheet" media="screen" href="' . WEB_PLUGIN . '/TeiDisplay/views/public/css/tei_display_public.css"/>';
        //echo js('tei_display_toggle_toc');
        queue_css('tei_display_plublic.css');
        queue_js('tei_display_toggle_toc');
    }

    /**
     * Display the config form
     *
     * @return void
     */
    public function configForm()
    {
        $form = self::getDisplayOptions();

        $formText = <<<FORM
  <style type="text/css">.zend_form>dd{ margin-bottom:20px; }</style>
  <div class="field">
    <h3>TEI Display Type</h3>
    <p class="explanation">There are two display types: Segmental or Entire
Document. The segmental display incorporates a table of contents with links
to display sections of the TEI document (generally div1 or div2).  This is
perhaps the most appropriate mode for extremely large TEI documents that
consist of hundreds of pages, especially those that include references to
figure images.  The Entire Document display renders the entire TEI document
in HTML form.</p>
FORM;

        echo $formText . $form . '</div>';

    }

    /**
     * Display the config form
     *
     * @return voic
     */
    public function displayConfig()
    {
        $form = tei_display_options();
        if ($form->isValid($_POST)) {
            //get posted values		
            $uploadedData = $form->getValues();

            //cycle through each checkbox
            foreach ($uploadedData as $k => $v) {
                if ($k != 'submit') {
                    set_option($k, $v);
                }		
            }
        }
    }

    /**
     * Displayable element form
     *
     * @return form to display
     */
    public function getDisplayOptions()
    {
        $xslFiles = TeiDisplay_File::getFiles();

        include "Zend/Form/Element.php";
        $form = new Zend_Form();  	
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');	

        $teiDisplay = new Zend_Form_Element_Select('tei_display_type');
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

    /**
     * Check if plugin is installed
     *
     * @return boolean
     */
    public function installed()
    {
        return 'active';
    }

    /**
     * Render a TEI file
     *
     * @param int    $item_id Item id to render
     * @param string $section TEI section to render.
     *
     * @return void
     */
    public function renderTeiFiles($item_id, $section)
    {
        $item = $this->_db->getTable('Item')->find($item_id);
        $hasTeiFile = array();
        foreach ($item->Files as $file) {
            if (trim(strip_formatting(item_file('Dublin Core', 'Type', $options, $file)) == 'TEI Document')) {
                $hasTeiFile[] = 'true';
            }
        }
        if (in_array('true', $hasTeiFile)) {
            $teiFiles = $this->_db->getTable('TeiDisplay_Config')->findBySql(
                'item_id = ?',
                array($item_id)
            );

            foreach ($teiFiles as $teiFile) {
                render_tei_file($teiFile->id, $section);
            }
        }
    }

    /**
     * Render a TEI file
     * 
     * @param string $identifier TEI identifier
     * @param string $section    TEI document section
     *
     * @return void
     */
    public function renderTeiFile($identifier, $section)
    {
        $teiRecord = $this->_db->getTable('TeiDisplay_Config')->find($identifier);
        //initialize Dom xslt, xml documents
        $xp = new XsltProcessor();
        $xsl = new DomDocument;
        $xml_doc = new DomDocument;

        if ($teiRecord->file_id != null) {
            $file_id = $teiRecord->file_id;
            $teiFile = $this->_db->getTable('File')->find($file_id)->getWebPath('archive');
        }

        //render TEI file from Fedora.
        if (function_exists('fedora_connector_installed')) {
            if ($teiRecord->fedoraconnector_id != null) {
                $pid = $teiRecord->fedoraconnector_id;
                $datastream = $this->_db->getTable('FedoraConnector_Datastream')->find($pid);
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

    /**
     * Get title
     *
     * @param int $id TEI document id
     *
     * @return string Document title
     */
    public function getTitle($id)
    {
        $teiFile = $this->_db->getTable('TeiDisplay_Config')->find($id);

        if ($teiFile->file_id != null) {
            $file = $this->_db->getTable('File')->find($teiFile->file_id);
            return strip_formatting(item_file('Dublin Core', 'Title', $options, $file));
        }

        if ($teiFile->fedoraconnector_id != null) {
            $item = $this->_db->getTable('Item')->find($teiFile->item_id);
            return strip_formatting(item('Dublin Core', 'Title', $options, $item));
        }
    }

    /**
     * get the stylesheet
     *
     * @param int $id TEI document id to look up
     * 
     * @return string Stylesheet
     */
    public function localStylesheet($id)
    {
        $teiFile = $this->_db->getTable('TeiDisplay_Config')->find($id);
        if ($teiFile->stylesheet != null && $teiFile->stylesheet != '') {
            return TEI_DISPLAY_STYLESHEET_FOLDER . $teiFile->stylesheet;
        } else {
            return TEI_DISPLAY_STYLESHEET_FOLDER . get_option('tei_default_stylesheet');
        }

    }

    /**
     * get the display type
     *
     * @param int $id TEI document id to look up
     *
     * @return string TEI display type
     */
    public function localDisplay($id)
    {
        $teiFile = $this->_db->getTable('TeiDisplay_Config')->find($id);
        if ($teiFile->display_type != null && $teiFile->display_type != '') {
            return $teiFile->display_type;
        } else {
            return get_option('tei_display_type');
        }
    }

    /**
     * Add Fedora data streams
     *
     * @return void
     */
    protected function addFedoraItems()
    {
        if (function_exists('fedora_connector_installed')) {
            $datastreams = $this->_db->getTable('FedoraConnector_Datastream')->findBySql('datastream = ?', array('TEI'));

            foreach ($datastreams as $datastream) {
                $teiFile = fedora_connector_content_url($datastream);
                //get the TEI id
                $xml_doc = new DomDocument;									
                $xml_doc->load($teiFile);
                $xpath = new DOMXPath($xml_doc);

                $teiNode = $xml_doc->getElementsByTagName('TEI');
                $tei2Node = $xml_doc->getElementsByTagName('TEI.2');

                foreach ($teiNode as $teiNode) {
                    $p5_id = $teiNode->getAttribute('xml:id');
                } 				
                foreach ($tei2Node as $tei2Node) {
                    $p4_id = $tei2Node->getAttribute('id');
                }

                if (isset($p5_id)) {
                    $tei_id = $p5_id;
                } else if (isset($p4_id)) {
                    $tei_id = $p4_id;
                } else {
                    $tei_id = null;
                }

                if ($tei_id != null) {
                    $teiData = array(
                        'item_id' => $datastream->item_id,
                        'is_fedora_datastream' => 1,
                        'fedoraconnector_id' => $datastream->id,
                        'tei_id'=>$tei_id
                    );
                    $this->_db->insert('tei_display_configs', $teiData);
                }
            }
        }

    }

    /**
     * Checks if the XSLT PHP module is present
     *
     * @return boolean
     */
    protected function xsltExists()
    {
        return class_exists('xsltprocessor');
    }

    /**
     * Populate the tei_display_config table with exisiting TEI
     *
     * @return void;
     */
    protected function batchAddDocs()
    {
        //repopulate the tei_display_config table with existing TEI 
        //Document typed files upon plugin reinstallation
        $files = $this->_db->getTable('File')->findBySql(
            'mime_browser = ?',
            array('application/xml')
        );

        foreach ($files as $file) {
            $xml_doc = new DomDocument;	
            $teiFile = $file->getWebPath('archive');
            $xml_doc->load($teiFile);
            $tei2 = $xml_doc->getElementsByTagName('TEI.2');

            foreach ($tei2 as $tei2) {
                $tei_id = $tei2->getAttribute('id');
            }

            if ($tei_id != null && $tei_id != '') {
                $this->_db->insert(
                    'tei_display_config', array(
                        'item_id'=>$file->item_id,
                        'file_id'=>$file->id,
                        'tei_id'=>trim($tei_id)
                    )
                );
            }
        }

        return;

    }

    /**
     * set the options for the plugin
     *
     * @return void
     */
    protected function setOptions()
    {
        set_option('tei_display_type', 'entire');
        set_option('tei_default_stylesheet', 'default.xsl');

    }

    /**
     * Delete options
     *
     * @return void
     */
    protected function deleteOptions()
    {
        delete_option('tei_display_type');
        delete_option('tei_default_stylesheet');

    }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
