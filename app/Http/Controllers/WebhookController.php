<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Services\MUserService;

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
     * @param MUserService $mUserService ユーザーマスタサービス
     */
    public function __construct(protected NotificationService $notificationService, protected MUserService $mUserService) {}

    /**
     * Webhook受信処理
     *
     * @param Request $request リクエスト
     */
    public function __invoke(Request $request)
    {
        // ユーザーマスタ情報取得
        Log::info('契約ID：' . $request->input('contractId'));
        $mUserInfo = $this->mUserService->findByContractId($request->input('contractId'));

        if (is_null($mUserInfo)) {
            Log::info('登録されていない契約IDのためメール通知処理をスキップします。');
        } else {
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
}
