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
     * Gets all the skis in the database and compares them to an expected number
     * With a consistant test set this number will be correct as this is the first test run
     */
    public function testGetAllSkis() {
        $SkisModel = new skisModel();

        $expectedCount = 23;
        $this->assertCount($expectedCount, $SkisModel->getSkis());
    }

    /**
     * A unit test for adding a new ski, make sure there are no skis of
     * ski_type_id 3 so the seeInDatabase wont find an already existing ski
     * @todo Make sure the database cleans up after each test
    */
    public function testAddSkiByType() {
        $SkisModel = new skisModel();
        $SkisModel->addSki(array('ski_type_id' => 1));

        $res = $SkisModel->getLastInsertedSki();

        $this->tester->assertEquals('1',  $res[0]['ski_type_id']);
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

        $this->tester->assertEquals('1', $res[0]['production_number']);
    }

    /**
     * You get the array of skis, then you test the first ski in the array. If the array key
     * 'grip_system' matches your input the test passes
     */
    public function testGetSkiTypesByGripSystem() {
        $skisModel = new skisModel();
        $res = $skisModel->getSkiTypesByGripSystem(array('wax'));

        $this->tester->assertIsArray($res);

        $this->tester->assertEquals('wax', $res[0]['grip_system']);
    }

}