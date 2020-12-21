<?php

namespace Kwin\WechatAdmin\Controllers;

use \Kwin\WechatAdmin\Models\WechatQrCodeScanLog;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WechatQrcodeScanLogController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('二维码扫码记录')
            ->description('列表')
            ->body($this->grid());
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
            ->header('二维码扫码记录')
            ->description('详情')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('二维码扫码记录')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('二维码扫码记录')
            ->description('新增')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WechatQrCodeScanLog);

        $grid->id('Id');
        $grid->column('wxMembers.nickname', '扫码人');
        $grid->wechat_qrcode_id('二维码ID');
        $grid->wechat_qrcode_cate_id('二维码分组ID');
        $grid->created_at('扫码时间');

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->column(1 / 2, function ($filter) {
                $filter->equal('wechat_qrcode_id', '二维码ID');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->equal('wechat_qrcode_cate_id', '分组ID');
            });

        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WechatQrCodeScanLog::findOrFail($id));

        $show->id('Id');
        $show->user_id('User id');
        $show->wechat_qrcode_id('Wechat qrcode id');
        $show->wechat_qrcode_cate_id('Wechat qrcode cate id');
        $show->created_at('创建时间');
        $show->updated_at('修改时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WechatQrCodeScanLog);

        $form->number('user_id', 'User id');
        $form->number('wechat_qrcode_id', 'Wechat qrcode id');
        $form->number('wechat_qrcode_cate_id', 'Wechat qrcode cate id');

        return $form;
    }
}
