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
     * Check for listings table when stylesheets.
     *
     * @return void.
     */
    public function testBrowseMarkup()
    {

        // Create stylesheets.
        $sheet1 = $this->__stylesheet('Title 1');
        $sheet2 = $this->__stylesheet('Title 2');

        // Check for listings.
        $this->dispatch('tei/stylesheets');
        $this->assertQueryCount('table.tei-display tbody tr', 2);

        // Check titles.
        $this->assertQueryContentContains(
            '#stylesheet-'.$sheet1->id.' td.title a.edit', 'Title 1');
        $this->assertQueryContentContains(
            '#stylesheet-'.$sheet2->id.' td.title a.edit', 'Title 2');

    }

}
