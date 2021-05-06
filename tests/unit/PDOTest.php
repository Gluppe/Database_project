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

    /*
    public function testGetOrderByOrderNumber() {
        $OrdersModel = new OrdersModel();
        $OrdersModel->getOrder(array('order_number' => 1));

        $this->tester->assertEquals(array('order_number' => 1));
    }
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

    public function testCancelOrderByOrderNumber() {
        $OrdersModel = new OrdersModel();
        $OrdersModel->cancelOrder(array('order_number' => 428, 'customer_id' => 10));
        $res = $OrdersModel->getOrder(array('order_number' => 428, 'customer_id' => 10));

        $this->tester->assertEquals('canceled', $res['state']);
    }


    public function testGetSkiTypesByGripSystem() {
        $skisModel = new skisModel();
        $res = $skisModel->getSkiTypesByGripSystem(array('wax'));

        //$this->tester->assertIsArray($res);

        if ($res[0]['grip_system'] = 'wax') {
            $test1 = $res[0];
        }

        $this->tester->assertEquals('wax', $test1['grip_system']);
    }
}