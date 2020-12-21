<?php

namespace Kwin\WechatAdmin\Controllers;

use Kwin\WechatAdmin\Models\WechatMessage;
use App\Http\Controllers\Controller;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class WechatMessageController extends Controller
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
            ->header('消息管理')
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
            ->header('消息管理')
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
            ->header('消息管理')
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
            ->header('消息管理')
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
        $grid = new Grid(new WechatMessage);

        $grid->id('Id');
        $grid->msg_type('消息类型');
        $grid->media_id('素材id');
        $grid->title('标题');
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
        $show = new Show(WechatMessage::findOrFail($id));

        $show->id('Id');
        $show->msg_type('消息类型');
        $show->media_id('素材id');
        $show->title('标题');
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
        $form = new Form(new WechatMessage);

        $form->select('msg_type', '消息类型')
            ->options(WechatMessageController::getType())
            ->addElementClass('msg_type');
        $form->text('media_id', '素材id')
            ->addElementClass('media_id');
        $form->text('title', '标题')->required();
        $form->textarea('description', '描述/内容');
        $form->hasMany('news_item', '图文', function (Form\NestedForm $form) {
            $form->text('title', '标题');
            $form->textarea('description', '描述');
            $form->file('image', '图片')->uniqueName();
            $form->text('url', '跳转URL');
        });
        //$form->textarea('news_item', 'News item');

        $form->saving(function (Form $form) {
            $newsItem = $form->news_item;
            if (is_array($newsItem)) {
                foreach ($newsItem as &$v) {
                    if (empty($v['id'])) {
                        unset($v['id']);
                    }
                }
                $form->input('news_item', $newsItem);
            }
        });

        $this->showMediaJs();
        return $form;
    }

    public static function getType()
    {
        return ['text' => '文本', 'image' => '图片', 'voice' => '语音', 'video' => '视频', 'news' => '图文'];
    }

    protected function showMediaJs()
    {
        Admin::script(
            <<<JS
            function showMediaId(val){
    switch (val) {
      case 'voice':
      case 'image':
      case 'video':
        $('.media_id').parents('.form-group').show();
        $('#title').parents('.form-group').show();
        $('#description').parents('.form-group').show();   
        
        $('.has-many-news_item').hide();
        $('.has-many-news_item').prev('hr').hide();
        $('.has-many-news_item').prev('hr').prev('.row').hide();
          break;
          case 'news':
          $('.has-many-news_item').prev('hr').show();
          $('.has-many-news_item').prev('hr').prev('.row').show();
          $('.has-many-news_item').show();
              
          $('.media_id').parents('.form-group').hide();
          //$('#title').parents('.form-group').hide();
          $('#description').parents('.form-group').hide();
          break;
          default:
        $('.media_id').parents('.form-group').hide();
        
        $('.has-many-news_item').hide();
        $('.has-many-news_item').prev('hr').hide();
        $('.has-many-news_item').prev('hr').prev('.row').hide();
        
   
        $('#title').parents('.form-group').show();
        $('#description').parents('.form-group').show();
        break;
    }
            }
            $(function(){
                 showMediaId($('.msg_type').val());
            });
$('.msg_type').on('change',function(){
 showMediaId($(this).val());
});
JS
        );
    }
}
