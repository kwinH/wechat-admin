<?php

namespace Kwin\WechatAdmin;

use Illuminate\Support\ServiceProvider;

class WechatAdminServiceProvider extends ServiceProvider
{
    /**
     * 控制台命令
     * @var array
     */
    protected $commands = [
        Console\InstallCommand::class,
    ];

    /**
     * 前台的路由中间件
     *
     * @var array
     */
    protected $routeMiddleware = [
        'loadExtensions' => \Kwin\WechatAdmin\Middleware\LoadExtensions::class,

        'wechat.getUser' => \Kwin\WechatAdmin\Middleware\WechatGetUser::class,
        'wechat.SimulatedAuthorization' => \Kwin\WechatAdmin\Middleware\SimulatedAuthorization::class,
        'wechat.oauth' => \Overtrue\LaravelWeChat\Middleware\OAuthAuthenticate::class,
        'wechat.authenticate' => \Kwin\WechatAdmin\Middleware\WxAuthenticate::class,
        'wechat.VisitLogmiddleware' => \Kwin\WechatAdmin\Middleware\VisitLogmiddleware::class,
    ];

    /**
     * 前台的路由中间件组
     *
     * @var array
     */
    protected $middlewareGroups = [
        'wechatAdmin' => [
            'loadExtensions'
        ],

        'wechat' => [
            //'wechat.SimulatedAuthorization',
            //'wechat.oauth:default,snsapi_userinfo',
            'wechat.getUser',
            'wechat.authenticate',
            'wechat.VisitLogmiddleware'
        ]
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if (file_exists($routes = __DIR__ . DIRECTORY_SEPARATOR . 'routes.php')) {
            $this->loadRoutesFrom($routes);
        }
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'Views', 'wechatAdmin');
        $this->loadMigrationsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //注册中间件
        $this->registerRouteMiddleware();

        //注册控制台命令
        $this->commands($this->commands);

    }

    /**
     * 注册路由器
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // 注册路由中间件
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // 注册中间件组
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
}
