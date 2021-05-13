<?php
use Codeception\Test\Unit;
require_once 'dbCredentials.php';
require_once 'models/TransitionHistoryModel.php';

class TransitionHistoryModelTest extends Unit
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

    public function testAddTransitionHistory() {
        $transitionHistoryModel = new TransitionHistoryModel();
        $transitionHistoryModel->addTransitionHistory(1, "skis-available");
        $this->tester->seeInDatabase('transition_history', ['state_change' => 'new -> skis-available']);
    }
}
