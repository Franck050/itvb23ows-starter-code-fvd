<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use PHPUnit\Framework\TestCase;

class GrasshopperTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testWhenHorizontalJumpIsPossible_ShouldMakeHorizontalMove()
    {
        $board = [
            '0,0' => [['0', 'Q']],
            '-1,0' => [['0', 'G']],
            '1,0' => [['1', 'Q']],
            '1,1' => [['1', 'B']]
        ];
        Board::setBoard($board);
        GameController::move('-1,0', '2,0');
        $updatedBoard = Board::getBoard();

        $this->assertArrayNotHasKey('-1,0', $updatedBoard);
        $this->assertArrayHasKey('2,0', $updatedBoard);
        $this->assertEquals([['0', 'G']], $updatedBoard['2,0']);
    }

    public function testWhenDiagonalJumpIsPossible_ShouldMakeHorizontalMove()
    {
        $board = [
            '0,2' => [['1', 'B']],
            '0,1' => [['0', 'B']],
            '0,0' => [['0', 'Q']],
            '0,-1' => [['1', 'Q']],
            '0,-2' => [['1', 'B']],
            '0,-3' => [['0', 'G']]
        ];
        Board::setBoard($board);
        GameController::move('0,2', '0,-4');
        $updatedBoard = Board::getBoard();

        $this->assertArrayNotHasKey('0,2', $updatedBoard);
        $this->assertArrayHasKey('0,-4', $updatedBoard);
        $this->assertEquals([['0', 'G']], $updatedBoard['0,-4']);
    }
}
