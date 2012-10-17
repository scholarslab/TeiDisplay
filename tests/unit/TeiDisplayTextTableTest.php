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

        // Create texts.
        $text1 = $this->__text($item, null, 0);
        $text2 = $this->__text($item, null, 1);

        // Get texts.
        $texts = $this->textsTable->findByItem($item);
        $this->assertEquals($texts[0]->id, $text1->id);
        $this->assertEquals($texts[1]->id, $text2->id);

    }

    /**
     * getActiveText() should return the current active text when
     * there are 1 or more texts.
     *
     * @return void.
     */
    public function testGetActiveTextWhenActiveTextExists()
    {

        // Create item.
        $item = $this->__item();

        // Create text.
        $text1 = $this->__text($item, null, 0);
        $text2 = $this->__text($item, null, 1);

        // Get active text.
        $activeText = $this->textsTable->getActiveText($item);
        $this->assertEquals($activeText->id, $text2->id);

    }

    /**
     * getActiveText() should return null when there is no existing
     * active text for the item.
     *
     * @return void.
     */
    public function testGetActiveTextWhenNoActiveTextExists()
    {

        // Create item.
        $item = $this->__item();

        // Get active text.
        $activeText = $this->textsTable->getActiveText($item);
        $this->assertNull($activeText);

        // Create texts.
        $text1 = $this->__text($item, null, 0);
        $text2 = $this->__text($item, null, 0);

        // Get active text.
        $activeText = $this->textsTable->getActiveText($item);
        $this->assertNull($activeText);

    }

}
