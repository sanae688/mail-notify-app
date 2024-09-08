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
            $table->foreignId('user_id')->unique()->constrained('m_user')->onDelete('cascade')->comment('ユーザーID'); // m_userテーブルへの外部キー
            $table->text('access_token')->comment('アクセストークン');
            $table->text('refresh_token')->comment('リフレッシュトークン');
            $table->timestamp('expires_at')->comment('トークン有効期限');
            $table->timestamp('created_at')->useCurrent()->comment('作成日時');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新日時');
        });

        DB::statement("ALTER TABLE m_api_tokens COMMENT = 'APIトークンマスタ'");
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
