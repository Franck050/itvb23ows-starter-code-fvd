<?php

namespace controllers;

class AiController
{
    private static string $url = 'http://ai:5000/';

    public static function postToGetAiMove($board, $currentMove, $playersHand)
    {
        $content = [
            'move_number' => $currentMove,
            'hand' => $playersHand,
            'board' => $board
        ];
        return self::postRequest(self::$url, $content);
    }

    private static function postRequest($url, $content)
    {
        $httpHeader = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($content),
            ],
        ];

        return json_decode(
            file_get_contents($url,false, stream_context_create($httpHeader))
        );
    }
}
