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
        $text1 = $this->__text($item);
        $text2 = $this->__text($item);

        // Get texts.
        $texts = $this->textsTable->findByItem($item);
        $this->assertEquals($texts[0]->id, $text1->id);
        $this->assertEquals($texts[1]->id, $text2->id);

    }

}
