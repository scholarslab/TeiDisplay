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
     * When an item is added and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    // public function testFedoraObjectCreationOnItemAdd()
    // {

    //     // Capture starting count.
    //     $count = $this->objectsTable->count();

    //     // Set exhibit id.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'public' => 1,
    //             'featured' => 0,
    //             'Elements' => array(),
    //             'order' => array(),
    //             'server' => 1,
    //             'pid' => 'pid:test',
    //             'dsids' => array('DC', 'content'),
    //             'import' => 0
    //         )
    //     );

    //     // Hit item edit.
    //     $this->dispatch('items/add');

    //     // +1 editions.
    //     $this->assertEquals($this->objectsTable->count(), $count+1);

    //     // Get out service and check.
    //     $object = $this->objectsTable->find(1);
    //     $this->assertEquals($object->server_id, 1);
    //     $this->assertEquals($object->pid, 'pid:test');
    //     $this->assertEquals($object->dsids, 'DC,content');

    // }

    /**
     * When an item is added and the "Import now?" checkbox is checked,
     * the datastreams should be imported.
     *
     * @return void.
     */
    // public function testImportOnItemAdd()
    // {

    //     // Create server.
    //     $this->__server();

    //     // Mock post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'public' => 1,
    //             'featured' => 0,
    //             'Elements' => array(),
    //             'order' => array(),
    //             'server' => 1,
    //             'pid' => 'pid:test',
    //             'dsids' => array('DC'),
    //             'import' => 1
    //         )
    //     );

    //     // Mock Fedora.
    //     $this->__mockImport('describe-v3x.xml', 'dc.xml');

    //     // Hit item edit.
    //     $this->dispatch('items/add');

    //     // Get the new item.
    //     $item = $this->itemsTable->find(2);

    //     // Title.
    //     $title = $item->getElementTextsByElementNameAndSetName('Title', 'Dublin Core');
    //     $this->assertEquals($title[0]->text, 'Dr. J.S. Grasty');

    //     // Contributor
    //     $contributor = $item->getElementTextsByElementNameAndSetName('Contributor', 'Dublin Core');
    //     $this->assertEquals($contributor[0]->text, 'Holsinger, Rufus W., 1866-1930');

    //     // Types.
    //     $types = $item->getElementTextsByElementNameAndSetName('Type', 'Dublin Core');
    //     $this->assertEquals($types[0]->text, 'Collection');
    //     $this->assertEquals($types[1]->text, 'StillImage');
    //     $this->assertEquals($types[2]->text, 'Photographs');

    //     // Formats.
    //     $formats = $item->getElementTextsByElementNameAndSetName('Format', 'Dublin Core');
    //     $this->assertEquals($formats[0]->text, 'Glass negatives');
    //     $this->assertEquals($formats[1]->text, 'image/jpeg');

    //     // Description.
    //     $description = $item->getElementTextsByElementNameAndSetName('Description', 'Dublin Core');
    //     $this->assertEquals($description[0]->text, 'With Child, Two Poses');

    //     // Subjects.
    //     $subjects = $item->getElementTextsByElementNameAndSetName('Subject', 'Dublin Core');
    //     $this->assertEquals($subjects[0]->text, 'Photography');
    //     $this->assertEquals($subjects[1]->text, 'Portraits, Group');
    //     $this->assertEquals($subjects[2]->text, 'Children');
    //     $this->assertEquals($subjects[3]->text, 'Holsinger Studio (Charlottesville, Va.)');

    //     // Identifiers.
    //     $identifiers = $item->getElementTextsByElementNameAndSetName('Identifier', 'Dublin Core');
    //     $this->assertEquals($identifiers[0]->text, 'H03424B');
    //     $this->assertEquals($identifiers[1]->text, 'uva-lib:1038848');
    //     $this->assertEquals($identifiers[2]->text, '39667');
    //     $this->assertEquals($identifiers[3]->text, 'uri: uva-lib:1038848');
    //     $this->assertEquals($identifiers[4]->text, '7688');
    //     $this->assertEquals($identifiers[5]->text, '365106');
    //     $this->assertEquals($identifiers[6]->text, '000007688_0004.tif');
    //     $this->assertEquals($identifiers[7]->text, 'MSS 9862');

    // }

    /**
     * When an item is edited and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    // public function testFedoraObjectCreationOnItemEdit()
    // {

    //     // Create item.
    //     $item = $this->__item();

    //     // Capture starting count.
    //     $count = $this->objectsTable->count();

    //     // Set exhibit id.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'public' => 1,
    //             'featured' => 0,
    //             'Elements' => array(),
    //             'order' => array(),
    //             'server' => 1,
    //             'pid' => 'pid:test',
    //             'dsids' => array('DC', 'content'),
    //             'import' => 0
    //         )
    //     );

    //     // Hit item edit.
    //     $this->dispatch('items/edit/' . $item->id);

    //     // +1 editions.
    //     $this->assertEquals($this->objectsTable->count(), $count+1);

    //     // Get out service and check.
    //     $object = $this->objectsTable->find(1);
    //     $this->assertEquals($object->server_id, 1);
    //     $this->assertEquals($object->pid, 'pid:test');
    //     $this->assertEquals($object->dsids, 'DC,content');

    // }

    /**
     * When an item is edited and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    // public function testFedoraObjectUpdateOnItemEdit()
    // {

    //     // Create item.
    //     $item = $this->__item();

    //     // Create Fedora object.
    //     $object = $this->__object($item);

    //     // Capture starting count.
    //     $count = $this->objectsTable->count();

    //     // Set exhibit id.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'public' => 1,
    //             'featured' => 0,
    //             'Elements' => array(),
    //             'order' => array(),
    //             'server' => 1,
    //             'pid' => 'pid:test2',
    //             'dsids' => array('DC2', 'content2'),
    //             'import' => 0
    //         )
    //     );

    //     // Hit item edit.
    //     $this->dispatch('items/edit/' . $item->id);

    //     // +0 editions.
    //     $this->assertEquals($this->objectsTable->count(), $count);

    //     // Get out service and check.
    //     $object = $this->objectsTable->find(1);
    //     $this->assertEquals($object->server_id, 1);
    //     $this->assertEquals($object->pid, 'pid:test2');
    //     $this->assertEquals($object->dsids, 'DC2,content2');

    // }

    /**
     * When an item is edited and the "Import now?" checkbox is checked,
     * the datastreams should be imported.
     *
     * @return void.
     */
    // public function testImportOnItemEdit()
    // {

    //     // Create item and object.
    //     $item = $this->__item();
    //     $this->__object($item);

    //     // Mock post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'public' => 1,
    //             'featured' => 0,
    //             'Elements' => array(),
    //             'order' => array(),
    //             'server' => 1,
    //             'pid' => 'pid:test',
    //             'dsids' => array('DC'),
    //             'import' => 1
    //         )
    //     );

    //     // Mock Fedora.
    //     $this->__mockImport('describe-v3x.xml', 'dc.xml');

    //     // Hit item edit.
    //     $this->dispatch('items/edit/' . $item->id);

    //     // Title.
    //     $title = $item->getElementTextsByElementNameAndSetName('Title', 'Dublin Core');
    //     $this->assertEquals($title[0]->text, 'Dr. J.S. Grasty');

    //     // Contributor
    //     $contributor = $item->getElementTextsByElementNameAndSetName('Contributor', 'Dublin Core');
    //     $this->assertEquals($contributor[0]->text, 'Holsinger, Rufus W., 1866-1930');

    //     // Types.
    //     $types = $item->getElementTextsByElementNameAndSetName('Type', 'Dublin Core');
    //     $this->assertEquals($types[0]->text, 'Collection');
    //     $this->assertEquals($types[1]->text, 'StillImage');
    //     $this->assertEquals($types[2]->text, 'Photographs');

    //     // Formats.
    //     $formats = $item->getElementTextsByElementNameAndSetName('Format', 'Dublin Core');
    //     $this->assertEquals($formats[0]->text, 'Glass negatives');
    //     $this->assertEquals($formats[1]->text, 'image/jpeg');

    //     // Description.
    //     $description = $item->getElementTextsByElementNameAndSetName('Description', 'Dublin Core');
    //     $this->assertEquals($description[0]->text, 'With Child, Two Poses');

    //     // Subjects.
    //     $subjects = $item->getElementTextsByElementNameAndSetName('Subject', 'Dublin Core');
    //     $this->assertEquals($subjects[0]->text, 'Photography');
    //     $this->assertEquals($subjects[1]->text, 'Portraits, Group');
    //     $this->assertEquals($subjects[2]->text, 'Children');
    //     $this->assertEquals($subjects[3]->text, 'Holsinger Studio (Charlottesville, Va.)');

    //     // Identifiers.
    //     $identifiers = $item->getElementTextsByElementNameAndSetName('Identifier', 'Dublin Core');
    //     $this->assertEquals($identifiers[0]->text, 'H03424B');
    //     $this->assertEquals($identifiers[1]->text, 'uva-lib:1038848');
    //     $this->assertEquals($identifiers[2]->text, '39667');
    //     $this->assertEquals($identifiers[3]->text, 'uri: uva-lib:1038848');
    //     $this->assertEquals($identifiers[4]->text, '7688');
    //     $this->assertEquals($identifiers[5]->text, '365106');
    //     $this->assertEquals($identifiers[6]->text, '000007688_0004.tif');
    //     $this->assertEquals($identifiers[7]->text, 'MSS 9862');

    // }

    /**
     * When an item has a Fedora object with a dsid activated that has
     * a renderer, the dsid should be rendered at the bottom of the admin
     * item show page.
     *
     * @return void.
     */
    // public function testRenderOnItemAdminShow()
    // {

    //     // Create item and object.
    //     $item = $this->__item();
    //     $this->__object($item);

    //     // Mock getMimeType().
    //     $this->__mockFedora(
    //         'datastreams.xml',
    //         "//*[local-name() = 'datastream'][@dsid='content']"
    //     );

    //     // Hit item show.
    //     $this->dispatch('items/show/' . $item->id);

    //     // Check for image.
    //     $this->assertXpath('//img[@class="fedora-renderer"]');

    // }

}
