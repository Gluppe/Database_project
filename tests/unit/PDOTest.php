<?php

use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/SkisModel.php';

class PDOTest extends Unit {
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var \PDODemo
     */
    protected $pdoDemo;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testAddSKiNewSKiType() {
        $SkisModel = new skisModel();
        $SkisModel->addSki(array('ski_type_id' => 1));

        $this->tester->seeInDatabase('ski', array('ski_type_id' => 1));
    }


}