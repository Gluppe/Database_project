<?php
use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/productionPlan.php';

class ProductionPlanModelTest extends Unit
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


}
