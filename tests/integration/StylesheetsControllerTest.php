<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Index controller integration tests.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class TeiDisplay_StylesheetsControllerTest extends TeiDisplay_Test_AppTestCase
{

    /**
     * Index should redirect to browse.
     *
     * @return void.
     */
    public function testIndexRedirect()
    {
        $this->dispatch('tei/stylesheets');
        $this->assertModule('tei-display');
        $this->assertController('stylesheets');
        $this->assertAction('browse');
    }

    /**
     * When no stylesheets, link to upload one.
     *
     * @return void.
     */
    public function testBrowseMarkupWithNoStylesheets()
    {
        $this->dispatch('tei/stylesheets');
    }

}
