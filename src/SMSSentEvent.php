<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2018/3/15
 * Time: 18:00
 */

namespace MMHK\SMS;

use MMHK\SMS\Contracts\MessageInterface;

class SMSSentEvent
{

    public
        $status,
        $message,
        $response,
        $params;
    /**
     * SMSEvent constructor.
     */
    public function __construct($status, MessageInterface $message, $response, $params = [])
    {
        $this->message = $message;
        $this->status = $status;
        $this->response = $response;
        $this->params = $params;
    }
}