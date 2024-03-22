<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;

class SpiderTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testMove_Spider_MoveOneTile_MoveFails() {
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('S', '0,-1');
        GameController::play('G', '-1,2');

        GameController::move('0,-1', '-1,0');

        $this->assertArrayNotHasKey('-1,0', Board::getBoard());
        $this->assertEquals('S', Board::getBoard()['0,-1'][0][1]);
    }
    public function testMove_Spider_MoveThreeTiles_MoveSucceeds() {
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('S', '0,-1');
        GameController::play('G', '-1,2');

        GameController::move('0,-1', '-2,2');

        $this->assertArrayHasKey('-2,2', Board::getBoard());
        $this->assertArrayNotHasKey('0,-1', Board::getBoard());
    }

    public function testMove_Spider_MoveToTileWhereThereIsNoAdjacentInsect_MoveFails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('S', '0,-1');
        GameController::play('G', '-1,2');

        // Act
        GameController::move('0,-1', '-2,1');

        // Assert
        $this->assertArrayNotHasKey('-2,1', Board::getBoard());
        $this->assertArrayHasKey('0,-1', Board::getBoard());
    }

    public function testMove_Spider_MoveFourTiles_MoveFails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('S', '0,-1');
        GameController::play('G', '-1,2');

        // Act
        GameController::move('0,-1', '-2,3');

        // Assert
        $this->assertArrayNotHasKey('-2,3', Board::getBoard());
        $this->assertArrayHasKey('0,-1', Board::getBoard());
    }


}