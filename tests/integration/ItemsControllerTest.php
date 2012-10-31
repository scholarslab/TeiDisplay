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

class TeiDisplay_ItemsControllerTest extends TeiDisplay_Test_AppTestCase
{

    /**
     * There should be a 'TEI' tab in the item add form.
     *
     * @return void.
     */
    public function testItemAddTab()
    {

        // Create stylesheets.
        $sheet1 = $this->__sheet('Test Title 1');
        $sheet2 = $this->__sheet('Test Title 2');

        // Hit item add.
        $this->dispatch('items/add');

        // Check for tab.
        $this->assertXpathContentContains(
            '//ul[@id="section-nav"]/li/a[@href="#tei-metadata"]', 'TEI');

        // Check stylesheet dropdown.
        $this->assertXpath('//select[@id="teistylesheet"]
            [@name="teistylesheet"]');
        $this->assertXpath('//select[@name="teistylesheet"]/
            option[@value="'.$sheet1->id.'"][@label="Test Title 1"]');
        $this->assertXpath('//select[@name="teistylesheet"]/
            option[@value="'.$sheet2->id.'"][@label="Test Title 2"]');

        // Check import TEI header checkbox.
        $this->assertXpath('//input[@type="checkbox"][@id="teiimport"]
            [@name="teiimport"]');

    }

    /**
     * There should be a 'TEI' tab in the item edit form.
     *
     * @return void.
     */
    public function testItemEditTab()
    {

        // Create item.
        $item = $this->__item();

        // Create stylesheets.
        $sheet1 = $this->__sheet('Test Title 1');
        $sheet2 = $this->__sheet('Test Title 2');

        // Create files.
        $file1 = $this->__file($item, 'text1.xml');
        $file2 = $this->__file($item, 'text2.xml');

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Check for tab.
        $this->assertXpathContentContains(
            '//ul[@id="section-nav"]/li/a[@href="#tei-metadata"]', 'TEI');

        // Check files.
        $this->assertXpath('//select[@id="teitext"]
            [@name="teitext"]');
        $this->assertXpath('//select[@name="teitext"]/
            option[@value="'.$file1->id.'"][@label="text1.xml"]');
        $this->assertXpath('//select[@name="teitext"]/
            option[@value="'.$file2->id.'"][@label="text2.xml"]');

        // Check stylesheets.
        $this->assertXpath('//select[@id="teistylesheet"]
            [@name="teistylesheet"]');
        $this->assertXpath('//select[@name="teistylesheet"]/
            option[@value="'.$sheet1->id.'"][@label="Test Title 1"]');
        $this->assertXpath('//select[@name="teistylesheet"]/
            option[@value="'.$sheet2->id.'"][@label="Test Title 2"]');

        // Check import TEI header checkbox.
        $this->assertXpath('//input[@type="checkbox"][@id="teiimport"]
            [@name="teiimport"]');

    }

    /**
     * If there is an existing text record for the item, the data should
     * be populated.
     *
     * @return void.
     */
    public function testItemEditData()
    {

        // Create item.
        $item = $this->__item();

        // Create stylesheets.
        $sheet1 = $this->__sheet('Test Title 1');
        $sheet2 = $this->__sheet('Test Title 2');

        // Create files.
        $file1 = $this->__file($item, 'text1.xml');
        $file2 = $this->__file($item, 'text2.xml');

        // Create text.
        $text = $this->__text($item, $file2, $sheet2);

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Check files.
        $this->assertXpath('//select[@name="teitext"]/
            option[@value="'.$file2->id.'"][@label="text2.xml"]
            [@selected="selected"]');

        // Check stylesheets.
        $this->assertXpath('//select[@name="teistylesheet"]/
            option[@value="'.$sheet2->id.'"][@label="Test Title 2"]
            [@selected="selected"]');

    }

    /**
     * When an item is added and the stylesheet is set, the text should
     * be created.
     *
     * @return void.
     */
    public function testTextCreationOnItemAdd()
    {

        // Create stylesheet.
        $sheet = $this->__sheet('Test Title 1');

        // Capture starting count.
        $count = $this->textsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')->setPost(array(
            'public' => 1,
            'featured' => 0,
            'Elements' => array(),
            'order' => array(),
            'tags' => '',
            'teistylesheet' => $sheet->id
        ));

        // Hit item edit.
        $this->dispatch('items/add');

        // +1 texts.
        $this->assertEquals($this->textsTable->count(), $count+1);

        // Get out text and check.
        $item = $this->getLastItem();
        $text = $this->getFirstText();
        $this->assertEquals($text->item_id, $item->id);
        $this->assertEquals($text->sheet_id, $sheet->id);

    }

    /**
     * When an item is edited and TEI data is set, the text should be
     * created.
     *
     * @return void.
     */
    public function testTextCreationOnItemEdit()
    {

        // Create item.
        $item = $this->__item();

        // Create file and stylesheet.
        $sheet = $this->__sheet('Test Title');
        $file = $this->__file($item, 'text.xml');

        // Capture starting count.
        $count = $this->textsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')->setPost(array(
            'public' => 1,
            'featured' => 0,
            'Elements' => array(),
            'order' => array(),
            'tags' => '',
            'teistylesheet' => $sheet->id,
            'teitext' => $file->id
        ));

        // Hit item edit.
        $this->dispatch('items/edit/'.$item->id);

        // +1 texts.
        $this->assertEquals($this->textsTable->count(), $count+1);

        // Get out text and check.
        $text = $this->getFirstText();
        $this->assertEquals($text->item_id, $item->id);
        $this->assertEquals($text->sheet_id, $sheet->id);
        $this->assertEquals($text->file_id, $file->id);

    }

    /**
     * When an item is edited and new TEI data is set, the text should be
     * updated.
     *
     * @return void.
     */
    public function testTextEditOnItemEdit()
    {

        // Create item.
        $item = $this->__item();

        // Create stylesheets.
        $sheet1 = $this->__sheet('Test Title 1');
        $sheet2 = $this->__sheet('Test Title 2');

        // Create files.
        $file1 = $this->__file($item, 'text1.xml');
        $file2 = $this->__file($item, 'text2.xml');

        // Create text.
        $text = $this->__text($item, $file1, $sheet1);

        // Capture starting count.
        $count = $this->textsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')->setPost(array(
            'public' => 1,
            'featured' => 0,
            'Elements' => array(),
            'order' => array(),
            'tags' => '',
            'teistylesheet' => $sheet2->id,
            'teitext' => $file2->id
        ));

        // Hit item edit.
        $this->dispatch('items/edit/'.$item->id);

        // +0 texts.
        $this->assertEquals($this->textsTable->count(), $count);

        // Re-get text and check.
        $text = $this->textsTable->find($text->id);
        $this->assertEquals($text->sheet_id, $sheet2->id);
        $this->assertEquals($text->file_id, $file2->id);

    }

    /**
     * The TEI item output format should render the document.
     *
     * @return void.
     */
    public function testTeiOutputFormat()
    {

    }

}
