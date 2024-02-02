<?php
//
//namespace tests;
//
//use GameController\Board;
//use PHPUnit\Framework\MockObject\Exception;
//use PHPUnit\Framework\TestCase;
////include_once '../GameComponents/Board.php';
//
//class MoveHelperTest extends TestCase
//{
//    private $moveHelper;
//    private $boardMock;
//    private $playerMock;
//    private $handMock;
//    private $gameControllerMock;
//
//    /**
//     * @throws Exception
//     */
//    protected function setUp(): void
//    {
//        $this->boardMock = $this->createMock(Board::class);
//        $this->playerMock = $this->createMock(\Player::class);
//        $this->handMock = $this->createMock(\Hand::class);
//        $this->gameControllerMock = $this->createMock(\GameController::class);
//
//        $this->moveHelper = new \MoveHelper();
//    }
//
//    public function testMove_WhiteWinsTheGame()
//    {
//        // White
//        $this->gameControllerMock->play('Q', '0,0');
//
//        // Black
//        $this->gameControllerMock->play('Q', '1,0');
//
//        // White
//        $this->gameControllerMock->play('A', '-1,0');
//
//        // Black
//        $this->gameControllerMock->play('B', '1,1');
//
//        // White
//        $this->gameControllerMock->play('A', '-2,0');
//
//        // Black
//        $this->gameControllerMock->play('B', '2,-1');
//
//        // White
//        $this->gameControllerMock->move('-2,0', '1,-1');
//
//        // Black
//        $this->gameControllerMock->play('A', '2,0');
//
//        // White (Winning move)
//        $this->gameControllerMock->move('-1,0', '0,1');
//
//        $this->assertTrue($this->gameControllerMock->checkWin(0));
//    }
//}