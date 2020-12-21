<?php

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    /**
     * [$guarded description]
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * [$guarded description]
     *
     * @var string
     */
    protected $table = "visit_log";

    public function wxMembers()
    {
        return $this->belongsTo(WxMembers::class);
    }
}
