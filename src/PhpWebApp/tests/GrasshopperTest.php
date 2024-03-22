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

    public function testMove_Grasshopper_LeftToRight_MoveSucceeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '0,-1');
        GameController::play('A', '0,2');
        GameController::play('G', '-1,0');
        GameController::move('0,2', '1,0');

        // Act
        GameController::move('-1,0', '2,0');

        // Assert
        $this->assertArrayHasKey('2,0', Board::getBoard());
        $this->assertArrayNotHasKey('-1,0', Board::getBoard());
    }

    public function testMove_Grasshopper_LeftToRight_MoveFails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '0,-1');
        GameController::play('A', '0,2');
        GameController::play('G', '-1,0');
        GameController::move('0,2', '1,0');

        // Act
        GameController::move('-1,0', '3,0');

        // Assert
        $this->assertArrayNotHasKey('3,0', Board::getBoard());
        $this->assertArrayHasKey('-1,0', Board::getBoard());
    }


    public function testMove_Grasshopper_ToLeftToBottomRight_MoveSucceeds()
    {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '1,0');
        GameController::play('G', '0,-1');
        GameController::play('B', '2,0');

        // Act
        GameController::move('0,-1', '0,1');

        // Assert
        $this->assertArrayHasKey('0,1', Board::getBoard());
        $this->assertArrayNotHasKey('0,-1', Board::getBoard());
    }

    public function testMove_Grasshopper_TopLeftToBottomRight_MoveFails()
    {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '1,0');
        GameController::play('G', '0,-1');
        GameController::play('B', '2,0');

        // Act
        GameController::move('0,-1', '0,2');

        // Assert
        $this->assertArrayHasKey('0,-1', Board::getBoard());
        $this->assertArrayNotHasKey('0,2', Board::getBoard());
    }

    public function testMove_Grasshopper_JumpOnExistedTile_MoveFails()
    {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '1,0');
        GameController::play('G', '0,-1');
        GameController::play('B', '2,0');

        // Act
        GameController::move('0,-1', '2,0');

        // Assert
        $this->assertArrayHasKey('0,-1', Board::getBoard());
        $this->assertArrayHasKey('2,0', Board::getBoard());
        $this->assertEquals('G', Board::getBoard()['0,-1'][0][1]);
    }
}
