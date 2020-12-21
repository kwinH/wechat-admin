<?php

namespace Kwin\WechatAdmin\Middleware;


use Kwin\WechatAdmin\Models\VisitLog;
use Closure;

class VisitLogmiddleware
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
        $wxMember = session('wxMember');
        $data = [
            'wx_members_id' => $wxMember['id'],
            'path' => $request->path(),
            'method' => $request->method(),
            'ip' =>  sprintf("%u", ip2long($request->ip())),
            'input' => json_encode($request->all())
        ];

        VisitLog::create($data);
        return $next($request);
    }


}
