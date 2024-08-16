<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('m_users', function (Blueprint $table) {
            $table->id()->comment('ユーザーID');
            $table->string('smaregi_contract_id')->unique()->comment('スマレジ契約ID');
            $table->string('smaregi_user_id')->unique()->comment('スマレジユーザーID');
            $table->string('smaregi_user_name')->nullable()->comment('スマレジユーザー名');
            $table->string('smaregi_user_email')->nullable()->comment('スマレジユーザーメールアドレス');
            $table->timestamp('created_at')->useCurrent()->comment('作成日時');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新日時');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');
            $table->integer('delete_flg')->default(0)->comment('論理削除フラグ'); // 0: active, 1: deleted
        });

        DB::statement("ALTER TABLE m_users COMMENT = 'ユーザーマスタ'");
        DB::statement('ALTER TABLE m_users ADD CONSTRAINT check_delete_flg_users CHECK (delete_flg IN (0, 1))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('m_users');
    }
}
