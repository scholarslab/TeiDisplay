<?php
class TeiDisplay_ConfigController extends Omeka_Controller_Action
{
    public function browseAction() 	
    {
    	$db = get_db();

    	$currentPage = $this->_getParam('page', 1);
    	$this->view->entries =  TeiDisplay_Config::getConfig($currentPage);
		
        /** 
         * Now process the pagination
         * 
         **/
        $paginationUrl = $this->getRequest()->getBaseUrl().'/config/browse/';

        //Serve up the pagination
        $pagination = array('page'          => $currentPage, 
                            'per_page'      => 20, 
                            'total_results' => $count, 
                            'link'          => $paginationUrl);

        Zend_Registry::set('pagination', $pagination);
    }

	public function editAction()
	{
		$db = get_db();
		$id = $this->_getParam('id');		
		$entry = $db->getTable('TeiDisplay_Config')->find($id);
		$file_id = $db->getTable('File')->find($entry['file_id'])->id;
		$this->view->file_id = $file_id;
		$form = $this->configForm($entry);
		$this->view->form = $form;
	}
	
    public function updateAction() 
    {
		$form = $this->configForm($entry);
		
    	if ($_POST) {
    		if ($form->isValid($this->_request->getPost())) {    
    			//get posted values		
				$uploadedData = $form->getValues();
				$data = array('id'=>$uploadedData['tei_display_entry_id'],
								'display_type'=>$uploadedData['tei_display_type'],
								'stylesheet'=>$uploadedData['tei_default_stylesheet']);				
				//get db
				try{		
					$db = get_db();								
					$db->insert('tei_display_configs', $data); 
					$this->flashSuccess('TEI file display configuration successfully modified.');			
					$this->redirect->goto('browse');
					
				} catch (Exception $e) {
					$this->flashError($e->getMessage());
        		}
    		}    		
    	}
    	else {
    			$this->flashError('Failed to gather posted data.');
    			$this->view->form = $form;
    	}
    }

	private function configForm($entry) {
		$xslFiles = TeiDisplay_File::getFiles();
	
	    require "Zend/Form/Element.php";
    	$form = new Zend_Form();
		$form->setAction('update');    	
    	$form->setMethod('post');
    	$form->setAttrib('enctype', 'multipart/form-data');  

		$teiDisplay = new Zend_Form_Element_Select ('tei_display_type');
	    $teiDisplay->setLabel('Display Type:');
	    $teiDisplay->addMultiOption('', 'Select...');
	    $teiDisplay->addMultiOption('entire', 'Entire Document');
		$teiDisplay->addMultiOption('segmental', 'Segmental');
		if ($entry['display_type'] != NULL){
			$teiDisplay->setValue($entry['display_type']);
		}
	    $form->addElement($teiDisplay);

	    //default stylesheet
	    $stylesheet = new Zend_Form_Element_Select('tei_default_stylesheet');
	    $stylesheet->setLabel('Default Stylesheet:');
		$stylesheet->addMultiOption('', 'Select...');
	    foreach ($xslFiles as $xslFile) {
			$stylesheet->addMultiOption($xslFile, $xslFile);
	    }
	    if ($entry['stylesheet'] != NULL){
			$stylesheet->setValue($entry['stylesheet']);
		};
		$form->addElement($stylesheet);
		
		//Id, only if it is an update    	
    	$entryId = new Zend_Form_Element_Hidden('tei_display_entry_id');
    	$entryId->setValue($entry['id']);
    	$form->addElement($entryId);
    	
		//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Submit');    	
    	return $form;
	}
}


