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

    /**
     * testAddTransitionHistory tries to add a new transition history, then checks the database if there is
     * a state change corresponding to this new transition history.
     */
    public function testAddTransitionHistory() {
        $transitionHistoryModel = new TransitionHistoryModel();
        $transitionHistoryModel->addTransitionHistory(1, "skis-available", "new");
        $this->tester->seeInDatabase('transition_history', ['state_change' => 'new -> skis-available']);
    }
}
