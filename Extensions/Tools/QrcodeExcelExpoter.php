<?php

namespace Kwin\WechatAdmin\Extensions\Tools;

use Illuminate\Support\Facades\Request;
use \Kwin\WechatAdmin\Models\WechatQrCode;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Encore\Admin\Grid;

class QrcodeExcelExpoter extends AbstractExporter
{
    public $file_name = 'filename';
    public $cell_arr = [];
    public $cell_items = [];

    public function setCellsArrtibute($file_name, array $cells_arr, array $cell_items)
    {
        $this->file_name = $file_name;
        $this->cell_arr = $cells_arr;
        $this->cell_items = $cell_items;
    }

    public function export()
    {
        $datas = $this->getData();
        Excel::create($this->file_name, function ($excel) use ($datas) {
            $this->exportAfter();

            $excel->sheet($this->file_name, function ($sheet) use ($datas) {
                // 这段逻辑是从表格数据中取出需要导出的字段
                $newData = [];
                $rows = collect($datas)->map(function ($item) {
                    $data = [];
                    foreach ($this->cell_items as $k => $v) {
                        if (is_numeric($k)) {
                            $data[$v] = isset($item[$v]) ? $item[$v] : '';
                        } elseif ($v instanceof \Closure) {
                            $data[$k] = $v($item[$k]);
                        } else {
                            $data[$k] = $v;
                        }
                    }
                    return $data;
                    return array_only($item, $this->cell_items);
                });
                $title = collect([$this->cell_arr]);
                $rows = $title->merge($rows);
                $sheet->rows($rows);

            });

        })->export('xls');


    }


    protected function exportAfter()
    {
        if ($export = Request::get('_export_')) {
            if ($export == Grid\Exporter::SCOPE_ALL) {
                WechatQrcode::where('exported', '0')->update(['exported' => 1]);

            } else {
                list($scope, $args) = explode(':', $export);
                if ($scope == Grid\Exporter::SCOPE_CURRENT_PAGE) {

                    WechatQrcode::whereIn('id', function ($query) use ($args) {
                        $offset = ($args - 1) * 20;
                        \DB::update("update `wechat_qrcode` set `exported` = 1 where `id` in (select * from (select `id` from `qrcode` limit {$offset},20) as t2)");
                    });

                } else if ($scope == Grid\Exporter::SCOPE_SELECTED_ROWS) {
                    $selected = explode(',', $args);
                    $grid = $this->grid();
                    WechatQrcode::whereIn($grid->getKeyName(), $selected)->update(['exported' => 1]);
                }
            }
        }
    }
}