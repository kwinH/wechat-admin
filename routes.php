<?php

use Illuminate\Routing\Router;

Route::any('/wechat', 'Kwin\WechatAdmin\WechatServe\WeChatController@serve');
Route::get('/wechat/authorize', 'Kwin\WechatAdmin\WechatServe\WeChatController@oauthAuthorize')->name('wechat.oauthAuthorize');
Route::get('/wechat/toSource', 'Kwin\WechatAdmin\WechatServe\WeChatController@toSource')->name('wechat.toSource');

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => 'Kwin\\WechatAdmin\\Controllers',
    'middleware' => array_merge((array)config('admin.route.middleware'), ['wechatAdmin']),
], function (Router $router) {
    $router->group([
        'prefix' => 'wechat',
    ], function (Router $router) {
        //用户列表
        $router->resource('/members', 'WxMembersController');
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
