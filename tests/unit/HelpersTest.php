<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

class TeiDisplay_HelpersTest extends Omeka_Test_AppTestCase
{
    public function setup()
    {
        parent::setUp();
        $this->helper = new TeiDisplay_Test_AppCase;
        $this->helper->setUpPlugin();
        $this->db = get_db();
    }
  
}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
