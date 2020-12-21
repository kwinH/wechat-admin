<?php
/**
 * Project: Project-HNB-20181108.
 * Author: Kwin
 * QQ:284843370
 * Email:kwinwong@hotmail.com
 */

namespace Kwin\WechatAdmin\Middleware;

use Closure;

class LoadExtensions
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
        config()->set('admin.extensions.light-box.enable', true);
        \Encore\Admin\Form::extend('largefile', \Encore\LargeFileUpload\LargeFileField::class);
        return $next($request);
    }
}