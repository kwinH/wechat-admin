<?php
/**
 * Project: WechatAdmin.
 * Author: Kwin
 * QQ:284843370
 * Email:kwinwong@hotmail.com
 */

namespace Kwin\WechatAdmin;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use Kwin\WechatAdmin\Models\WechatEvent;
use Kwin\WechatAdmin\Models\WechatQrCode;
use Kwin\WechatAdmin\Models\WechatQrCodeCate;
use Kwin\WechatAdmin\Models\WechatQrCodeScanLog;
use Kwin\WechatAdmin\Models\WechatText;
use Kwin\WechatAdmin\Models\WxMembers;

class MessageReplyHandler implements EventHandlerInterface
{
    /**
     * @param mixed $payload
     */
    public function handle($payload = null)
    {
        $app = app('wechat.official_account');

        switch ($payload['MsgType']) {
            case 'event':
                $res = $this->event($payload, $app);
                break;
            case 'text':
                $res = $this->text($payload, $app);
                break;
        }

        return $res;
    }

    public function event($message, $app)
    {
        switch ($message['Event']) {
            //订阅
            case 'subscribe':
                $event = WechatEvent::where('event', 2)->first();
                $res = $this->scan($message, $app);
                if ($res) {
                    return $res;
                }
                break;
            //取消订阅
            case 'unsubscribe':
                $event = WechatEvent::where('event', 3)->first();
                break;
            case 'SCAN':
                return $this->scan($message, $app);
                break;
            case 'CLICK':
                $event = WechatEvent::where('event', 1)->where('key', $message['EventKey'])->first();
                break;
            case 'LOCATION':
                break;
        }

        Log::info($event);
        if (!empty($event->method)) {
            $user = $this->getUser($message, $app);
            $res = $this->callClassMethod($event->method, [$user, $message]);
            if (!is_null($res)) {
                return $res;
            }
        }

        if (!is_null($event->message)) {
            return $this->returnMsg($event->message);
        }
    }

    public function text($message, $app)
    {
        $msg = null;
        if ($res = WechatText::where('type', 1)->where('key', $message['Content'])->first()) {
            $msg = $res->message;
        } elseif ($res = WechatText::where('type', 2)->whereRaw("LOCATE(`key`,'{$message['Content']}')")->first()) {
            $msg = $res->message;
        } elseif ($res = WechatText::where('type', 3)->whereRaw("'{$message['Content']}' regexp `key`")->first()) {
            $msg = $res->message;
        }

        if (!empty($res->method)) {
            $user = $this->getUser($message, $app);
            $result = $this->callClassMethod($res->method, [$user, $message]);
            if (!is_null($result)) {
                return $result;
            }
        }

        if (!is_null($msg)) {
            return $this->returnMsg($msg);
        }

    }

    protected function returnMsg($msg)
    {
        switch ($msg->msg_type) {
            case 'text':
                return new Text($msg->description);
            case 'image':
                return new Image($msg->media_id);
            case 'voice':
                return new Voice($msg->media_id);
            case 'video':
                return new Video($msg->media_id, [
                    'title' => $msg->title,
                    'description' => $msg->description,
                ]);
            case 'news':
                $items = [];
                foreach ($msg->news_item as $v) {
                    $items[] = new NewsItem([
                        'title' => $v->title,
                        'description' => $v->description,
                        'url' => $v->url,
                        'image' => $v->image,
                    ]);
                }
                return new News($items);
            default:
                Log::info('returnMsg Not Found');
                return null;
        }
    }

    protected function scan($message, $app)
    {
        if (isset($message['Ticket'])) {
            $qrcode = WechatQrCode::where('ticket', $message['Ticket'])->limit(1)->first();
            if ($qrcode) {
                $qrcode->sweep_number += 1;
                $qrcode->save();
                WechatQrCodeCate::where('id', $qrcode->wechat_qrcode_cate_id)->increment('sweep_number');

                $user = $this->getUser($message, $app);
                WechatQrCodeScanLog::create([
                    'user_id' => $user->id,
                    'wechat_qrcode_id' => $qrcode->id,
                    'wechat_qrcode_cate_id' => $qrcode->wechat_qrcode_cate_id
                ]);

                if (!empty($qrcode->cate->method)) {
                    Log::info('执行的方法 ' . $qrcode->cate->method);
                    $res = $this->callClassMethod($qrcode->cate->method, [$user, $qrcode, $message]);
                    if (!is_null($res)) {
                        return $res;
                    }
                }

                if ($qrcode->cate->message) {
                    return $this->returnMsg($qrcode->cate->message);
                }
            }
        }
    }

    protected function getUser($message, $app)
    {
        if (empty($user)) {
            $fromUserName = $app->user->get($message['FromUserName']);
            $user = array_only($fromUserName, ['openid', 'nickname', 'sex', 'province', 'city', 'country', 'headimgurl', 'language', 'subscribe_time', 'subscribe_scene']);

            $user = WxMembers::create($user);
            $user->isNewUser = 1;
        } else {
            if ($user->subscribe_time == 0) {
                $fromUserName = $app->user->get($message['FromUserName']);
                $user->subscribe_time = $fromUserName['subscribe_time'];
                $user->subscribe_scene = $fromUserName['subscribe_scene'];
                $user->save();
                $user->isNewUser = 1;
            } else {
                $user->isNewUser = 0;
            }
        }
        return $user;
    }


    /**
     * @param $method
     * @param array $param
     * @return mixed
     * @throws \Exception
     */
    protected function callClassMethod($method, array $param)
    {
        if (strpos($method, '@') !== false) {
            list($class, $method) = explode('@', $method);
            if (class_exists($class)) {
                $class = new $class;
                if (method_exists($class, $method)) {
                    return call_user_func_array([$class, $method], $param);
                } else {
                    throw new \Exception($class . '@' . $method . '  Method Not Found');
                }
            } else {
                throw new \Exception($class . ' Class Not Found');
            }
        }
    }
}
