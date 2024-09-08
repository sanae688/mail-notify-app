<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OAuthAuthenticationException;
use App\Services\MUserService;
use App\Services\MApiTokenService;

/**
 * ログインコントローラー
 *
 * @package App\Http\Controllers
 * @author naito
 * @version ver1.0.0 2024/08/13
 */
class LoginController extends Controller
{
    /**
     * コンストラクタ
     *
     * @param MUserService $mUserService ユーザーマスタサービス
     * @param MApiTokenService $mApiTokenService APIトークンマスタサービス
     */
    public function __construct(protected MUserService $mUserService, protected MApiTokenService $mApiTokenService) {}

    /**
     * ユーザー認可要求
     *
     * @return JsonResponse jsonレスポンス
     */
    public function requestUserAuthorization(): JsonResponse
    {
        try {
            // ユーザー認可の要求URL生成
            $authorizationCode = 'https://id.smaregi.dev/authorize?response_type=code&client_id=' . env('SMAREGI_CLIENT_ID') .
                '&scope=openid+email+profile+offline_access&state=' . rand() .
                '&redirect_uri=' . env('APP_URL') . env('SMAREGI_API_URL') . env('SMAREGI_REDIRECT_URL');

            return response()->json([
                'success' => true,
                'redirect_url' => $authorizationCode,
            ]);
        } catch (\Exception $error) {
            // ここでエラーは発生しないと思われるが、後続の処理に影響ないように念のためtry-catchを追加
            return response()->json([
                'success' => false,
                'message' => $error,
            ]);
        }
    }

    /**
     * ログイン認証情報の取得
     *
     * @param Request $request リクエスト
     * @return JsonResponse jsonレスポンス
     * @throws OAuthAuthenticationException ログイン認証が失敗した場合
     */
    public function handleOAuthCallback(Request $request): JsonResponse
    {
        try {
            // 認可コード取得
            throw_unless(is_null($request->input('error')), OAuthAuthenticationException::class, '認可コード取得エラー：' . $request->input('error_description'));
            $authorizationCode = $request->input('code');

            // ユーザーアクセストークン取得
            $accessTokenInfo = $this->requestAccessToken($authorizationCode);
            throw_if(is_null($accessTokenInfo), OAuthAuthenticationException::class, 'ユーザーアクセストークン取得エラー');

            // ユーザー情報取得
            $userInfo = $this->requestUserInfo($accessTokenInfo['access_token']);
            throw_if(is_null($userInfo), OAuthAuthenticationException::class, 'ユーザー情報取得エラー');

            // ユーザーマスタ情報取得
            $mUserInfo = $this->mUserService->findByContractId($userInfo['contract']['id']);

            if (is_null($mUserInfo)) {
                // 契約IDがユーザー情報に存在しない場合、登録処理
                // ユーザーマスタ登録
                $createUser = $this->mUserService->createUser($userInfo['contract']['id']);
                throw_if(is_null($createUser), OAuthAuthenticationException::class, 'ユーザーマスタ登録エラー');
                Log::info('ユーザーID： ' . $createUser->id);

                // APIトークンマスタ登録
                $createApiToken = $this->mApiTokenService->createApiToken($createUser->id, Crypt::encryptString($accessTokenInfo['access_token']), Crypt::encryptString($accessTokenInfo['refresh_token']), $accessTokenInfo['expires_in']);
                throw_if(is_null($createApiToken), OAuthAuthenticationException::class, 'APIトークンマスタ登録エラー');
                Log::info('APIトークンID： ' . $createApiToken->id);
            } else {
                // 契約IDがユーザー情報に存在する場合、更新処理
                // APIトークンマスタ情報取得
                $mApiTokenInfo = $this->mUserService->findMApiTokenByMUser($mUserInfo);
                throw_if(is_null($mApiTokenInfo), OAuthAuthenticationException::class, 'APIトークンマスタ取得エラー');
                // APIトークンマスタ更新
                $updateApiTokenCount = $this->mApiTokenService->updateApiToken($mApiTokenInfo->id, Crypt::encryptString($accessTokenInfo['access_token']), Crypt::encryptString($accessTokenInfo['refresh_token']), $accessTokenInfo['expires_in']);
                Log::info('APIトークンマスタ更新件数： ' . $updateApiTokenCount);
                throw_if($updateApiTokenCount = 0, OAuthAuthenticationException::class, 'APIトークンマスタ更新エラー');
            }

            return response()->json([
                'success' => true,
                'message' => $userInfo
            ]);
        } catch (OAuthAuthenticationException $error) {
            Log::error('エラーが発生しました: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ]);
        } catch (\Exception $error) {
            Log::error('エラーが発生しました: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ]);
        }
    }

    /**
     * ユーザーアクセストークン取得
     *
     * @param String $authorizationCode 認可コード
     * @return mixed array | null
     */
    private function requestAccessToken(String $authorizationCode): mixed
    {
        $responseAccessToken = Http::withHeaders([
            'Authorization' => 'Basic {' . base64_encode(env('SMAREGI_CLIENT_ID') . ':' . env('SMAREGI_CLIENT_SECRET')) . '}',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://id.smaregi.dev/authorize/token', [
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
            'redirect_uri' => env('APP_URL') . env('SMAREGI_API_URL') . env('SMAREGI_REDIRECT_URL'),
        ]);

        return json_decode($responseAccessToken->getBody()->getContents(), true);
    }

    /**
     * ユーザー情報取得
     *
     * @param String $accessToken アクセストークン
     * @return mixed array | null
     */
    private function requestUserInfo(String $accessToken): mixed
    {
        $responseUser = Http::withHeaders([
            'Authorization' => $accessToken,
        ])->post('https://id.smaregi.dev/userinfo');

        return json_decode($responseUser->getBody()->getContents(), true);
    }
}
