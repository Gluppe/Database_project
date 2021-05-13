<?php
require_once "Authorisation.php";
class CustomerCest
{
    public function _before(ApiTester $I)
    {
    }


    public function getAllOrdersByCustomerId(ApiTester $I) {
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/customer/orders?customer_id=10');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'city' => 'string',
            'county' => 'string'
        ]);

    }
}
