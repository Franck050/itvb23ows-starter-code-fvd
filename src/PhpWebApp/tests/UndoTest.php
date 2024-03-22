<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;

class UndoTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testUndoFunctionality_UndoOneMove_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '0,-1');
        GameController::play('A', '0,2');
        GameController::play('G', '-1,0');

        // Act
        GameController::undo();

        // Assert
        $this->assertArrayNotHasKey('-1,0', Board::getBoard());
        $this->assertCount(4, Board::getBoard());
    }

    public function testUndoFunctionality_UndoThreeMoves_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '0,-1');
        GameController::play('A', '0,2');
        GameController::play('G', '-1,0');

        // Act
        GameController::undo();
        GameController::undo();
        GameController::undo();

        // Assert
        $this->assertArrayNotHasKey('-1,0', Board::getBoard());
        $this->assertArrayNotHasKey('0,2', Board::getBoard());
        $this->assertArrayNotHasKey('0,-1', Board::getBoard());
        $this->assertCount(2, Board::getBoard());
    }
}