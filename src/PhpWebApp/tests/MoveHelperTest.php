<?php

namespace tests;

use controllers\GameController;
use gameComponents\Hand;
use helpers\MoveHelper;
use PHPUnit\Framework\TestCase;

class MoveHelperTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testPlayerMustPlayQueenTest_QueenDoesNotHaveToPlayYet() {
        // Arrange
        GameController::play('B', '0,0');
        GameController::play('S', '0,1');
        GameController::play('S', '-1,0');
        GameController::play('S', '-1, 2');

        // Act
        $piece = 'B';
        $hand = Hand::getHand(0);

        // Assert
        $this->assertFalse(MoveHelper::playerMustPlayQueen($piece, $hand));
    }

    public function testPlayerMustPlayQueenTest_QueenMustBePlayed() {
        // Arrange
        GameController::play('B', '0,0');
        GameController::play('S', '0,1');
        GameController::play('S', '-1,0');
        GameController::play('S', '-1, 2');
        GameController::play('A', '0,-1');
        GameController::play('A', '0,2');

        // Act
        $piece = 'B';
        $hand = Hand::getHand(0);

        // Assert
        $this->assertTrue(MoveHelper::playerMustPlayQueen($piece, $hand));
    }

    public function testGetPositions_SecondMove_Succeed() {
        // Arrange
        GameController::play('Q', '0,0');

        // Act
        $expectedSecondMovePositions = ['0,1', '0,-1', '1,0', '-1,0', '-1,1', '1,-1'];
        $actualSecondMovePositions = MoveHelper::getPositions();

        sort($expectedSecondMovePositions);
        sort($actualSecondMovePositions);

        // Assert
        $this->assertEquals($expectedSecondMovePositions, $actualSecondMovePositions);
    }

    public function testValidateMove_MoveToSamePosition_Fails() {
        // Arrange
        $from = '0,0';
        $to = '0,0';

        // Act
        $result = MoveHelper::validateMove($from, $to);

        // Assert
        $this->assertFalse($result);
    }

    public function testValidateMove_EmptyFromPosition_Fails() {
        // Arrange
        $from = '0,0';
        $to = '0,1';

        // Act
        $result = MoveHelper::validateMove($from, $to);

        // Assert
        $this->assertFalse($result);
    }

    public function testValidateMove_TileNotOwnedByPlayer_Fails() {
        // Arrange
        GameController::play('Q', '0,0');
        $from = '0,0';
        $to = '0,1';

        // Act
        $result = MoveHelper::validateMove($from, $to);

        // Assert
        $this->assertFalse($result);
    }

    public function testValidateMove_QueenBeeNotBeenPlayed_Fails() {
        // Arrange
        GameController::play('B', '0,0');
        GameController::play('G', '0,1');
        $from = '0,0';
        $to = '1,0';

        // Act
        $result = MoveHelper::validateMove($from, $to);

        // Assert
        $this->assertFalse($result);
    }

    public function testValidateMove_TileBlockedByBeetle_Fails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '-1,0');
        GameController::play('A', '0,2');
        GameController::play('A', '0,-1');
        GameController::move('0,2', '-1,-1');
        GameController::move('-1,0', '-1,-1');
        $from = '-1,-1';
        $to = '1,0';

        // Act
        $result = MoveHelper::validateMove($from, $to);

        // Assert
        $this->assertFalse($result);
    }

    public function testValidateMove_AntToValidPosition_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('S', '0,-1');
        GameController::play('G', '-1,2');
        $from = '0,-1';
        $to = '-2,2';

        // Act
        $result = MoveHelper::validateMove($from, $to);

        // Assert
        $this->assertTrue($result);
    }
}
