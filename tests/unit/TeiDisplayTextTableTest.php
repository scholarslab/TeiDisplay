<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Text table tests.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class TeiDisplay_TeiDisplayTextTableTest extends TeiDisplay_Test_AppTestCase
{

    /**
     * findByItem() should return all texts for an item.
     *
     * @return void.
     */
    public function testFindByItem()
    {

        // Create item.
        $item = $this->__item();

        // Create text.
        $text = $this->__text($item);

        // Get texts.
        $retrievedText = $this->textsTable->findByItem($item);
        $this->assertEquals($retrievedText->id, $text->id);

    }

    /**
     * createOrUpdate() should create a new text when one does not
     * already exist.
     *
     * @return void.
     */
    public function testCreateOrUpdateWhenNoTextExists()
    {

        // Create records.
        $item = $this->__item();
        $sheet = $this->__sheet();
        $file = $this->__file();

        // Capture starting count.
        $count = $this->textsTable->count();

        // Create or update.
        $this->textsTable->createOrUpdate(
            $item, $sheet->id, $file->id);

        // Check count+1.
        $this->assertEquals($this->textsTable->count(), $count+1);

        // Check text keys.
        $text = $this->getFirstText();
        $this->assertEquals($text->item_id, $item->id);
        $this->assertEquals($text->sheet_id, $sheet->id);
        $this->assertEquals($text->file_id, $file->id);

    }

    /**
     * createOrUpdate() should create a new text when one does not
     * already exist.
     *
     * @return void.
     */
    public function testCreateOrUpdateWhenTextExists()
    {

        // Create records.
        $item = $this->__item();
        $sheet1 = $this->__sheet();
        $sheet2 = $this->__sheet();
        $file1 = $this->__file();
        $file2 = $this->__file();

        // Create text.
        $text = $this->__text($item, $file1, $sheet1);

        // Capture starting count.
        $count = $this->textsTable->count();

        // Create or update.
        $this->textsTable->createOrUpdate(
            $item, $sheet2->id, $file2->id);

        // Check unchanged count.
        $this->assertEquals($this->textsTable->count(), $count);

        // Check text keys.
        $text = $this->getFirstText();
        $this->assertEquals($text->item_id, $item->id);
        $this->assertEquals($text->sheet_id, $sheet2->id);
        $this->assertEquals($text->file_id, $file2->id);

    }

}
