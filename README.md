# メール通知アプリ

## 概要

### 1.システム概要
本アプリでは、スマレジ（クラウドPOSレジ）の取引情報を取得し、<br>
特定の商品が販売された場合に、利用者にメールで通知をするアプリである<br>

<img width="50%" alt="システム全体図" src="system.png">

### 2.環境構築

#### 環境
**バックエンド**<br>
　Laravel 11（Laravel Sail, Vite）<br>
　MySQL 8<br>

**フロントエンド**<br>
　React 18<br>
　Sass<br>
　TypeScript<br>

#### 手順
下記の記事を参考に環境構築を実施<br>
　[Laravel Sail を活用した Laravel 10 と React 18 の SPA 開発環境の構築手順](https://ryamate.hatenablog.com/entry/laravel_sail_react)<br>
　[Laravel × React + TypeScript で SPA の開発環境を構築（Laravel Sail を利用）](https://qiita.com/shikuno_dev/items/7e679b2fdf0bb92cb2b0)<br>
　[Laravelまとめその1 Laravel Sailで11および10環境を構築しよう編](https://qiita.com/motuneko253/items/4ca503b2a2beba5fa232)<br>
　　※現在（2024/08時点）でインストールコマンドを実行すると`Laravel11`がインストールされる<br>

#### 補足
**1.[スマレジDevelopers](https://developers.smaregi.jp/signup/)へ登録**<br>
　→登録完了後、アプリを登録する<br>
　→アプリ登録完了後、環境設定からクライアントIDとクライアントシークレットを確認し、<br>
　　`.env`の`SMAREGI_CLIENT_ID`と`SMAREGI_CLIENT_SECRET`を自身のものに書き換える<br>

**2.[ngrok](https://ngrok.com/)へ登録**<br>
　→スマレジ・プラットフォームAPIからWebhookを受け取る必要があり、ローカル環境にて動作確認をするために必要<br>
　→登録完了後、[こちらの記事](https://dev.to/naxrohan/laravel-sail-ngrok-2kk4)を参考に、<br>
　　`.env`の`APP_URL`にngrokのURL（パブリックURL）及び`.env`の`NGROK_AUTHTOKEN`に自身の認証トークンを記載をする<br>
　　（`docker-compose.yml`への記載は対応済み）<br>
　※ngrokのURL（パブリックURL）は起動ごとに毎回変わるため、都度変更が必要<br>
　　また、スマレジDevelopersのアプリのURLなども同様に変更が必要<br>
　　（他に良いやり方がないか検討中）<br>

**3.[mailtrap](https://mailtrap.io/)へ登録**<br>
　→ローカル環境にてメール送信の動作確認をするために、開発者向けのテスト用メールサーバーサービスを使用<br>
　→登録完了後、[こちらの記事](https://reffect.co.jp/laravel/mailtrap-dummy-smtp-server#google_vignette)を参考に、`.env`の該当箇所を自身のものに書き換える<br>

**4.マイグレーションの実行**<br>
- **sailコマンドを実行する前は下記コマンドを実行する**<br>
    ```bash
    alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
    ```
- **Laravel Sailを起動する**<br>
    ```bash
    sail up -d
    ```
- **マイグレーションファイルを実行し、データベースに反映させる**<br>
    ```bash
    # マイグレーション時やロールバック時はLaravelが自動的に依存関係を考慮して正しい順序で実行してくれる

    # マイグレーション実行
    # マイグレーションファイルのupメソッドを実行し、データベースに新しいテーブルやカラムが作成される
    sail artisan migrate

    # ロールバック実行（上記で行ったマイグレーションを戻したい場合のみ実施する）
    # マイグレーションファイルのdownメソッドを実行し、元の状態に戻す
    sail artisan migrate:rollback
    ```

**5.アプリの実行**<br>
- **sailコマンドを実行する前は下記コマンドを実行する**<br>
    ```bash
    alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
    ```
- **Laravel Sailを起動する**<br>
    ```bash
    sail up -d
    ```
- **Viteの開発サーバーを起動する**<br>
    ```bash
    sail npm run dev
    ```
    上記のコマンドを実行後、`APP_URL`にアクセスをするとアプリが起動する

### 3.テーブル設計

#### テーブル定義
**1. `m_users` テーブル（`ユーザーマスタ`テーブル）**

**役割**: 通知メールを受け取るユーザー情報を格納します。

```sql
CREATE TABLE m_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'ユーザーID',
    smaregi_contract_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'スマレジ契約ID',
    smaregi_user_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'スマレジユーザーID',
    smaregi_user_name VARCHAR(255) COMMENT 'スマレジユーザー名',
    smaregi_user_email VARCHAR(255) COMMENT 'スマレジユーザーメールアドレス',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
    deleted_at TIMESTAMP COMMENT '削除日時',
    delete_flg INTEGER NOT NULL DEFAULT 0 COMMENT '論理削除フラグ', -- 0: active, 1: deleted
    CHECK (delete_flg IN (0, 1))
) COMMENT = 'ユーザーマスタ';
```

| 論理名                 | 物理名                | データ型       | PK/FK | 必須  | デフォルト                        |
|------------------------|-----------------------|----------------|-------|-------|-----------------------------------|
| ユーザーID             | id                    | BIGINT        | PK    | YES   | AUTO_INCREMENT                   |
| スマレジ契約ID         | smaregi_contract_id   | VARCHAR(255)   |       | YES   | NULL                              |
| スマレジユーザーID     | smaregi_user_id       | VARCHAR(255)   |       | YES   | NULL                              |
| スマレジユーザー名     | smaregi_user_name     | VARCHAR(255)   |       | NO    | NULL                              |
| スマレジユーザーメールアドレス | smaregi_user_email    | VARCHAR(255)   |       | NO    | NULL                              |
| 作成日時               | created_at            | TIMESTAMP      |       | NO    | CURRENT_TIMESTAMP                |
| 更新日時               | updated_at            | TIMESTAMP      |       | NO    | CURRENT_TIMESTAMP ON UPDATE      |
| 削除日時               | deleted_at            | TIMESTAMP      |       | NO    | NULL                              |
| 論理削除フラグ         | delete_flg            | INTEGER        |       | YES   | 0                                 |

**2. `m_api_tokens` テーブル（`APIトークンマスタ`テーブル）**

**役割**: API通信で使用するアクセストークンとリフレッシュトークンを格納します。

```sql
CREATE TABLE m_api_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'APIトークンID',
    user_id BIGINT UNSIGNED UNIQUE COMMENT 'ユーザーID', -- m_usersテーブルへの外部キー（1ユーザーに対して1つのトークンが関連付く仕様）
    access_token VARCHAR(255) UNIQUE NOT NULL COMMENT 'アクセストークン',
    refresh_token VARCHAR(255) UNIQUE NOT NULL COMMENT 'リフレッシュトークン',
    expires_at TIMESTAMP NOT NULL COMMENT 'トークン有効期限',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
    deleted_at TIMESTAMP COMMENT '削除日時',
    delete_flg INTEGER NOT NULL DEFAULT 0 COMMENT '論理削除フラグ', -- 0: active, 1: deleted
    CHECK (delete_flg IN (0, 1)),
    FOREIGN KEY (user_id) REFERENCES m_users(id) ON DELETE CASCADE
) COMMENT = 'APIトークンマスタ';
```

| 論理名                 | 物理名                | データ型       | PK/FK | 必須  | デフォルト                        |
|------------------------|-----------------------|----------------|-------|-------|-----------------------------------|
| APIトークンID          | id                    | BIGINT        | PK    | YES   | AUTO_INCREMENT                   |
| ユーザーID             | user_id               | BIGINT        | FK    | YES   | NULL                              |
| アクセストークン       | access_token          | VARCHAR(255)   |       | YES   | NULL                              |
| リフレッシュトークン   | refresh_token         | VARCHAR(255)   |       | YES   | NULL                              |
| トークン有効期限       | expires_at            | TIMESTAMP      |       | YES   | NULL                              |
| 作成日時               | created_at            | TIMESTAMP      |       | NO    | CURRENT_TIMESTAMP                |
| 更新日時               | updated_at            | TIMESTAMP      |       | NO    | CURRENT_TIMESTAMP ON UPDATE      |
| 削除日時               | deleted_at            | TIMESTAMP      |       | NO    | NULL                              |
| 論理削除フラグ         | delete_flg            | INTEGER        |       | YES   | 0                                 |

#### ER図

```mermaid
erDiagram
    M_USERS {
        BIGINT id PK "ユーザーID"
        VARCHAR smaregi_contract_id "スマレジ契約ID"
        VARCHAR smaregi_user_id "スマレジユーザーID"
        VARCHAR smaregi_user_name "スマレジユーザー名"
        VARCHAR smaregi_user_email "スマレジユーザーメールアドレス"
        TIMESTAMP created_at "作成日時"
        TIMESTAMP updated_at "更新日時"
        TIMESTAMP deleted_at "削除日時"
        INTEGER delete_flg "論理削除フラグ"
    }

    M_API_TOKENS {
        BIGINT id PK "APIトークンID"
        BIGINT user_id FK "ユーザーID"
        VARCHAR access_token "アクセストークン"
        VARCHAR refresh_token "リフレッシュトークン"
        TIMESTAMP expires_at "トークン有効期限"
        TIMESTAMP created_at "作成日時"
        TIMESTAMP updated_at "更新日時"
        TIMESTAMP deleted_at "削除日時"
        INTEGER delete_flg "論理削除フラグ"
    }

    M_USERS ||--o| M_API_TOKENS : "user_id"
```
