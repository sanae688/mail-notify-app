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
　　※現在（2024/08時点）でインストールコマンドを実行するとLaravel11がインストールされる<br>

#### 補足
**1.[スマレジDevelopers](https://developers.smaregi.jp/signup/)へ登録**<br>
　→登録完了後、アプリを登録する<br>
　→アプリ登録完了後、環境設定からクライアントIDとクライアントシークレットを確認し、<br>
　　.envのSMAREGI_CLIENT_IDとSMAREGI_CLIENT_SECRETを自身のものに書き換える<br>

**2.[ngrok](https://ngrok.com/)へ登録**<br>
　→スマレジ・プラットフォームAPIからWebhookを受け取る必要があり、ローカル環境にて動作確認をするために必要<br>
　→登録完了後、[こちらの記事](https://dev.to/naxrohan/laravel-sail-ngrok-2kk4)を参考に、<br>
　　.envのAPP_URLにngrokのURL（パブリックURL）及び.envのNGROK_AUTHTOKENに自身の認証トークンを記載をする<br>
　　（docker-compose.ymlへの記載は対応済み）<br>
　※ngrokのURL（パブリックURL）は起動ごとに毎回変わるため、都度変更が必要<br>
　　また、スマレジDevelopersのアプリのURLなども同様に変更が必要<br>
　　（他に良いやり方がないか検討中）<br>

**3.[mailtrap](https://mailtrap.io/)へ登録**<br>
　→ローカル環境にてメール送信の動作確認をするために、開発者向けのテスト用メールサーバーサービスを使用<br>
　→登録完了後、[こちらの記事](https://reffect.co.jp/laravel/mailtrap-dummy-smtp-server#google_vignette)を参考に、.envを自身のものに書き換える<br>
