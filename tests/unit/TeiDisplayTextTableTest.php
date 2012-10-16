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
     * getActiveText() should return the current active text when
     * there are 1 or more texts.
     *
     * @return void.
     */
    public function testGetActiveTextWhenTextsExist()
    {

        // Create item.
        $item = $this->__item();

        // Create texts.
        $text1 = $this->__text($item);
        $text2 = $this->__text($item);

    }

}
