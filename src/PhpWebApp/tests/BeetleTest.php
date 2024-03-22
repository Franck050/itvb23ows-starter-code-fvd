<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;

class BeetleTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testMove_Beetle_MoveOneTile_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '0,-1');
        GameController::play('A', '0,2');

        // Act
        GameController::move('0,-1', '-1,0');

        // Assert
        $this->assertArrayHasKey('-1,0', Board::getBoard());
        $this->assertArrayNotHasKey('0,-1', Board::getBoard());
    }

    public function testMove_Beetle_MoveTwoTile_Fails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '0,-1');
        GameController::play('A', '0,2');

        // Act
        GameController::move('0,-1', '-1,1');

        // Assert
        $this->assertArrayHasKey('0,-1', Board::getBoard());
        $this->assertArrayNotHasKey('-1,1', Board::getBoard());
    }

    public function testMove_Beetle_StackOnOpponentTile_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '0,-1');
        GameController::play('A', '0,2');
        GameController::move('0,-1', '-1,0');
        GameController::move('0,2', '-1,1');

        // Act
        GameController::move('-1,0', '-1,1');

        // Assert
        $this->assertEquals('A', Board::getBoard()['-1,1'][0][1]);
        $this->assertEquals('B', Board::getBoard()['-1,1'][1][1]);
    }
}