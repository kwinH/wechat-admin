<?php

namespace Kwin\WechatAdmin\Middleware;


use  Kwin\WechatAdmin\Models\WxMembers;
use Closure;

class WxAuthenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $app = app('wechat.official_account');

        $user = session('wechat.oauth_user.default'); // 拿到授权用户资料

        $WxUser = $app->user->get($user->getId());

        $user = $user['original'];
        
        $user = array_only($WxUser, ['openid', 'nickname', 'sex', 'province', 'city', 'country', 'headimgurl', 'language', 'subscribe_time', 'subscribe_scene']);

        $user = WxMembers::updateOrCreate(['openid' => $user['openid']], $user);

        session()->put('wxMember', $user);
        \View::share('user', $user);
        \View::share('urlPath', \Request::path());
        return $next($request);
    }


}
