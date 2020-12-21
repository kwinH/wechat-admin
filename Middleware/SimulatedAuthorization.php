<?php

namespace Kwin\WechatAdmin\Middleware;


use Closure;
use Overtrue\Socialite\User as SocialiteUser;

class SimulatedAuthorization
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
        $user = array(
            'openid' => 'oxgMF0np-7kwW2lUoSWNfgnukApQ',
            'nickname' => '黑月',
            'sex' => 1,
            'language' => 'zh_CN',
            'city' => '温州',
            'province' => '浙江',
            'country' => '中国',
            'headimgurl' => 'http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLAvtsZsukLwqNCpiatV7DsKqxJciajnsiab4vZMicDaP9qoekz5zA6OdnsGaGTITYBAfhAUhicj0o7ZSA/132',
            'privilege' =>
                array(),
        );
        $user = new SocialiteUser([
            'id' => array_get($user, 'openid'),
            'name' => array_get($user, 'nickname'),
            'nickname' => array_get($user, 'nickname'),
            'avatar' => array_get($user, 'headimgurl'),
            'email' => null,
            'original' => $user,
            'provider' => 'WeChat',
        ]);
        session(['wechat.oauth_user.default' => $user]);

        return $next($request);
    }


}
