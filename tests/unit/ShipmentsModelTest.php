<?php
use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/ShipmentsModel.php';


class ShipmentsModelTest extends Unit
{
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
     * Test for getting a shipment. Shipmentmodel returns an array. take the first
     * index in the array and check if the shipment number is the same as your imput.
     */
    public function testGetShipmentByShipmentNumber() {
        $ShipmentsModel = new ShipmentsModel();

        $res = $ShipmentsModel->getShipment(array('100'));

        $this->tester->assertIsArray($res);

        $this->tester->assertEquals('100', $res[0]['shipment_number']);
    }
}
