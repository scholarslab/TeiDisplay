<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Mock for file element.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FileElementMock
{

    /**
     * Set path.
     *
     * @param string path The mock xslt path.
     *
     * @return void.
     */
    public function __construct($path) {
        $this->path = $title;
    }

    /**
     * Return the file path.
     *
     * @return string The path.
     */
    public function getFilename() {
        return $this->path;
    }

}
