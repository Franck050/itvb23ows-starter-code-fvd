<?php

class Hand
{
    public function __construct()
    {
    }

    public static function getHand(int $player = null)
    {
        if ($player === null) {
            return $_SESSION['hand'] ?? null;
        }
        $hand = $_SESSION['hand'][$player];
        if (!$hand) {
            $hand = self::resetHand()[$player];
            self::setHand($hand);
        }
        return $hand;
    }

    public static function setHand(array $hand)
    {
        $_SESSION['hand'] = $hand;
    }

    public static function resetHand(): array
    {
        return [
            0 => [
                "Q" => 1,
                "B" => 2,
                "S" => 2,
                "A" => 3,
                "G" => 3
            ],
            1 => [
                "Q" => 1,
                "B" => 2,
                "S" => 2,
                "A" => 3,
                "G" => 3
            ]
        ];
    }
}