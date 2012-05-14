<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

class TeiDisplay_PublicControllerTest extends Omeka_Test_AppTestCase
{
  protected $_isAdminTest = false;

  public function setUp()
  {

    $this->helper = new Neatline_Test_AppTestCase;
    $this->helper->setUpPlugin();
    $this->db = get_db();
    parent::setUp();

  }
}
