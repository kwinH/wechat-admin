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

class AutoReplyController extends Controller
{
    use HasResourceActions;


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
            ->description('编辑')
            ->body($this->form());
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $model = new WechatEvent;

        $form = new Form(new WechatEvent);


        $form->hidden('key');
        $form->hidden('event')->default(2);
        $form->text('method', '执行方法')->help('namespace\\class@method');
        $form->select('wechat_message_id', '消息')->options(WechatMessage::select('id', 'title')->pluck('title', 'id'));

        $form->saved(function () {
            return redirect(route('auto_reply.create'));
        });

        if ($newModel = WechatEvent::where('event', 2)->first()) {
            $form->setAction(route('auto_reply.update', ['auto_reply' => $newModel->id]));

            return $form->edit($newModel->id);

        }

        return $form;
    }


}
