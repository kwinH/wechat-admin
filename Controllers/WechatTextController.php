<?php

namespace Kwin\WechatAdmin\Controllers;

use Kwin\WechatAdmin\Models\WechatMessage;
use Kwin\WechatAdmin\Models\WechatText;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WechatTextController extends Controller
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
            ->header('自动回复')
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
            ->header('自动回复')
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
            ->header('自动回复')
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
            ->header('自动回复')
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
        $grid = new Grid(new WechatText);

        $grid->id('Id');
        $grid->type('匹配方式')->using(WechatTextController::getType());
        $grid->key('关键词');
        $grid->method('执行方法');
        $grid->column('message.title', '消息标题');
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');

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
        $show = new Show(WechatText::findOrFail($id));

        $show->id('Id');
        $show->type('匹配方式')->using(WechatTextController::getType());
        $show->key('关键词');
        $show->method('执行方法');
        $show->wechat_message_id('消息ID');
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
        $form = new Form(new WechatText);

        $form->select('type', '匹配方式')->options(WechatTextController::getType());
        $form->text('key', '关键词');
        $form->text('method', '执行方法')->help('namespace\\class@method');
        $form->select('wechat_message_id', '消息')->options(WechatMessage::select('id', 'title')->pluck('title', 'id'));

        return $form;
    }

    public static function getType()
    {
        return [1 => '全匹配', 2 => '半匹配', 3 => '正则匹配'];
    }
}
