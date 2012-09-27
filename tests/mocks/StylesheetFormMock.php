<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Mock for stylesheet form.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

require_once 'FileElementMock.php';

class StylesheetFormMock
{

    /**
     * Set title, create XSLT element mock.
     *
     * @param string title The stylesheet title.
     * @param string path The mock xslt path.
     *
     * @return void.
     */
    public function __construct($title, $path) {
        $this->xslt = new FileElementMock($path);
        $this->title = $title;
    }

    /**
     * Return the values array.
     *
     * @return array The form values.
     */
    public function getValues() {
        return array('title' => $title);
    }

}
