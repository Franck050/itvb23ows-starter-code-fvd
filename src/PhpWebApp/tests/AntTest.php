<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;

class AntTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testMove_Ant_MoveAroundBoard_MoveSucceeds() {
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '0,-1');
        GameController::play('S', '1,1');

        GameController::move('0,-1', '2,0');

        GameController::play('B', '-1,2');

        GameController::move('2,0', '-2,3');

        $this->assertArrayHasKey('-2,3', Board::getBoard());
        $this->assertEquals('A', Board::getBoard()['-2,3'][0][1]);
    }

    public function testMove_Ant_MoveToOccupiedTile_MoveFails() {
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '0,-1');
        GameController::play('S', '1,1');

        GameController::move('0,-1', '1,1');

        $this->assertEquals('A', Board::getBoard()['0,-1'][0][1]);
        $this->assertEquals('S', Board::getBoard()['1,1'][0][1]);
    }

    public function testMove_Ant_CannotMoveWhenBlockedByBeetle_MoveFails() {
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '0,-1');
        GameController::play('B', '0,2');

        GameController::move('0,-1', '1,1');
        GameController::move('0,2', '1,1');
        GameController::move('1,1', '0,-1');

        $stack = Board::getBoard()['1,1'];
        $this->assertEquals('B', end($stack)[1]);
        $this->assertEquals(['A', 'B'], array_column($stack, 1));
        $this->assertArrayNotHasKey('0,-1', Board::getBoard());
    }
}