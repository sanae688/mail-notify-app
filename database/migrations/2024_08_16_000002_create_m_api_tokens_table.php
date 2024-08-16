<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('m_api_tokens', function (Blueprint $table) {
            $table->id()->comment('APIトークンID');
            $table->foreignId('user_id')->unique()->constrained('m_users')->onDelete('cascade')->comment('ユーザーID'); // m_usersテーブルへの外部キー
            $table->string('access_token')->unique()->comment('アクセストークン');
            $table->string('refresh_token')->unique()->comment('リフレッシュトークン');
            $table->timestamp('expires_at')->comment('トークン有効期限');
            $table->timestamp('created_at')->useCurrent()->comment('作成日時');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新日時');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');
            $table->integer('delete_flg')->default(0)->comment('論理削除フラグ'); // 0: active, 1: deleted
        });

        DB::statement("ALTER TABLE m_api_tokens COMMENT = 'APIトークンマスタ'");
        DB::statement('ALTER TABLE m_api_tokens ADD CONSTRAINT check_delete_flg_tokens CHECK (delete_flg IN (0, 1))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('m_api_tokens');
    }
}
