<?php
/**
 * Created by PhpStorm.
 * User: Huangzhongyu
 * Date: 2017/10/11
 * Time: 9:55
 */

use Encore\Admin\Facades\Admin;


if (!function_exists('jsConfirm')) {
    function jsConfirm()
    {
        $script = <<<SCRIPT

confirmSwal= function(title,url,_method) {
    title = title||'';
    url= url || '';
    _method= _method || 'put';
    swal(
    {
      title: title,
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "确定",
      closeOnConfirm: false,
      cancelButtonText: "取消"
    }).then(function(res){
       console.log(res)
       if(res.value){
        $.ajax({
            method: 'post',
            url: url,
            data: {
                _method:_method,
                _token:LA.token,
            },
            success: function (data) {
                $.pjax.reload('#pjax-container');

                if (typeof data === 'object') {
                    if (data.status) {
                        swal(data.message, '', 'success');
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            }
        });
 }       
 });
};

SCRIPT;

        Admin::script($script);


    }
}