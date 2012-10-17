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


class TeiDisplay_TeiDisplayTextTest extends TeiDisplay_Test_AppTestCase
{

    /**
     * Test attribute get/set.
     *
     * @return void.
     */
    public function testAttributeAccess()
    {

        // Create text.
        $text = new TeiDisplayText();

        // Set.
        $text->item_id = 1;
        $text->file_id = 1;
        $text->active = 1;

        // Get.
        $this->assertEquals($text->item_id, 1);
        $this->assertEquals($text->file_id, 1);
        $this->assertEquals($text->active, 1);

    }

    /**
     * getItem() should return the parent item.
     *
     * @return void.
     */
    public function testGetItem()
    {

        // Create item.
        $item = $this->__item();

        // Create text.
        $text = $this->__text($item);

        // Check ids.
        $this->assertEquals($text->getItem()->id, $item->id);

    }

    /**
     * getFileName() should return the original filename of the
     * parent file.
     *
     * @return void.
     */
    public function testGetFileName()
    {

        // Create file and text.
        $file = $this->__file(null, 'original.xml');
        $text = $this->__text(null, $file);

        // Check file name.
        $this->assertEquals($text->getFileName(), 'original.xml');

    }

    /**
     * beforeSave() should manage per-item `active` uniqueness.
     *
     * @return void.
     */
    public function testBeforeSave()
    {

        // Create item and file.
        $item = $this->__item();
        $file = $this->__file();

        // Create active text.
        $text1 = new TeiDisplayText();
        $text1->item_id = $item->id;
        $text1->file_id = $file->id;
        $text1->active = 1;
        $text1->save();

        // Create new active text.
        $text2 = new TeiDisplayText();
        $text2->item_id = $item->id;
        $text2->file_id = $file->id;
        $text2->active = 1;
        $text2->save();

        // Re-get texts.
        $text1 = $this->textsTable->find($text1->id);
        $text2 = $this->textsTable->find($text2->id);

        // Check statuses.
        $this->assertEquals($text1->active, 0);
        $this->assertEquals($text2->active, 1);

    }

}
