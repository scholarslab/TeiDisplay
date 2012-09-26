<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Stylesheet row tests.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class TeiDisplay_TeiDisplayStylesheetTest extends TeiDisplay_Test_AppTestCase
{

    /**
     * Test attribute get/set.
     *
     * @return void.
     */
    public function testAttributeAccess()
    {
        $stylesheet = new TeiDisplayStylesheet();
        $stylesheet->xslt = 'xslt';
        $this->assertEquals($stylesheet->xslt, 'xlst');
    }

}
