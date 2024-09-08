<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\MApiToken;
use App\Repositories\MApiTokenRepository;

/**
 * APIトークンマスタサービス
 *
 * @package App\Services
 * @author naito
 * @version ver1.0.0 2024/08/16
 */
class MApiTokenService
{
    /**
     * コンストラクタ
     *
     * @param MApiTokenRepository $mApiTokenRepository APIトークンマスタリポジトリ
     */
    public function __construct(protected MApiTokenRepository $mApiTokenRepository) {}

    /**
     * APIトークン登録処理
     *
     * @param int $userId ユーザーID
     * @param string $accessToken アクセストークン（暗号化）
     * @param string $refreshToken リフレッシュトークン（暗号化）
     * @param int $expiresAt　アクセストークンの有効期間（秒）
     * @return MApiToken APIトークンマスタ
     *
     */
    public function createApiToken(int $userId, string $accessToken, string $refreshToken, int $expiresAt)
    {
        try {
            $data = [
                'user_id' => $userId,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_at' => Carbon::now()->addSeconds($expiresAt)
            ];
            return $this->mApiTokenRepository->create($data);
        } catch (\Exception $error) {
            throw new \Exception($error . 'APIトークンの登録に失敗しました。');
        }
    }

    /**
     * APIトークン更新処理（条件：ユーザーID）
     *
     * @param int $userId ユーザーID
     * @param string $accessToken アクセストークン（暗号化）
     * @param string $refreshToken リフレッシュトークン（暗号化）
     * @param int $expiresAt　アクセストークンの有効期間（秒）
     * @return int 更新件数
     *
     */
    public function updateApiToken(int $userId, string $accessToken, string $refreshToken, int $expiresAt)
    {
        $data = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => Carbon::now()->addSeconds($expiresAt)
        ];

        return $this->mApiTokenRepository->updateByUserId($userId, $data);
    }
}
