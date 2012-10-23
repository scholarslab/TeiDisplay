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

}
