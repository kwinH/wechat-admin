<?php

namespace Kwin\WechatAdmin\Controllers;

use Kwin\WechatAdmin\Models\WechatEvent;
use App\Http\Controllers\Controller;
use Kwin\WechatAdmin\Models\WechatMessage;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WechatEventController extends Controller
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
            ->header('事件管理')
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
            ->header('事件管理')
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
            ->header('事件管理')
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
            ->header('事件管理')
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
        $grid = new Grid(new WechatEvent);

        $grid->id('Id');
        $grid->title('事件标题');
        $grid->key('Key');
        $grid->event('事件类型')->using(WechatEvent::EVENTLIST);
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
        $show = new Show(WechatEvent::findOrFail($id));

        $show->id('Id');
        $show->title('事件标题');
        $show->key('Key');
        $show->event('事件类型')->using(WechatEvent::EVENTLIST);
        $show->method('执行方法');
        $show->message('消息', function ($message) {
            $message->id();
            $message->title('消息标题');
            $message->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableList();
                    $tools->disableDelete();
                });
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
        $form = new Form(new WechatEvent);

        $form->text('title', '事件标题')->help('起说明性作用');
        $form->text('key', 'Key')->default(md5(time()));
        $form->select('event', '事件类型')->options(WechatEvent::EVENTLIST);
        $form->text('method', '执行方法')->help('namespace\\class@method');
        $form->select('wechat_message_id', '消息')->options(WechatMessage::select('id', 'title')->pluck('title', 'id'));

        return $form;
    }
}
