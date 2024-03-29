<?php

session_start();

require_once './vendor/autoload.php';

use controllers\GameController;
use controllers\DatabaseController;
use gameComponents\Board;
use gameComponents\Hand;
use gameComponents\Player;
use helpers\MoveHelper;

$board = Board::getBoard();

if (GameController::isGameStarted()) {
    GameController::restart();
}

$player = Player::getPlayer();
$hand = Hand::getHand();

$to = MoveHelper::getPositions();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Hive</title>
        <link rel="stylesheet" href="styles/index.css">
    </head>
    <body>
        <h1>
            <?php
            $winnerWhite = GameController::checkWin(0);
            $winnerBlack = GameController::checkWin(1);

            if ($winnerWhite && $winnerBlack) {
                echo "Draw!";
            } else {
                if ($winnerWhite) {
                    echo "White wins!";
                } elseif ($winnerBlack) {
                    echo "Black wins!";
                }
            }
            ?>
        </h1>
        <div class="board">
            <?php
                $min_p = 1000;
                $min_q = 1000;
                foreach ($board as $pos => $tile) {
                    $pq = explode(',', $pos);
                    if ($pq[0] < $min_p) {
                        $min_p = $pq[0];
                    }
                    if ($pq[1] < $min_q) {
                        $min_q = $pq[1];
                    }
                }
                foreach (array_filter($board) as $pos => $tile) {
                    $pq = explode(',', $pos);
                    $h = count($tile);
                    echo '<div class="tile player';
                    echo $tile[$h-1][0];
                    if ($h > 1) {
                        echo ' stacked';
                    }
                    echo '" style="left: ';
                    echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
                    echo 'em; top: ';
                    echo ($pq[1] - $min_q) * 4;
                    echo "em;\">($pq[0],$pq[1])<span>";
                    echo $tile[$h-1][1];
                    echo '</span></div>';
                }
            ?>
        </div>
        <div class="hand">
            White:
            <?php
                foreach ($hand[0] as $tile => $ct) {
                    for ($i = 0; $i < $ct; $i++) {
                        echo '<div class="tile player0"><span>'.$tile."</span></div> ";
                    }
                }
            ?>
        </div>
        <div class="hand">
            Black:
            <?php
            foreach ($hand[1] as $tile => $ct) {
                for ($i = 0; $i < $ct; $i++) {
                    echo '<div class="tile player1"><span>'.$tile."</span></div> ";
                }
            }
            ?>
        </div>
        <div class="turn">
            Turn: <?php
            if ($player == 0) {
                echo "White";
            } else {
                echo "Black";
            } ?>
        </div>
        <form method="post" action="router.php">
            <label>
                <select name="piece">
                    <?php
                        foreach ($hand[$player] as $tile => $ct) {
                            if ($ct > 0) {
                                echo "<option value=\"$tile\">$tile</option>";
                            }
                        }
                    ?>
                </select>
            </label>
            <label>
                <select name="to">
                    <?php
                        foreach (Movehelper::getPossibleMoves() as $pos) {
                            echo "<option value=\"$pos\">$pos</option>";
                        }
                    ?>
                </select>
            </label>
            <input type="submit" name="action" value="Play">
        </form>
        <form method="post" action="router.php">
            <label>
                <select name="from">
                    <?php
                        foreach (array_keys($board) as $pos) {
                            if (Player::playerPositions($board, $player, $pos)) {
                                echo "<option value=\"$pos\">$pos</option>";
                            }
                        }
                    ?>
                </select>
            </label>
            <label>
                <select name="to">
                    <?php
                        foreach (Movehelper::getPositions() as $pos) {
                            echo "<option value=\"$pos\">$pos</option>";
                        }
                    ?>
                </select>
            </label>
            <input type="submit" name="action" value="Move">
        </form>
        <form method="post" action="router.php">
            <input type="submit" name="action" value="Pass">
        </form>
        <form method="post" action="router.php">
            <input type="submit" name="action" value="AiMove">
        </form>
        <form method="post" action="router.php">
            <input type="submit" name="action" value="Restart">
        </form>
        <strong>
            <?php
                if (isset($_SESSION['error'])) {
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                }
            ?>
        </strong>
        <ol>
            <?php
                $db = DatabaseController::getInstance();
                $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = '.$_SESSION['game_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_array()) {
                    echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
                }
            ?>
        </ol>
        <form method="post" action="router.php">
            <input type="submit" name="action" value="Undo">
        </form>
    </body>
</html>
