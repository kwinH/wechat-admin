<?php

namespace Kwin\WechatAdmin\Controllers;

use Kwin\WechatAdmin\Models\WechatMessage;
use \Kwin\WechatAdmin\Models\WechatQrCodeCate;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WechatQrcodeCateController extends Controller
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
            ->header('二维码分组')
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
            ->header('二维码分组')
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
            ->header('二维码分组')
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
            ->header('二维码分组')
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
        $grid = new Grid(new WechatQrCodeCate);

        $grid->id('Id');
        $grid->column('name', '分组名称')->display(function ($val) {
            return "<a href='/admin/wechat/qrcode?&cate%5Bname%5D={$val}'>{$val}</a>";
        });

        $grid->column('desc', '详情描述');
        $grid->column('method', '执行的方法');
        $grid->column('message.title', '消息标题');
        $grid->column('sweep_number', '扫码人数');

        $grid->actions(function ($actions) {
            $actions->append('<a class="btn btn-xs btn-primary" href="' . route('admin.wechat.qrcode_scan_log.index', ['wechat_qrcode_cate_id' => $actions->row->id]) . '">扫描记录</a>');
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
        $show = new Show(WechatQrCodeCate::findOrFail($id));

        $show->id('Id');
        $show->name('分组名称');
        $show->desc('详情描述');
        $show->sweep_number('扫码人数');
        $show->method('执行的方法');
        $show->message('消息', function ($message) {
            $message->id();
            $message->title('消息标题');
            $message->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableList();
                    $tools->disableDelete();
                });;
        });

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
        $form = new Form(new WechatQrCodeCate);

        $form->text('name', '分组名称');
        $form->text('desc', '详情描述');
        $form->text('method', '执行方法')->help('namespace\\class@method');
        $form->select('wechat_message_id', '消息')->options(WechatMessage::select('id', 'title')->pluck('title', 'id'));

        return $form;
    }
}
