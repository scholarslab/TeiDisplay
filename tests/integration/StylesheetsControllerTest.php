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

    /**
     * Test for add form markup.
     *
     * @return void.
     */
    public function testAddStylesheetFormMarkup()
    {

        $this->dispatch('tei/stylesheets/add');
        $this->assertXpath('//input[@name="title"]');
        $this->assertXpath('//input[@name="xslt"]');

    }

    /**
     * Test for add form markup.
     *
     * @return void.
     */
    public function testAddStylesheetFormEmptyFieldErrors()
    {

        // Mock post.
        $this->request->setMethod('POST')->setPost(
            array('title' => '', 'xslt' => ''));

        $this->dispatch('tei/stylesheets/add');
        $this->assertQueryContentContains('ul.error li', 'Enter a title.');

        // TODO: How to mock file?

    }

    /**
     * Valid form should create a stylesheet.
     *
     * @return void.
     */
    public function testAddStylesheetSuccess()
    {
        // TODO: How to mock file?
    }

    /**
     * Test for edit form markup.
     *
     * @return void.
     */
    public function testEditStylesheetFormMarkup()
    {

        // Create stylesheet.
        $sheet = $this->__stylesheet('Title');

        $this->dispatch('tei/stylesheets/edit/'.$sheet->id);
        $this->assertXpath('//input[@name="title"][@value="Title"]');

    }

    /**
     * Test for edit form markup.
     *
     * @return void.
     */
    public function testEditStylesheetFormEmptyFieldErrors()
    {

        // Create stylesheet.
        $sheet = $this->__stylesheet('Title');

        // Mock post.
        $this->request->setMethod('POST')->setPost(
            array('title' => '', 'xslt' => ''));

        $this->dispatch('tei/stylesheets/edit/'.$sheet->id);
        $this->assertQueryContentContains('ul.error li', 'Enter a title.');

        // TODO: How to mock file?

    }

}
