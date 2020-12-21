<?php
/**
 * Project: Project-HNB-20181108.
 * Author: Kwin
 * QQ:284843370
 * Email:kwinwong@hotmail.com
 */

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class Material extends Model
{
    protected $keyType = 'string';
    protected $primaryKey = 'media_id';
    protected $dates = [
        'update_time',
    ];

    public static function paginate($limit)
    {
        $app = app('wechat.official_account');


        $page = Request::get('page', 0);

        // $start = ($page - 1) * $perPage;

        //   dd($app->material->stats());
        $data = ($app->material->list(Request::get('type', 'image'), $page, $limit));


        $data['item'] = static::hydrate($data['item']);

        $paginator = new LengthAwarePaginator($data['item'], $data['total_count'], $limit);

        $paginator->setPath(url()->current());

        return $paginator;
    }


    public static function with($relations)
    {
        return new static;
    }

    public function where()
    {
        return $this;
    }

    public function get()
    {
        die('avav');
    }

    public function orderBy()
    {
        return $this;
    }


    public static function findOrFail($mediaId)
    {
        $app = app('wechat.official_account');
        $data = $app->material->get($mediaId);

        return (new static)->newFromBuilder($data);
    }
}
