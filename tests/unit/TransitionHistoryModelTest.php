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

    public function testAddTransitionHistory() {
        $transitionHistoryModel = new TransitionHistoryModel();
        $transitionHistoryModel->addTransitionHistory(1, "open");
        $this->tester->seeInDatabase('transition_history', ['state_change' => 'new -> open']);
    }
}
