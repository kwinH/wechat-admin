<?php

use Illuminate\Routing\Router;


Route::group([
    'prefix' => 'wechat',
    'namespace' => 'Kwin\\WechatAdmin\\WechatServe',
], function (Router $router) {
    $router->any('/', 'WeChatController@serve');
    $router->get('/authorize', 'WeChatController@oauthAuthorize')->name('wechat.oauthAuthorize');
    $router->get('/toSource', 'WeChatController@toSource')->name('wechat.toSource');
    $router->get('/config', 'WeChatController@jssdkConfig'); //获取jssdk配置数组
});

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => 'Kwin\\WechatAdmin\\Controllers',
    'middleware' => array_merge((array)config('admin.route.middleware'), ['wechatAdmin']),
], function (Router $router) {
    $router->group([
        'prefix' => 'wechat',
    ], function (Router $router) {
        //用户列表
        $router->resource('/members', 'WxMembersController')->only('index', 'show');
        $router->put('/menu/upload', 'MenuController@upload')->name('admin.wechat.menu.upload');
        $router->put('/menu/down', 'MenuController@down')->name('admin.wechat.menu.down');
        $router->resource('/menu', 'MenuController');
        $router->resource('/material', 'MaterialController');
        $router->resource('/message', 'WechatMessageController');
        $router->resource('/text', 'WechatTextController');
        $router->resource('/event', 'WechatEventController');
        $router->resource('/auto_reply', 'AutoReplyController')->only('create', 'store', 'update');
        $router->resource('qrcode_cate', 'WechatQrcodeCateController');
        $router->resource('qrcode', 'WechatQrcodeController')->only('create', 'store', 'index');
        $router->get('qrcode_scan_log', 'WechatQrcodeScanLogController@index')
            ->name('admin.wechat.qrcode_scan_log.index');
    });
});
