<?php

namespace tests;

use controllers\GameController;
use PHPUnit\Framework\TestCase;

class CheckWinTest  extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testCheckWin_PlayerWhiteWins_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('B', '-1,0');
        GameController::play('B', '1,1');
        GameController::play('G', '0,-1');
        GameController::play('A', '-1,2');
        GameController::move('0,-1', '0,2');
        GameController::play('A', '2,1');
        GameController::play('A', '0,-1');
        GameController::move('2,1', '1,0');
        GameController::play('A', '-1,-1');
        GameController::play('A', '2,1');
        GameController::move('-1,-1', '-1,1');

        // Act
        $result = GameController::checkWin(0);

        // Assert
        $this->assertTrue($result);
    }
    public function testCheckWin_PlayerBlackWins_Succeeds() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '0,1');
        GameController::play('A', '-1,0');
        GameController::play('A', '1,1');

        GameController::play('G', '0,-1');
        GameController::play('A', '-1,2');
        GameController::play('A', '1,-1');
        GameController::play('A', '0,2');
        GameController::play('A', '-1,-1');
        GameController::move('0,2', '-1,1');
        GameController::move('-1,-1', '1,0');

        // Act
        $result = GameController::checkWin(1);

        // Assert
        $this->assertTrue($result);
    }
}