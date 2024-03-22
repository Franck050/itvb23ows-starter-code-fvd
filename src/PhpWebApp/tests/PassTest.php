<?php

namespace tests;

use controllers\GameController;
use gameComponents\Board;
use gameComponents\Player;
use PHPUnit\Framework\TestCase;

class PassTest extends TestCase
{
    protected function setUp(): void
    {
        GameController::restart();
    }

    public function testPassFunctionality_PlayerMustPass_Succeeds() {
        // Arrange, Invalid game, just for testing purposes.
        GameController::play('Q', '0,0');
        Board::setPiece('-1,0', 'A');
        Board::setPiece('1,0', 'B');
        Board::setPiece('0,1', 'S');
        Board::setPiece('0,-1', 'G');
        Board::setPiece('-1,1', 'A');
        Board::setPiece('1,-1', 'S');
        Player::setPlayer(1 - Player::getPlayer());

        // Act
        GameController::pass();
        $currentPlayer = Player::getPlayer();

        // Asserts
        $this->assertEquals(0, $currentPlayer);
    }

    public function testPassFunctionality_PassIsNotAllowed_Fails() {
        // Arrange
        GameController::play('Q', '0,0');
        GameController::play('Q', '1,0');
        GameController::play('G', '0,-1');
        GameController::play('B', '2,0');

        // Act
        GameController::pass();
        $currentPlayer = Player::getPlayer();

        $this->assertEquals(0, $currentPlayer);
    }
}