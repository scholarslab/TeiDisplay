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

        // Create stylesheet.
        $stylesheet = new TeiDisplayStylesheet();

        // Set.
        $stylesheet->title = 'title';
        $stylesheet->xslt = 'xslt';
        $stylesheet->modified = 'yyyy-MM-dd HH:mm:ss';

        // Get.
        $this->assertEquals($stylesheet->title, 'title');
        $this->assertEquals($stylesheet->xslt, 'xslt');
        $this->assertEquals($stylesheet->modified, 'yyyy-MM-dd HH:mm:ss');

    }

    /**
     * beforeSave() should update modified timestamp.
     *
     * @return void.
     */
    public function testBeforeSave()
    {

        // Create stylesheet.
        $stylesheet = new TeiDisplayStylesheet();
        $stylesheet->title = 'title';
        $stylesheet->xslt = 'xslt';

        // Save, check for timestamp.
        $stylesheet->save();
        $this->assertNotNull($stylesheet->modified);

    }

}
