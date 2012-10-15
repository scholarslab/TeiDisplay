<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Mock for file transfer adapter.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TransferAdapterMock extends Zend_File_Transfer_Adapter_Http
{

    /**
     * Clobber default validator.
     *
     * @param array files Uploaded files.
     *
     * @return true.
     */
    public function isValid($files = null)
    {
        return true;
    }

}
