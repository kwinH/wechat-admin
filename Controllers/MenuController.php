<?php

namespace Kwin\WechatAdmin\Controllers;

use Kwin\WechatAdmin\Models\WechatEvent;
use Kwin\WechatAdmin\Models\WechatMenu;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MenuController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $app = app('wechat.official_account');

        //  dd($app->material->stats());
        //dd($app->material->list('image', 0, 10));
        // dd($app->menu->list());
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.menu'));
            $content->description(trans('admin.list'));

            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());

                $obj = $this;
                $row->column(6, function (Column $column) use ($obj) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('wechat/menu'));

                    $obj->makeForm($form);

                    $form->hidden('_token')->default(csrf_token());

                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });
        });
    }

    /**
     * Redirect to edit page.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        return redirect()->route('menu.edit', ['id' => $id]);
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView()
    {

//        return WechatMenu::tree(function (Tree $tree) {
//            $tree->disableCreate();
//        });
        return WechatMenu::tree(function (Tree $tree) {
            $tree->disableCreate();
            jsConfirm();
            $tree->tools(function (Tree\Tools $tool) {
                $tool->add('<a class="btn btn-info btn-sm" onclick="confirmSwal(\'是否下载菜单\',\'' . route('admin.wechat.menu.down', [], 'put') . '\')">&nbsp;下载菜单</a>');
                $tool->add('<a class="btn btn-info btn-sm" style="margin-left: 10px;" onclick="confirmSwal(\'是否上传菜单\',\'' . route('admin.wechat.menu.upload', [], 'put') . '\')">&nbsp;上传菜单</a>');
            });


        });
    }

    /**
     * Edit interface.
     *
     * @param string $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin.menu'));
            $content->description(trans('admin.edit'));

            $content->row($this->form()->edit($id));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $obj = $this;
        return WechatMenu::form(function (Form $form) use ($obj) {
            $form->display('id', 'ID');
            $obj->makeForm($form);
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
        });
    }

    public function makeForm($form)
    {
        $form->select('parent_id', '父级菜单')->options(WechatMenu::where('parent_id', 0)->get()->pluck('name', 'id')->prepend('顶级菜单', 0));
        $form->text('name', '菜单名称')->rules('required');
        $form->radio('type', '菜单类型')->options(['view' => '跳转网页', 'click' => '点击事件']);
        $form->text('url', '跳转地址');
        $form->select('key', '事件')
            ->options(WechatEvent::select('key', 'title')
                ->pluck('title', 'key'))
            ->addElementClass('key');

        Admin::script(
            <<<JS
            function showOrHide(val){
    switch (val) {
      case 'view':
         $('#url').parents('.form-group').show();
         $('.key').parents('.form-group').hide();
          break;
          default:
             $('#url').parents('.form-group').hide();
         $('.key').parents('.form-group').show();
        break;
    }
}
            $(function(){
                showOrHide($('.type').val());
            });
        $('.type').on('ifChecked',function(){
        showOrHide($(this).val());
        });
JS
        );

    }


    /**
     * Help message for icon field.
     *
     * @return string
     */
    protected function iconHelp()
    {
        return 'For more icons please see <a href="http://fontawesome.io/icons/" target="_blank">http://fontawesome.io/icons/</a>';
    }


    public function store(Request $request)
    {
        $parentId = $request->get('parent_id');
        $count = WechatMenu::where('parent_id', $parentId)->count();

        if ($count == 3 && $parentId == 0) {
            $error = '顶级菜单不能超过3个';
            return back()->withErrors(['parent_id' => $error]);
        } else if ($count == 5 && $parentId != 0) {
            $error = '二级菜单不能超过5个';
            return back()->withErrors(['parent_id' => $error]);
        }

        $data = $request->only('parent_id', 'name', 'type', 'uri');
        return $this->form()->store();
    }

    protected
    function type()
    {
        return ['view' => '跳转网页', 'click' => '点击事件'];
        //  return ['click' => '点击事件', 'view' => '跳转网页', 'scancode_push'=>'扫一扫', 'scancode_waitmsg'=>'', 'pic_sysphoto'=>'拍照发图', 'pic_photo_or_album'=>'弹出拍照或者相册', 'pic_weixin'=>'微信相册', 'location_select'=>'微信相册', 'media_id'=>'下发素材', 'view_limited'=>'跳转图文消息'];
    }


    public function down()
    {
        WechatMenu::where([])->delete();
        $app = app('wechat.official_account');
        $nemuList = $app->menu->list();
        $nemuList = $nemuList['menu'];
        \Log::info($nemuList);
        foreach ($nemuList['button'] as $v) {
            $data = [
                'parent_id' => 0,
                'name' => $v['name'],
                'type' => $v['type'] ?? '',
                'url' => $v['url'] ?? '',
                'key' => $v['key'] ?? '',
            ];
            $wechatMenu = new WechatMenu($data);
            $wechatMenu->save();
            if (!empty($v['sub_button'])) {
                foreach ($v['sub_button'] as $vv) {
                    $data = [
                        'parent_id' => $wechatMenu->id,
                        'name' => $vv['name'],
                        'type' => $vv['type'] ?? '',
                        'url' => $vv['url'] ?? '',
                        'key' => $vv['key'] ?? '',
                        'media_id' => $vv['media_id'] ?? ''
                    ];

                    $subWechatMenu = new WechatMenu($data);
                    $subWechatMenu->save();
                }
            }
        }
        return response()->json([
            'status' => true,
            'message' => '操作成功',
        ]);
    }

    public function upload()
    {
        $res = [];
        $data = WechatMenu::where('parent_id', 0)->orderBy('sort', 'asc')->get();
        foreach ($data as $v) {
            $button = [
                "type" => $v->type,
                "name" => $v->name,
                "key" => $v->key,
                "url" => $v->url,
                'media_id' => $v->media_id,
                "sub_button" => [],
            ];

            $subData = WechatMenu::where('parent_id', $v->id)->orderBy('sort', 'asc')->get();
            foreach ($subData as $vv) {
                $button['sub_button'][] = [
                    "type" => $vv->type,
                    "name" => $vv->name,
                    "key" => $vv->key,
                    "url" => $vv->url,
                    'media_id' => $vv->media_id,
                    "sub_button" => [],
                ];
            }
            $res[] = $button;
        }

        $app = app('wechat.official_account');
        $res = $app->menu->create($res);
        \Log::info('上传菜单');
        \Log::info($res);
        if ($res['errcode'] === 0) {
            return response()->json([
                'status' => true,
                'message' => '操作成功',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => $res['errmsg'],
            ]);
        }
    }
}
