<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * APIトークンマスタモデル
 *
 * @package App\Models
 * @author naito
 * @version ver1.0.0 2024/08/16
 */
class MApiToken extends Model
{
    // テストやデータシーディングのために簡単にダミーデータを生成できる
    // use HasFactory;

    // 削除時は通常は物理削除だが、下記を使用することで論理削除に変更できる
    // use SoftDeletes;

    protected $table = 'm_api_tokens';

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    /**
     * リレーションシップの定義
     */
    public function mUser()
    {
        return $this->belongsTo(MUser::class);
    }
}
