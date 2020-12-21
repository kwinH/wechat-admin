<?php

namespace Kwin\WechatAdmin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporter;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class QrcodeExportButtonGender extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['gender' => '_gender_']);

        return <<<EOT

$('input:radio.user-gender').change(function () {

    var url = "$url".replace('_gender_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function exportUrl($scope = 1, $args = null)
    {
        $query = Exporter::formatExportQuery($scope, $args);

        $input = array_merge(Request::all(), ['_imgs_export_' => $query[Exporter::$queryName]]);

        return $this->grid->resource() . '?' . http_build_query($input);
    }

    public function render()
    {
        //Admin::script($this->script());

        $export = trans('admin.export');
        $all = trans('admin.all');
        $currentPage = trans('admin.current_page');
        $selectedRows = trans('admin.selected_rows');
        $page = request('page', 1);
        return <<<EOT
<div class="btn-group">
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        二维码导出 <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="{$this->exportUrl('all')}" target="_blank">{$all}</a></li>
        <li><a href="{$this->exportUrl('page', $page)}" target="_blank">{$currentPage}</a></li>
        <li><a href="{$this->exportUrl('selected', '__rows__')}" target="_blank" class='export-selected'>{$selectedRows}</a></li>

    </ul>
</div>
EOT;


    }
}