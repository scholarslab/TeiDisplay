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
        $sheet1 = $this->__sheet('Title 1');
        $sheet2 = $this->__sheet('Title 2');

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
        $this->assertXpath('//input[@type="text"][@name="title"]');
        $this->assertXpath('//input[@type="file"][@name="xslt"]');

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
            array('title' => ''));

        $this->dispatch('tei/stylesheets/add');
        $this->assertQueryContentContains('ul.error li', 'Enter a title.');

    }

    /**
     * Valid form should create a stylesheet.
     *
     * @return void.
     */
    public function testAddStylesheetSuccess()
    {

        // Mock post.
        $this->request->setMethod('POST')->setPost(
            array('title' => 'Test Title'));

        // Populate $_FILES.
        $this->__setStylesheetFileUpload();

        // Capture starting count.
        $count = $this->sheetsTable->count();

        // Add.
        $this->dispatch('tei/stylesheets/add');

        // Check count+1.
        $this->assertEquals($this->sheetsTable->count(), $count+1);

        // Get new sheet, check params.
        $sheet = $this->getFirstStylesheet();
        $this->assertEquals($sheet->title, 'Test Title');
        $this->assertEquals($sheet->xslt, 'xslt mock');

    }

    /**
     * Test for edit form markup.
     *
     * @return void.
     */
    public function testEditStylesheetFormMarkup()
    {

        // Create stylesheet.
        $sheet = $this->__sheet('Title');

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
        $sheet = $this->__sheet('Title');

        // Mock post.
        $this->request->setMethod('POST')->setPost(
            array('title' => ''));

        $this->dispatch('tei/stylesheets/edit/'.$sheet->id);
        $this->assertQueryContentContains('ul.error li', 'Enter a title.');

    }

    /**
     * When a stylesheet form is saved without a new file upload,
     * modifications to other fields should be saved and the value of
     * the xslt field should not be changed.
     *
     * @return void.
     */
    public function testEditStylesheetNoNewFile()
    {

        // Create stylesheet.
        $sheet = $this->__sheet('Title', 'xslt');

        // Empty $_FILES.
        $this->__setEmptyStylesheetFileUpload();

        // Mock post.
        $this->request->setMethod('POST')->setPost(
            array('title' => 'New Title'));

        // Edit.
        $this->dispatch('tei/stylesheets/edit/'.$sheet->id);

        // Get new sheet, check params.
        $sheet = $this->sheetsTable->find($sheet->id);
        $this->assertEquals($sheet->title, 'New Title');
        $this->assertEquals($sheet->xslt, 'xslt');

    }

    /**
     * When a stylesheet form is saved and a new file is uploaded,
     * the new file should be saved.
     *
     * @return void.
     */
    public function testEditStylesheetNewFile()
    {

        // Create stylesheet.
        $sheet = $this->__sheet('Title', 'xslt');

        // Populate $_FILES.
        $this->__setStylesheetFileUpload('new xslt');

        // Mock post.
        $this->request->setMethod('POST')->setPost(
            array('title' => 'New Title'));

        // Edit.
        $this->dispatch('tei/stylesheets/edit/'.$sheet->id);

        // Get new sheet, check params.
        $sheet = $this->sheetsTable->find($sheet->id);
        $this->assertEquals($sheet->title, 'New Title');
        $this->assertEquals($sheet->xslt, 'new xslt');

    }

}
