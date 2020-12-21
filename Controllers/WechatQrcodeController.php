<?php

namespace Kwin\WechatAdmin\Controllers;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Kwin\WechatAdmin\Extensions\Tools\QrcodeExcelExpoter;
use Kwin\WechatAdmin\Extensions\Tools\QrcodeExportButtonGender;
use \Kwin\WechatAdmin\Models\WechatQrCode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Kwin\WechatAdmin\Models\WechatQrCodeCate;


class WechatQrcodeController extends Controller
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
            ->header('二维码管理')
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
            ->header('二维码管理')
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
            ->header('二维码管理')
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
            ->header('二维码管理')
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
        $grid = new Grid(new WechatQrCode);

        $grid->id('Id');
        $grid->column('cate.name', '分组名称')->display(function ($val) {
            return "<a href='http://127.0.0.35/admin/wechat/qrcode_cate?id={$this->cate->id}'>{$val}</a>";
        });;
        $grid->qrcode_type('二维码类型')->using(WechatQrCode::$typeList);
        $grid->expire_time('过期时间');
        $grid->img_url('二维码')->light_box(['height' => 50]);;
        $grid->sweep_number('被扫描次数');
        $grid->exported('是否已导出')->switch([
            'on' => ['value' => 1, 'text' => '已导出', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '未导出', 'color' => 'default'],
        ]);;


        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('cate.name', '分组名称');
                $filter->between('expire_time', '过期时间')->datetime();
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->equal('exported', '是否已导出')->select([0 => '未导出', 1 => '已导出']);
                $filter->equal('qrcode_type', '二维码类型')->select(WechatQrCode::$typeList);
                $filter->lt('sweep_number', '扫描次数小于等于')->default('0')->integer();
            });

        });

        $exporter = new QrcodeExcelExpoter();
        $exporter->setCellsArrtibute(uniqid(), ['二维码字符串'], ['url' => function ($val) {
            return '<qrcode>' . $val . '</qrcode>';
        }]);
        $grid->exporter($exporter);

        // $grid->disableRowSelector();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->disableView();
            $actions->prepend('<a class="btn btn-xs btn-primary" href="' . route('admin.wechat.qrcode_scan_log.index', ['wechat_qrcode_id' => $actions->row->id]) . '">扫描记录</a>');
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
            // $tools->append(new QrcodeExportButtonGender());
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
        $show = new Show(WechatQrCode::findOrFail($id));

        $show->id('Id');
        $show->wechat_qrcode_cate_id('Wechat qrcode cate id');
        $show->qrcode_type('Qrcode type');
        $show->scene_id('Scene id');
        $show->ticket('Ticket');
        $show->expire_time('Expire time');
        $show->url('Url');
        $show->img_url('Img url');
        $show->sweep_number('Sweep number');
        $show->exported('Exported');
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
        $form = new Form(new WechatQrCode);

        $form->select('wechat_qrcode_cate_id', '分组')
            ->options(WechatQrCodeCate::pluck('name', 'id'))
            ->rules('required', [
                'required' => '分组不能为空',
            ]);
        $form->select('qrcode_type', '二维码类型')
            ->options(WechatQrCode::$typeList)->default(1)
            ->rules('required', [
                'required' => '二维码类型不能为空',
            ]);

//        $form->text('scene_id', 'Scene id');
//        $form->text('ticket', 'Ticket');
        $form->datetime('expire_time', '过期时间')->default(date('Y-m-d H:i:s', time() + 2592000));

        $form->number('count', '生成数量')->default(1);

        $form->switch('exported', '是否已导出');

        return $form;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $wechatQrcodeCateId = $request->get('wechat_qrcode_cate_id');
        $count = $request->get('count');
        $qrcode_type = $request->get('qrcode_type', 1);
        if ($qrcode_type == 1) {
            $makeQrcodeMethod = 'temporary';
            if (($expire_seconds = strtotime($request->get('expire_time')) - time()) > 2592000) {
                return back()->withErrors(['count' => '有效时间必须小于30天']);
            }
            $expire_time = $request->get('expire_time');
        } else {
            $makeQrcodeMethod = 'forever';
            $expire_seconds = null;
            $expire_time = null;
        }

        if ($count < 1) {
            /*
             //错误提示  header下面
                $error = new MessageBag([
                    'title' => '批量生成数量不能少于1',
                    'message' => '批量生成数量不能少于1',
                ]);
                return back()->with(compact('error'));
            */
            //错误提示 表单上面
            return back()->withErrors(['count' => '批量生成数量不能少于1']);
        }


        $result = [];
        $app = Factory::officialAccount(config('wechat.official_account.default'));

        while ($count--) {
            $scene_id = date('YmdHis') . $count . Str::random(20);
            $result[$count] = $app->qrcode->$makeQrcodeMethod($scene_id, $expire_seconds);
            $result[$count]['qrcode_type'] = $qrcode_type;
            $result[$count]['scene_id'] = $scene_id;
            $result[$count]['img_url'] = $app->qrcode->url($result[$count]['ticket']);
            $result[$count]['wechat_qrcode_cate_id'] = $wechatQrcodeCateId;
            $result[$count]['expire_time'] = $expire_time;
            unset($result[$count]['expire_seconds']);
        }

        WechatQrCode::insert($result);
        admin_toastr(trans('admin.update_succeeded'));
        return redirect(implode('/', explode('/', trim(app('request')->getUri(), '/'))) . '?exported=0');
    }
}
