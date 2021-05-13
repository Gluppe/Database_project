<?php
use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/TransitionHistoryModel.php';
require_once 'models/OrdersModel.php';

class TransitionHistoryModelTest extends Unit
{
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

    public function testTranstitionHistory() {
        $ordersModel = new OrdersModel();
        //$transitionHistoryModel = new TransitionHistoryModel();
        $ordersModel->updateOrder(array('order_number' => '1'), array('state' => 'open'));
    }

}
