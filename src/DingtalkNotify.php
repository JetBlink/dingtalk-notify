<?php

namespace JetBlink\DingtalkNotify;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;

class DingtalkNotify
{
    /**
     * @var string $token DingTalk Token
     */
    protected $token;

    /**
     * @var string $token DingTalk Token
     */
    protected $secret;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string $apiUri DingTalk Api Uri
     */
    protected $apiUrl;

    public function __construct($token, $secret = '', $apiUri = '')
    {
        $this->token = $token;

        $this->secret = $secret;

        if (empty($apiUri)) {
            $apiUri = 'https://oapi.dingtalk.com/robot/send?access_token=%s';
        }
        $this->apiUrl = sprintf($apiUri, $token);

        $this->httpClient = new HttpClient([
            // 'base_uri'      => $baseUri,
            'connect_timeout' => 30,
            'timeout'       => 10,
            'http_errors'   => false,
            // 'verify'        => false,
        ]);
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    protected function sign($timestamp)
    {
        $stringToSign = $timestamp . "\n" . $this->secret;
        $signData = base64_encode(hash_hmac('sha256', $stringToSign, $this->secret, true));
        return rawurlencode($signData);
    }

    /**
     * 发送原始消息
     *
     * @param array $msg 发送的消息内容
     *
     * @throws \Exception
     */
    public function sendMessage($msg)
    {
        try {
            if (! empty($this->secret)) {
                $timestamp = floor(microtime(true) * 1000);
                $url = $this->apiUrl . '&timestamp=' . $timestamp . '&sign=' . $this->sign($timestamp);
            } else {
                $url = $this->apiUrl;
            }
            $response = $this->httpClient->post($url, [
                RequestOptions::JSON => $msg,
            ]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \Exception('send dingTalk message failed, error: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new \Exception('send dingTalk message failed, error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        $code = $response->getStatusCode();
        $response = $response->getBody()->getContents();

        if ($code != 200) {
            throw new \Exception('send dingTalk message failed, http code: ' . $code);
        }

        try {
            $result = \GuzzleHttp\json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception('send dingTalk message failed, result: ' . $response);
        }

        if (! isset($result['errcode']) || $result['errcode'] != 0) {
            throw new \Exception('send dingTalk message failed, result: ' . $response);
        }

        return ;
    }

    /**
     * @param string $content 要发送的text消息
     * @param array $atMobiles 要@的手机号
     * @param bool $isAtAll 是否@所有人
     *
     * @throws \Exception
     */
    public function sendTextMessage($content, $atMobiles = [], $isAtAll = false)
    {
        $message = [
            'msgtype'   => 'text',
            'text'      => [
                'content' => $content
            ],
            'at'        => [
                'atMobiles' => $atMobiles,
                'isAtAll'   => $isAtAll,
            ]
        ];

        $this->sendMessage($message);
    }

    /**
     * @param string $title 标题
     * @param string $text 内容
     * @param array $atMobiles 要@的手机号
     * @param bool $isAtAll 是否@所有人
     *
     * @throws \Exception
     */
    public function sendMarkdownMessage($title, $text, $atMobiles = [], $isAtAll = false)
    {
        $message = [
            'msgtype'   => 'markdown',
            'markdown'  => [
                'title'     => $title,
                'text'      => $text,
            ],
            'at'        => [
                'atMobiles' => $atMobiles,
                'isAtAll'   => $isAtAll,
            ]
        ];

        $this->sendMessage($message);
    }

    /**
     * @param string $title 标题
     * @param string $text 消息内容
     * @param string $messageUrl 消息链接
     * @param string $picUrl 消息图片
     *
     * @throws \Exception
     */
    public function sendLinkMessage($title, $text, $messageUrl, $picUrl = '')
    {
        $message = [
            'msgtype'   => 'link',
            'link'      => [
                'title'         => $title,
                'text'          => $text,
                'messageUrl'    => $messageUrl,
                'picUrl'        => $picUrl,
            ]
        ];

        $this->sendMessage($message);
    }
}
