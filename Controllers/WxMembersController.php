<?php

namespace Kwin\WechatAdmin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Kwin\WechatAdmin\Models\WxMembers;


class WxMembersController extends Controller
{

    public static function getSex($val)
    {
        switch ($val) {
            case 1:
                return '男';
            case 2:
                return '女';
            default:
                return '未知';
        }
    }

    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户管理');
            $content->description('列表');

            $content->body($this->grid());
        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(WxMembers::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('nickname', '用户名');
            $grid->column('headimgurl', '头像')->image(null, 50);
            $grid->column('sex', '性别')->using(WxMembers::SEX_LIST);
            $grid->column('province', '省份');
            $grid->column('city', '城市');
            $grid->column('subscribe_scene', __('关注方式'))->using(WxMembers::SUBSCRIBE_SCENE);
            $grid->column('subscribe_time', '关注时间')->display(function ($val) {
                return $val == 0 ? '未关注' : date('Y-m-d H:i:s', $val);
            });
            $grid->column('remark', '备注');
            $grid->created_at('创建时间');

            //禁用新增按钮
            $grid->disableCreation()
                ->disableRowSelector();

            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();

            });


            $grid->filter(function (Grid\Filter $filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                $filter->like('nickname', '用户名');
                $filter->between('subscribe_time', '关注时间')->datetime();
            });

        });
    }


    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('用户管理')
            ->description('详情')
            ->body($this->detail($id));
    }


    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WxMembers::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('openid', __('用户的唯一标识'));
        $show->field('nickname', __('用户昵称'));
        $show->field('sex', __('用户的性别'))->using(WxMembers::SEX_LIST);
        $show->field('province', __('省份'));
        $show->field('city', __('城市'));
        $show->field('country', __('国家'));
        $show->field('headimgurl', __('用户头像'))->image();
        $show->field('remark', __('备注'));
        $show->field('language', __('微信用户语言'));
        $show->field('subscribe_time', __('关注时间'))->display(function ($val) {
            return $val == 0 ? '未关注' : date('Y-m-d H:i:s', $val);
        });
        $show->field('subscribe_scene', __('关注方式'))->using(WxMembers::SUBSCRIBE_SCENE);
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->panel()
            ->tools(function (\Encore\Admin\Show\Tools $tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });
        return $show;
    }
}
