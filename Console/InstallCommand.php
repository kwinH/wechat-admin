<?php

namespace Kwin\WechatAdmin\Console;

use Encore\Admin\Auth\Database\Menu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use \Cache;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'wechatAdmin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the wechat_admin package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->installExtensions();
        $this->createMenus();
        $this->info('success!');
    }

    public function installExtensions()
    {
        if (!Cache::store('file')->get('installWechatAdminExtensions')) {
            $this->call('vendor:publish', ['--provider' => 'SmallRuralDog\LightBox\LightBoxServiceProvider']);
            $this->call('vendor:publish', ['--tag' => 'light-box']);
            $this->call('aetherupload:publish');
            $this->call('vendor:publish', ['--tag' => 'large-file-upload']);
            Cache::store('file')->forever('installWechatAdminExtensions', 'success');
        }
    }


    protected function createMenus()
    {
        if (!Schema::hasTable((new Menu)->getTable())) {

            $this->call('admin:install');
        } else {
            $this->call('migrate');
        }


        $parent = $this->createMenu([
            'parent_id' => 0,
            'title' => '微信公众号管理',
            'icon' => 'fa-bars',
            'uri' => '',
        ]);


        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '用户管理',
            'icon' => 'fa-bars',
            'uri' => '/wechat/members',
        ]);

        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '菜单管理',
            'icon' => 'fa-bars',
            'uri' => '/wechat/menu',
        ]);


        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '素材管理',
            'icon' => 'fa-bars',
            'uri' => '/wechat/material',
        ]);


        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '消息管理',
            'icon' => 'fa-bars',
            'uri' => '/wechat/message',
        ]);


        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '关键词回复',
            'icon' => 'fa-bars',
            'uri' => '/wechat/text',
        ]);

        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '自动回复',
            'icon' => 'fa-bars',
            'uri' => '/wechat/auto_reply/create',
        ]);

        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '事件管理',
            'icon' => 'fa-bars',
            'uri' => '/wechat/event',
        ]);

        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '二维码分组',
            'icon' => 'fa-bars',
            'uri' => '/wechat/qrcode_cate',
        ]);

        $this->createMenu([
            'parent_id' => $parent->id,
            'title' => '二维码列表',
            'icon' => 'fa-bars',
            'uri' => '/wechat/qrcode',
        ]);
    }

    protected function createMenu(array $data)
    {
        if ($data['parent_id'] == 0) {
            $menu = Menu::where('parent_id', 0)->where('title', '微信公众号管理')->first();
        } else {
            $menu = Menu::where('parent_id', $data['parent_id'])->where('uri', $data['uri'])->first();
        }

        if (empty($menu)) {
            $menu = Menu::create($data);

            $menu->roles()->make([
                'menu_id' => $menu->id,
                'role_id' => 1
            ]);
        }

        return $menu;
    }
}
