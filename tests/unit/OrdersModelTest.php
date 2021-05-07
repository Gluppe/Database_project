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

        if ($res[0]['production_number'] = '1') {
            $test1 = $res[0];
        }
    }

    public function testCancelOrderByOrderNumber() {
        $OrdersModel = new OrdersModel();
        $OrdersModel->cancelOrder(array('order_number' => 428, 'customer_id' => 10));
        $res = $OrdersModel->getOrder(array('order_number' => 428, 'customer_id' => 10));

        $this->tester->assertEquals('canceled', $res['state']);
    }



}
