<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

/**
 * メール通知サービス
 *
 * @package App\Services
 * @author naito
 * @version ver1.0.0 2024/08/13
 */
class NotificationService
{
    /**
     * 通知メール送信処理
     *
     * @param array $mailText メール内容
     */
    public function send(array $mailText)
    {
        Mail::send(new NotificationMail($mailText));
    }
}
