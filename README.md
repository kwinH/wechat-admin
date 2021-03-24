# 介绍
基于laravel admin做的微信公众号管理后台

# 安装
```shell script
composer require kwin/wechat-admin
php artisan wechatAdmin:install
```

#使用
## 配置.env
```
WECHAT_OFFICIAL_ACCOUNT_APPID=开发者ID(AppID)
WECHAT_OFFICIAL_ACCOUNT_SECRET=开发者密码(AppSecret)
WECHAT_OFFICIAL_ACCOUNT_TOKEN=令牌(Token)
WECHAT_OFFICIAL_ACCOUNT_AES_KEY=消息加解密密钥(EncodingAESKey)
```


## 鉴权 获取code
- 请求获取code地址
跳转地址:/wechat/authorize?redirect_uri="+escape("http://xxx")
>http://xxx 表示当前页面地址
- 启动`wechat`中间件组，即可自动保存微信用户信息

## 服务器配置
服务器地址(URL)配置为`{网址地址}/wechat`

## 后台菜单
- 微信公众号管理

  - 用户管理

  - 菜单管理

  - 素材管理

  - 消息管理

  - 关键词管理

  - 自动回复

  - 事件管理

  - 二维码分组

  - 二维码列表
