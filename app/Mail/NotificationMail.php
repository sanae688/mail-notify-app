<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * 通知メール送信用クラス
 *
 * @package App\Mail
 * @author naito
 * @version ver1.0.0 2024/08/13
 */
class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * コンストラクタ
     *
     * @param array $mailText メール内容
     */
    public function __construct(protected array $mailText) {}

    /**
     * 通知メール構築
     */
    public function build()
    {
        return $this->subject('Notification')
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->to('abc987@example.com', 'Test')
            ->view('emails.notification')
            ->with('mailText', $this->mailText);
    }
}
