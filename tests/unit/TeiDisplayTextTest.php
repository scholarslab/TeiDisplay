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
        $text->sheet_id = 1;

        // Get.
        $this->assertEquals($text->item_id, 1);
        $this->assertEquals($text->file_id, 1);
        $this->assertEquals($text->sheet_id, 1);

    }

    /**
     * __construct() should set the item_id if an item is passed.
     *
     * @return void.
     */
    public function testConstruct()
    {

        // Create item and text.
        $item = $this->__item();
        $text = new TeiDisplayText($item);

        // Check for set item_id.
        $this->assertEquals($text->item_id, $item->id);

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

}
