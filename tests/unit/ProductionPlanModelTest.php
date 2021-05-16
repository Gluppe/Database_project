<?php
use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/ProductionPlanModel.php';

class ProductionPlanModelTest extends Unit
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

    public function testGetProductionPlan() {
        $model = new ProductionPlanModel();

        $res = $model->getProductionPlan("5");
        $this->tester->assertEquals($res, array(array('month' => '05', 'skis' => array('1' => 100, '2' => 50))));
    }

    public function testAddProductionPlan() {
        $model = new ProductionPlanModel();
        $payload = array('month' => '06', 'skis' => array('1' => 50, '2' => 100));

        $model->addProductionPlan($payload);
        $this->tester->seeInDatabase('production_plan', ['month' => '2021-06-01']);
        $this->tester->seeNumRecords(2, 'production_skis', ['production_plan_id' => 2]);
        $this->tester->seeNumRecords(2, 'production_skis', ['ski_type_id' => 2]);
        $this->tester->seeNumRecords(2, 'production_skis', ['ski_type_id' => 1]);
    }
}
