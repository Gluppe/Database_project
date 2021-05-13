<?php

use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/SkisModel.php';
require_once 'models/OrdersModel.php';

class OrdersModelTest extends Unit {
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * Tests the getOrder function
     */
    public function testGetOrderByOrderNumber() {
        $OrdersModel = new OrdersModel();
        $res = $OrdersModel->getOrder(array(2 => 1), array());

        $this->tester->assertIsArray($res);

        $this->tester->assertEquals('1', $res[0]['order_number']);
    }

    /**
     * Tests cancelOrder
     */
    public function testCancelOrderByOrderNumber() {
        $OrdersModel = new OrdersModel();
        $OrdersModel->cancelOrder(array(2 => 1), array('customer_id' => 10));
        $this->tester->seeInDatabase('order', ['state' => 'canceled']);
        $this->tester->seeNumRecords(10,'ski', ['order_no' => NULL]);
    }

    /**
     * A test for updating the state in an order
     */
    public function testUpdateOrderState() {
        $OrdersModel = new OrdersModel();
        $OrdersModel->updateOrder(array(2 => 1), array('state' => 'open'));

        $this->tester->seeInDatabase('order', ['state' => 'open']);
    }

}
