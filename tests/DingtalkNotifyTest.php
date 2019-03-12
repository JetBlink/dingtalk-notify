<?php

namespace Tests\JetBlink\DingtalkNotify;

use JetBlink\DingtalkNotify\DingtalkNotify;

class DingtalkNotifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DingtalkNotify
     */
    protected $dingTalk;

    protected function setUp()
    {
        parent::setUp();

        $this->dingTalk = new DingtalkNotify(getenv('DINGTALK_ROBOT_TOKEN'));
    }

    public function testSendMessage()
    {
        $this->dingTalk->sendMessage([
            'msgtype' => 'text',
            'text'    => [
                'content' => '这是一条测试消息。',
            ],
            'at'      => [
                'atMobiles' => [],
                'isAtAll'   => false,
            ]
        ]);
    }

    public function testSendTextMessage()
    {
        $this->dingTalk->sendTextMessage('这是一条文本测试消息。');
    }

    public function testSendMarkdownMessage()
    {
        $this->dingTalk->sendMarkdownMessage(
            'Markdown Test Title',
            "### Markdown 测试消息\n* 谷歌: [Google](https://www.google.com/)\n* 一张图片\n ![](https://avatars0.githubusercontent.com/u/40748346)"
        );
    }

    public function testSendLinkMessage()
    {
        $this->dingTalk->sendLinkMessage(
            'Link Test Title',
            "这是一条链接测试消息",
            'https://github.com/JetBlink',
            'https://avatars0.githubusercontent.com/u/40748346'
        );
    }
}
