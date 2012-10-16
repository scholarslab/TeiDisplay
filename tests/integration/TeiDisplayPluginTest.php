<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Plugin manager class tests.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class TeiDisplay_TeiDisplayPluginTest extends TeiDisplay_Test_AppTestCase
{

    /**
     * Install should add XML the mimetype / extension whitelists.
     *
     * @return void.
     */
    public function testMimeTypeAdditions()
    {
        $extensions = get_option('file_extension_whitelist');
        $mimes = get_option('file_mime_type_whitelist');
        $this->assertContains('xml', $extensions);
        $this->assertContains('application/xml', $mimes);
    }

}
