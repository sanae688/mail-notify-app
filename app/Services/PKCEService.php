<?php

namespace App\Services;

/**
 * PKCEサービス
 *
 * @package App\Services
 * @author naito
 * @version ver1.0.0 2024/09/16
 */
class PKCEService
{
    public function generateCodeVerifier(): string
    {
        // 43から128のランダム値を取得
        $randomValue = $this->randomValueBetween(43, 128);
        // カスタム文字セット
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
        $codeVerifier = substr(str_shuffle(str_repeat($characters, ceil($randomValue / strlen($characters)))), 0, $randomValue);

        return $codeVerifier;
    }

    public function generateCodeChallenge(string $codeVerifier): string
    {
        // SHA256でハッシュ化
        $hash = hash('sha256', $codeVerifier, true);
        // Base64 URLエンコード
        return $this->base64UrlEncode($hash);
    }

    private function randomValueBetween(int $min, int $max): int
    {
        // 指定された範囲から配列を作成
        $rangeArray = range($min, $max);

        // 配列からランダムに1つの値を取得
        return $rangeArray[array_rand($rangeArray)];
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
