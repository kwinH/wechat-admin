<?php

namespace Kwin\WechatAdmin\WechatServe;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Kwin\WechatAdmin\WechatServe\ReplyHandler\MessageReplyHandler;


class WeChatController extends Controller
{
    public function __construct()
    {
        //\Debugbar::disable();
    }


    public function oauthAuthorize(Request $request)
    {
        $appId = $request->get('appid', config('wechat.official_account.default.app_id'));
        $redirectUri = urlencode(
            route('wechat.toSource',
                ['redirect_uri' => $request->get('redirect_uri', '')]
            )
        );
        $responseType = $request->get('response_type', 'code');
        $scope = $request->get('scope', 'snsapi_base');
        $state = $request->get('state', '');

        return redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appId}&redirect_uri={$redirectUri}&response_type={$responseType}&scope={$scope}&state={$state}#wechat_redirect");

    }

    public function toSource()
    {
        if (isset($_GET['redirect_uri'])) {
            $redirect_uri = urldecode($_GET['redirect_uri']);
            unset($_GET['redirect_uri']);

            $inputs = http_build_query($_GET);

            if (strstr($redirect_uri, '?') !== false) {
                $redirect_uri .= '&' . $inputs;
            } else {
                $redirect_uri .= '?' . $inputs;
            }

            return redirect("$redirect_uri");
        }

    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived. ' . time()); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');
        $app->server->push(function ($message) use ($app) {
            /**
             * array (
             * 'ToUserName' => 'gh_b841d951c62f',
             * 'FromUserName' => 'oxgMF0np-7kwW2lUoSWNfgnukApQ',
             * 'CreateTime' => '1519786839',
             * 'MsgType' => 'text',
             * 'Content' => '1',
             * 'MsgId' => '6527434770819729077',
             * )
             */

            return null;
        });

        $app->server->push(MessageReplyHandler::class);

        Log::info('hand end. ' . time());
        return $app->server->serve();
    }

}