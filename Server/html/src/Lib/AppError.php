<?php
namespace app\Lib;

class AppError {
    public static function handleError($code, $description, $file = null,
        $line = null, $context = null) {
        $err = "$description, $file, Line: $line";
        exec('curl -X POST -H "Content-Type: application/json" -d \'{"secret":"*Facility:* '.env('FACILITY_ID').' '.env('FACILITY_NAME').' *PHP Eror:* '.$err.'"}\' https://bloombot.herokuapp.com/hubot/chatsecrets/'.env('CHATROOM_ID'));

    }
}