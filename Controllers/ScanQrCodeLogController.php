<?php

namespace App\Admin\Controllers;

use App\Models\QrCodeType;
use App\Models\ScanQrCodeLog;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class ScanQrCodeLogController extends Controller
{
    use ModelForm;

    protected $actionName;

    public function __construct()
    {

    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('二维码管理');
            $content->description('二维码扫码记录');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('二维码管理');
            $content->description('二维码类型');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ScanQrCodeLog::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('wxMembers.nickname', '扫码人');
            $grid->column('qrcode_id', '二维码ID');
            $grid->column('action_code', '活动编号');

            $grid->created_at();

            //禁用新增按钮
            $grid->disableCreation();
            //禁用行操作列
            $grid->disableActions();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(ScanQrCodeLog::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
