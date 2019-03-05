<?php

namespace MMHK\SMS\Gateways;

use MMHK\SMS\Contracts\GatewayInterface;
use MMHK\SMS\Contracts\MessageInterface;
use MMHK\SMS\SMSSentEvent;
use MMHK\SMS\Support\Config;
use MMHK\SMS\Traits\HasHttpRequest;
use MMHK\SMS\Traits\Util;
use MMHK\SMS\Job\SMSSendJob;

class AccessYou implements GatewayInterface
{
    use HasHttpRequest, Util;
    const
        ENDPOINT_URL = 'http://api.accessyou.com/sms/sendsms-utf8-senderid.php',
        ENDPOINT_LANG = 2, // tw 0; cn 1; unicode 2
        ENDPOINT_FORMAT = 'JSON';

    protected
        $queue = false,
        $config,
        $pretend;

    public function __construct($config)
    {
        $this->config = new Config($config);
        $this->pretend = config('sms-service.pretend');
        $this->queue = config('sms-service.queue', false);
    }


    public function send(MessageInterface $message, $params = []) {

        if ($this->queue) {
            dispatch(new SMSSendJob($message, $params));
        } else {
            $result = $this->sendSync($message);
            
            event(new SMSSentEvent($result[0], $result[1], $result[2], $params));

            return $result;
        }
    }
    /**
     * 发送SMS
     *
     * @param array $to 等待发送的号码
     * @param MessageInterface $message SMS内容
     * @param int $lang 发送语言编码--影响到内容的长度
     * @return array  'ret1' => success| erred,
     *                'ret2' => 短信内容
     *                'ret3' => 返回的结果
     */
    public function sendSync(MessageInterface $message)
    {
        $msg = self::unicode_get(self::convert(self::ENDPOINT_LANG, $message->getContent()));
        $phoneNumber = self::FixPhoneNumber($message->getTo());
        if (! $phoneNumber) {
            \Log::error('phone number is empty!');
        }
        // 伪装模式下不会真的发出去 判断开发测试环境，白名单pretend 值false
        if(! app()->environment(ENV_PRO)) {
            if(in_array($message->getTo(), config('sms-service.whitelist'))) {
                $this->pretend = false;
            }
        } else {
            $this->pretend = false;
        }

        if ($this->pretend) { // 伪装模式
            $msg = 'the sms has been sent to number:'. $message->getTo();
            return [self::STATUS_PRETEND, $message, json_encode(['msg' => $msg])];
        } else {
            $this->config->offsetSet('msg', $msg);
            $this->config->offsetSet('phone', $phoneNumber);

            $response = trim($this->get(self::ENDPOINT_URL, $this->config->all()));

            if (is_numeric($response)) {
                return [self::STATUS_SUCCESS, $message, json_encode(['sms_id' => $response])];
            }
        }
        return [self::STATUS_ERRED, $message, json_encode($response)];
    }

}