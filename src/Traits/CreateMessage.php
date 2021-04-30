<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2020/12/18
 * Time: 17:26
 */

namespace App\Services\SMS\Traits;


use App\Services\SMS\Message;

trait CreateMessage
{
    /**
     * @param $to
     * @param null $msg
     * @return \App\Services\SMS\Contracts\MessageInterface
     */
    public function createMessage($to, $msg = null) {
        return new Message([
            'to' => $to,
            'content' => $msg,
        ]);
    }
}