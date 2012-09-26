<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * XSLT stylesheets controller.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class TeiDisplay_StylesheetsController extends Omeka_Controller_AbstractActionController
{

    /**
     * Get tables.
     *
     * @return void
     */
    public function init()
    {
        $modelName = 'TeiDisplayStylesheet';
        $this->_helper->db->setDefaultModelName($modelName);
        $this->_table = $this->_helper->db->getTable($modelName);
    }

    /**
     * Create stylesheet.
     *
     * @return void
     */
    public function addAction()
    {

    }

    /**
     * Edit stylesheet.
     *
     * @return void
     */
    public function editAction()
    {

    }

    /**
     * Set add success message.
     */
    protected function _getAddSuccessMessage($stylesheet)
    {
        return __('The stylesheet "%s" was successfully added!', $stylesheet->title);
    }

    /**
     * Set edit success message.
     */
    protected function _getEditSuccessMessage($neatline)
    {
        return __('The Neatline "%s" was successfully changed!', $stylesheet->title);
    }

    /**
     * Set delete success message.
     */
    protected function _getDeleteSuccessMessage($neatline)
    {
        return __('The Neatline "%s" was successfully deleted!', $stylesheet->title);
    }

    /**
     * Set delete confirm message.
     */
    protected function _getDeleteConfirmMessage($neatline)
    {
        return __('This will delete the Neatline "%s" and its associated metadata.', $neatline->name);
    }

    /**
     * Construct the add/edit form.
     */
    private function _getForm(TeiDisplayStylesheet $stylesheet)
    {
        $form = new TeiDisplay_Form_Stylesheet(array('stylesheet' => $stylesheet));
        return $form;
    }

}
