<?php

use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/SkisModel.php';
require_once 'models/OrdersModel.php';

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

    /**
     * A unit test for adding a new ski, make sure there are no skis of
     * ski_type_id 3 so the seeInDatabase wont find an already existing ski
     * @todo Make sure the database cleans up after each test
     */
    public function testAddSKiNewSKiByType() {
        $SkisModel = new skisModel();
        $SkisModel->addSki(array('ski_type_id' => 3));

        $this->tester->seeInDatabase('ski', array('ski_type_id' => 3));
    }

    /**
     * Finding a single ski by their production number. Since getSki returns
     * an array, you check the first entry, if the production number matches
     * your input
     */
    public function testGetSkiByProductionNumber() {
        $SkisModel = new skisModel();

        $res = $SkisModel->getSki('1');

        $this->tester->assertIsArray($res);

        if ($res[0]['production_number'] = '1') {
            $test1 = $res[0];
        }

        $this->tester->assertEquals('1', $test1['production_number']);
    }

    public function testGetAllSkis() {
        $SkisModel = new skisModel();
        $res = $SkisModel->getSkis();

        $this->tester->assertIsArray($res);

        $expectedCount = 4;
        $this->assertCount($expectedCount, $res);
    }

    /**
     * You get the array of skis, then you test the first ski in the array. If the array key
     * 'grip_system' matches your input the test passes
     */
    public function testGetSkiTypesByGripSystem() {
        $skisModel = new skisModel();
        $res = $skisModel->getSkiTypesByGripSystem(array('wax'));

        $this->tester->assertIsArray($res);

        if ($res[0]['grip_system'] = 'wax') {
            $test1 = $res[0];
        }

        $this->tester->assertEquals('wax', $test1['grip_system']);
    }
}