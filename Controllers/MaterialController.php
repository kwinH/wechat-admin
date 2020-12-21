<?php

namespace Kwin\WechatAdmin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kwin\WechatAdmin\Models\Material;

class MaterialController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        //dd('aaa');
        if (!in_array($request->get('type', 'image'), ['image', 'video', 'voice', 'news'])) {
            return response('', 404);
        }
        //dd(Material::paginate(10));
        return Admin::content(function (Content $content) use ($request) {

            $content->header('素材管理');
            $content->description('列表');

            $content->body($this->grid($request));
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
            ->header('素材管理')
            ->description('详情')
            ->body($this->detail($id));
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

            $content->header('素材管理');
            $content->description('编辑');

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

            $content->header('素材管理');
            $content->description('新增');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(Request $request)
    {
        echo '<meta name="referrer" content="never">';
        return Admin::grid(Material::class, function (Grid $grid) use ($request) {

            $grid->column('type', '类型')->display(function () use ($request) {
                return Arr::get(MaterialController::getType(), $request->get('type', 'image'));
            });
            $grid->column('name', '文件名称');
            $grid->column('media_id', '素材id');
            switch ($request->get('type', 'image')) {
                case 'image':
                    $grid->column('url', 'url')->light_box(['height' => 50]);
                    break;
                default:
                    // $grid->column('url', 'url');
                    break;
            }

            $grid->column('update_time', '最后更新时间');


            $grid->disableRowSelector();
            $grid->disableExport();


            $grid->actions(function (Actions $actions) use ($request) {
                $actions->disableEdit();
                //$actions->disableDelete();
                switch ($request->get('type', 'image')) {
                    case 'video':

                        break;
                    default:
                        $actions->disableView();
                        break;
                }

                //
            });

            $grid->filter(function (Grid\Filter $filter) {

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                $filter->where(function ($query) {

                }, '类型', 'type')->select(MaterialController::getType());

                // $filter->equal('type', '类型')->select(MaterialController::getType());

            });
        });
    }


    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Material::findOrFail($id));
        $show->title('标题');
        $show->description('描述');
        $show->down_url('资源')->file();

        return $show;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Material::class, function (Form $form) {
            $form->radio('type', '类型')->options(MaterialController::getType());

            $form->largefile('file', '资源');

        });
    }

    public function destroy($mediaId)
    {
        $app = app('wechat.official_account');

        $app->material->delete($mediaId);
        $data = [
            'status' => true,
            'message' => trans('admin.delete_succeeded'),
        ];

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $app = app('wechat.official_account');
        $method = 'upload' . $request->get('type');
        $material = config('aetherupload.UPLOAD_PATH') . DIRECTORY_SEPARATOR . str_replace('_', '/', $request->get('file'));
        $result = $app->material->$method($material);
        @unlink($material);
        admin_toastr(trans('admin.save_succeeded'));
        return redirect('/admin/wechat/material');
    }

    public static function getType()
    {
        return ['image' => '图片', 'video' => '视频', 'voice' => '语音'];
    }
}
