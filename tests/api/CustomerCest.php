<?php
require_once "Authorisation.php";
class CustomerCest
{
    public function _before(ApiTester $I)
    {
    }

    public function getAllOrdersByCustomerIdCustomerEndpoint(ApiTester $I) {
        Authorisation::setAuthorisationTokenCustomer($I);
        $I->sendGet('/customer/orders?customer_id=10');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'order_number' => 'string',
            'total_price' => 'string',
            'state' => 'string',
            'shipment_number' => 'string',
            'customer_id' => 'string',
            'date' => 'string',
            'skiType_quantity' => 'array',
        ]);
        $I->assertEquals(2, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('order_number' => 1));
        $I->seeResponseContainsJson(array('order_number' => 428));
    }

    public function testAddOrder(ApiTester $I) {
        Authorisation::setAuthorisationTokenCustomer($I);
        $data = array('skis' => array('1' => 100, '2' => 50));
        $data = json_encode($data);
        $I->sendPost('/customer/orders?customer_id=10', $data);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeNumRecords(3, 'order', []);
        $I->seeNumRecords(2, 'order_skis', ['order_number' => '429']);
    }

    public function testCancelOrder(ApiTester $I) {
        Authorisation::setAuthorisationTokenCustomer($I);
        $I->sendDelete('/customer/orders/1?customer_id=10');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::ACCEPTED);
        $I->seeInDatabase('order', ['state' => 'canceled']);
        $I->seeNumRecords(10,'ski', ['order_no' => null]);
    }
}
