<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;

class AiTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testExecuteAiMove_AiMakesFirstMove_MoveSucceeds() {
        // Arrange = setUp()

        // Act
        GameController::executeAiMove();

        // Assert
        $this->assertArrayHasKey('0,0', Board::getBoard());
    }

    public function testExecuteAiMove_AiMakesWinningMove_MoveSucceeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '0,-1');
        GameController::play('A', '0,2');
        GameController::play('A', '1,-1');
        GameController::play('A', '1,1');
        GameController::play('A', '-1,-1');
        GameController::play('A', '2,0');
        GameController::play('B', '-1,0');
        GameController::move('2,0', '1,0');
        GameController::move('-1,-1', '-2,0');
        GameController::play('B', '-1,2');

        // Act - White wins by AI move
        GameController::executeAiMove();

        // Assert
        $this->assertArrayHasKey('-1,1', Board::getBoard());
        $this->assertTrue(GameController::checkWin(0));
    }
}
