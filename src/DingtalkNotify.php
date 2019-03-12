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
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string $apiUri DingTalk Api Uri
     */
    protected $apiUrl;

    public function __construct($token, $apiUri = '')
    {
        $this->token = $token;

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
            $response = $this->httpClient->post($this->apiUrl, [
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
     * @param $title
     * @param $text
     * @param $messageUrl
     * @param string $picUrl
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
