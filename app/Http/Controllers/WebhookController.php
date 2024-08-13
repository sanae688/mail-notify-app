<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

/**
 * Webhookコントローラー
 *
 * @package App\Http\Controllers
 * @author naito
 * @version ver1.0.0 2024/08/13
 */
class WebhookController extends Controller
{
    /**
     * コンストラクタ
     *
     * @param NotificationService $notificationService メール通知サービス
     */
    public function __construct(protected NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Webhook受信処理
     *
     * @param Request $request リクエスト
     */
    public function __invoke(Request $request)
    {
        $mailText = [
            'message' => 'WebHookを受信しました。',
            'contractId' => $request->input('contractId'),
            'event' => $request->input('event'),
            'action' => $request->input('action'),
            'transactionHeadIds' => $request->input('transactionHeadIds'),
        ];

        // 通知メール送信処理
        $this->notificationService->send($mailText);
    }
}
