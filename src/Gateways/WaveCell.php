<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/3/5
 * Time: 11:45
 */

namespace MMHK\SMS\Gateways;


use App\Services\SMS\Traits\CreateMessage;
use MMHK\SMS\Contracts\GatewayInterface;
use MMHK\SMS\Contracts\MessageInterface;
use MMHK\SMS\Job\SMSSendJob;
use MMHK\SMS\SMSSentEvent;
use MMHK\SMS\Support\Config;
use MMHK\SMS\Traits\HasHttpRequest;
use MMHK\SMS\Traits\Util;

class WaveCell implements GatewayInterface
{

    use HasHttpRequest, Util, CreateMessage;
    const
        STATUS_QUEUED = 'QUEUED',
        STATUS_REJECTED = 'REJECTED',
        ENDPOINT_URL = 'https://api.wavecell.com/sms/v1/%s/single';

    protected
        $queue = false,
        $config,
        $pretend;

    /**
     * WaveCell constructor.
     */
    public function __construct($config)
    {
        $this->config = new Config($config);
        $this->pretend = config('sms-service.pretend');
        $this->queue = config('sms-service.queue', false);
    }


    /**
     * 发送公开接口
     * @param MessageInterface $message
     * @return array
     */
    public function send(MessageInterface $message, $params = [])
    {
        if ($this->queue) {
            dispatch(new SMSSendJob($message, $params));
        } else {
            $result = $this->sendSync($message);

            event(new SMSSentEvent($result[0], $result[1], $result[2], $params));

            return $result;
        }
    }

    /**
     * 内部发送接口
     * @param MessageInterface $message
     * @return array
     */
    function sendSync(MessageInterface $message)
    {
        $phoneNumber = self::fixPhoneNumber($message->getTo());
        if (! $phoneNumber) {
            \Log::error('phone number is empty!');
        }
        /**
         * 伪装模式下不会真的发出去 判断开发测试环境，白名单pretend 值false
         */
        if (!app()->environment(ENV_PRO)) {
            if (in_array($message->getTo(), config('sms-service.whitelist'))) {
                $this->pretend = false;
            }
        } else {
            $this->pretend = false;
        }
        if ($this->pretend) { // 是伪装模式
            $msg = 'the sms has been sent to number:' . $message->getTo();
            \Log::info($msg);
            return [self::STATUS_PRETEND, $message, json_encode(['msg' => $msg])];
        } else { // 不是伪装模式
            $this->config->offsetSet('MessageBody', $message->getContent());
            $this->config->offsetSet('MessageReceiver', $phoneNumber);
            $endpoint = sprintf(self::ENDPOINT_URL, $this->config->get('account_id'));
            $response = $this->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' .$this->config->get('api_key'),
                ],
                'json' => [
                    'source' => $this->config->get('sender_id'),
                    'destination' => $phoneNumber,
                    'text' => $message->getContent(),
                ],
            ]);
            $code = !empty($response['status']) ? (!empty($response['status']['code']) ? $response['status']['code'] : null) : null;
            if ($code == self::STATUS_QUEUED) {
                return [self::STATUS_SUCCESS, $message, json_encode($response)];
            }
            return [self::STATUS_ERRED, $message, json_encode($response)];
        }
    }
}