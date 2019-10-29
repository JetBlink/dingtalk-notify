# DingTalk Notify
钉钉机器人通知(支持加签）。dingtalk robot notification sdk.

## Overview

* [Installation](#Installation)
* [Usage](#Usage)
  * [获取实例](#获取实例)
  * [发送消息](#发送消息)
    * [发送原始消息](#发送原始消息)
    * [发送文本消息](#发送文本消息)
    * [发送Markdown消息](#发送markdown消息)
    * [发送链接消息](#发送链接消息)
  * [Tips](#Tips)
* [官方文档](#官方文档)
* [License](#license)

## Installation

```
composer require jetblink/dingtalk-notify -vvv
```

## Usage

### 获取实例

  ```
$dingTalk = new \JetBlink\DingtalkNotify\DingtalkNotify('your_dingtalk_robot_token');
  ```

### 发送消息

#### 发送原始消息

```
$dingTalk->sendMessage([
    'msgtype' => 'text',
    'text'    => [
        'content' => '这是一条测试消息。',
    ],
    'at'      => [
        'atMobiles' => [],
        'isAtAll'   => false,
    ]
]);
```

#### 发送文本消息

```
$dingTalk->sendTextMessage('这是一条文本测试消息。');
```

#### 发送Markdown消息

```
$dingTalk->sendMarkdownMessage(
    'Markdown Test Title',
    "### Markdown  <font color='#ff0000'>测试消息</font>"
);
```

#### 发送链接消息

```
$dingTalk->sendLinkMessage(
    'Link Test Title',
    "这是一条链接测试消息",
    'https://github.com/JetBlink',
    'https://avatars0.githubusercontent.com/u/40748346'
);
```

### Tips

文本消息和Markdown消息都支持**@指定手机号**和**@所有人**，参数位置见具体方法。

## 官方文档

* [自定义机器人](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.karFPe&treeId=257&articleId=105735&docType=1)
* [消息类型及数据格式](https://ding-doc.dingtalk.com/doc#/serverapi2/qf2nxq)
> 每个机器人每分钟最多发送**20条**。

## License

[MIT](https://opensource.org/licenses/MIT)
