<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;

class PlayTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testPlay_CannotPlayPieceWithoutQueenFirst() {
        // Arrange
        GameController::play('B', '0,0');
        GameController::play('S', '0,1');
        GameController::play('S', '-1,0');
        GameController::play('S', '-1, 2');
        GameController::play('A', '0,-1');
        GameController::play('A', '0,2');

        // Act
        GameController::play('G', '-1,-1');

        // Assert
        $this->assertArrayNotHasKey('-1,-1', Board::getBoard());
        $this->assertCount(6, Board::getBoard());
    }

    public function testPlay_PlayMultipleValidMoves_Succeeds() {
        // Arrange
        GameController::restart();

        // Act
        GameController::play('Q', '0,0');
        GameController::play('S', '0,1');
        GameController::play('S', '-1,0');

        // Assert
        $this->assertArrayHasKey('0,0', Board::getBoard());
        $this->assertArrayHasKey('0,1', Board::getBoard());
        $this->assertArrayHasKey('-1,0', Board::getBoard());
        $this->assertCount(3, Board::getBoard());
    }
}
