<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Text form unit tests.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class TeiDisplay_TextFormTest extends TeiDisplay_Test_AppTestCase
{

    /**
     * getTextsForSelect() should return a well-formed array of
     * `id` => `filenames` for text records.
     *
     * @return void.
     */
    public function testGetTextsForSelect()
    {

        // Create form.
        $item = $this->__item();
        $form = new TeiDisplay_Form_Text(array('item' => $item));

        // Create texts.
        $file1 = $this->__file($item, 'text1.xml');
        $file2 = $this->__file($item, 'text2.xml');

        $this->assertEquals($form->getTextsForSelect(),
            array(
                "{$file1->id}" => 'text1.xml',
                "{$file2->id}" => 'text2.xml',
            )
        );

    }

    /**
     * getStylesheetsForSelect() should return a well-formed array of
     * `id` => `titles` for stylesheet records.
     *
     * @return void.
     */
    public function testGetStylesheetsForSelect()
    {

        // Create form.
        $item = $this->__item();
        $form = new TeiDisplay_Form_Text(array('item' => $item));

        // Create stylesheets.
        $sheet1 = $this->__sheet('sheet1');
        $sheet2 = $this->__sheet('sheet2');

        $this->assertEquals($form->getStylesheetsForSelect(),
            array(
                "{$sheet1->id}" => 'sheet1',
                "{$sheet2->id}" => 'sheet2',
            )
        );

    }

}
