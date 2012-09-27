<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Row class for XSLT stylesheet.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplayStylesheet extends Omeka_Record_AbstractRecord
{


    /**
     * The XSLT stylesheet.
     * tinytext collate utf8_unicode_ci
     */
    public $title;

    /**
     * The XSLT stylesheet.
     * TEXT COLLATE utf8_unicode_ci NULL
     */
    public $xslt;

    /**
     * The date the stylesheet was last modified.
     * TIMESTAMP NULL
     */
    public $modified;


    /**
     * Zend_Date format for MySQL timestamp.
     */
    const DATE_FORMAT = 'yyyy-MM-dd HH:mm:ss';


    /**
     * Ingest XSLT file contents and set attributes.
     *
     * @param Omeka_Form $form The form object.
     *
     * @return void.
     */
    public function saveForm($form)
    {

        // Read the file.
        $values = $form->getValues();
        $xslt = file_get_contents($form->xslt->getFilename());

        // Set attributes.
        $this->title = $values['title'];
        $this->xslt = $xslt;

        $this->save();

    }

    /**
     * Update the modified timestamp.
     *
     * @return void
     */
    protected function beforeSave()
    {
        $this->modified = Zend_Date::now()->toString(self::DATE_FORMAT);
    }

}
