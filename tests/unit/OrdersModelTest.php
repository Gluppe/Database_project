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

    public function testGetOrderByOrderNumber() {
        $OrdersModel = new OrdersModel();
        $res = $OrdersModel->getOrder(array('order_number' => 1));

        $this->tester->assertIsArray($res);

        $this->tester->assertEquals('1', $res[0]['production_number']);
    }

    public function testCancelOrderByOrderNumber() {
        $OrdersModel = new OrdersModel();
        $OrdersModel->cancelOrder(array('order_number' => 428, 'customer_id' => 10));
        $res = $OrdersModel->getOrder(array('order_number' => 428, 'customer_id' => 10));

        $this->tester->assertEquals('canceled', $res[0]['state']);
    }

    /**
     * A test for updating the shipment number and state in an order
     * It uses getOrder with the same order_number as the input and compares them
     */
    public function testUpdateOrderStateAndShipmentNumber() {
        $OrdersModel = new OrdersModel();
        $OrdersModel->updateOrder(array('order_number' => '1'), array('state' => 'ready to be shipped', 'shipment_number' => '200'));
        $res = $OrdersModel->getOrder(array('order_number' => '1'));

        $this->testerassertEquals('200', $res[0]['shipment_number']);
        $this->testerassertEquals('ready to be shipped', $res[0]['state']);
    }



}
