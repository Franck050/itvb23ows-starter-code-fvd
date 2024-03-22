<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;
use pieces\QueenBee;

class QueenBeeTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testMove_QueenBee_MoveOneTile_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '1,-1');
        GameController::play('B', '0,2');
        GameController::move('1,-1', '1,0');
        GameController::play('G', '1,2');

        // Act
        GameController::move('0,0', '1,-1');

        // Assert
        $this->assertArrayHasKey('1,-1', Board::getBoard());
        $this->assertEquals('Q', Board::getBoard()['1,-1'][0][1]);
    }

    public function testMove_QueenBee_MoveTwoTiles_Fails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '1,-1');
        GameController::play('B', '0,2');
        GameController::move('1,-1', '1,0');
        GameController::play('G', '1,2');

        // Act
        GameController::move('0,0', '2,-1');

        // Assert
        $this->assertArrayNotHasKey('2,-1', Board::getBoard());
        $this->assertEquals('Q', Board::getBoard()['0,0'][0][1]);
    }
    public function testMove_QueenBee_MoveWouldSplitHive_Fails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '1,-1');
        GameController::play('B', '0,2');
        GameController::move('1,-1', '1,0');
        GameController::play('G', '1,2');

        // Act
        GameController::move('0,0', '1,-2');

        // Assert
        $this->assertArrayNotHasKey('1,-2', Board::getBoard());
        $this->assertEquals('Q', Board::getBoard()['0,0'][0][1]);
    }

}