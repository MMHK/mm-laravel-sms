<?php
namespace MMHK\SMS\Contracts;

interface GatewayInterface
{
    /**
     * 伪装发出
     */
    const STATUS_ERRED = 0;
    /**
     * 成功发出
     */
    const STATUS_SUCCESS = 1;
    /**
     * 发送失败
     */
    const STATUS_PRETEND = 2;

    /**
     * 发送公开接口
     * @param MessageInterface $message
     * @return array
     */
    public function send(MessageInterface $message);

    /**
     * 内部发送接口
     * @param MessageInterface $message
     * @return array
     */
    function sendSync(MessageInterface $message);

    /**
     * @param $to
     * @param $content
     * @return MessageInterface
     */
    public function createMessage($to, $content);
}
