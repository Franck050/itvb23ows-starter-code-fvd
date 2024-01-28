<?php

session_start();

include_once 'util.php';
include_once 'database.php';
include_once 'Game.php';
include_once 'GameComponents/Hand.php';
include_once 'GameComponents/Player.php';
include_once 'GameComponents/Board.php';

$board = Board::getBoard();

if (!isset($board)) {
    Game::restart();
    exit(0);
}

$player = Player::getPlayer();
$hand = Hand::getHand();

$to = [];
$player_tiles = $hand[$player];

foreach ($GLOBALS['OFFSETS'] as $pq) {
    foreach (array_keys($board) as $pos) {
        $pq2 = explode(',', $pos);
        $new_pos = ($pq[0] + $pq2[0]).','.($pq[1] + $pq2[1]);

        if (isValidPosition($new_pos, $board, $player) && count($player_tiles) > 0) {
            $to[] = $new_pos;
        }
    }
}
$to = array_unique($to);
if (!count($to)) {
    $to[] = '0,0';
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Hive</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
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
                    $pq[0];
                    $pq[1];
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
        <form method="post" action="play.php">
            <select name="piece">
                <?php
                    foreach ($hand[$player] as $tile => $ct) {
                        if ($ct > 0) {
                            echo "<option value=\"$tile\">$tile</option>";
                        }
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach (getMoves($board, $player) as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <input type="submit" value="Play">
        </form>
        <form method="post" action="move.php">
            <select name="from">
                <?php
                    foreach (array_keys($board) as $pos) {
                        if ($board[$pos][count($board[$pos]) - 1][0] == $player) {
                            echo "<option value=\"$pos\">$pos</option>";
                        }
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach ($to as $pos) {
                        if (isset($board[$pos]))
                            continue;
                        if (hasNeighbour($pos, $board) && neighboursAreSameColor($player, $pos, $board)) {
                            echo "<option value=\"$pos\">$pos</option>";
                        }
                    }
                ?>
            </select>
            <input type="submit" value="Move">
        </form>
        <form method="post" action="pass.php">
            <input type="submit" value="Pass">
        </form>
        <form method="post" action="restart.php">
            <input type="submit" value="Restart">
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
                $db = Database::getInstance();
                $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = '.$_SESSION['game_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_array()) {
                    echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
                }
            ?>
        </ol>
        <form method="post" action="undo.php">
            <input type="submit" value="Undo">
        </form>
    </body>
</html>

