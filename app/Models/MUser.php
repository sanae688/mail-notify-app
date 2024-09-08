<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MUser extends Model
{
    // テストやデータシーディングのために簡単にダミーデータを生成できる
    // use HasFactory;

    // 削除時は通常は物理削除だが、下記を使用することで論理削除に変更できる
    // use SoftDeletes;

    protected $table = 'm_user';

    protected $fillable = [
        'smaregi_contract_id'
    ];

    public function mApiToken()
    {
        return $this->hasOne(MApiToken::class, 'user_id');
    }
}
