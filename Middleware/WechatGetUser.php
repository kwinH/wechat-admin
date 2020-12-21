<?php

namespace Kwin\WechatAdmin\Middleware;

use Closure;

class WechatGetUser
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('wechat.oauth_user.default')) {
            return $next($request);
        }

        if (!isset($_GET['code'])) {
            $this->getCode(config('wechat.official_account.default'), url()->full());
        } else {
            $app = app('wechat.official_account');
            $user = $app->oauth->setRequest($request)->user();
            session()->put('wechat.oauth_user.default', $user);
        }


        return $next($request);
    }

    public function getCode(array $config, $redirect_uri)
    {
        //如果没有code，就获取code
        $url = config('wechat.oauthAuthorize_url') . '?'
            //"http://wxapp.jojin.com/api/connect/oauth2/authorize?"
            // $url = "https://open.weixin.qq.com/connect/oauth2/authorize?"
            . "appid=" . $config['app_id']
            . "&redirect_uri=" . urlencode($redirect_uri)
            . "&response_type=" . "code"
            . "&scope=" . "snsapi_userinfo"
            . "&state=" . ""
            . "#wechat_redirect";
        //echo $url;exit;

        header("Location: $url");
        exit;
    }

}
