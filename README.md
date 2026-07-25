# NHU Platform

南華大學校園二手物品交易平台，提供學生刊登閒置物品、搜尋與收藏商品、議價、聊天、建立訂單及交易後評價等功能。管理員可透過後台審核商品並管理使用者、分類、議價與訂單。

## 主要功能

- 帳號註冊、登入、Email 驗證與密碼重設
- 二手商品刊登、編輯、圖片上傳、分類與搜尋
- 商品收藏、買賣雙方議價與訂單流程
- 站內聊天、未讀訊息與通知
- 交易完成後的使用者評價
- 會員中心：個人資料、刊登商品、議價與訂單管理
- 管理後台：使用者停權、商品審核、分類、議價及訂單管理
- 商品與後台統計 API

## 技術架構

- PHP 8.2+
- Laravel 12
- MySQL
- Blade、JavaScript、Bootstrap 5、Tailwind CSS 4
- Vite 6
- Laravel Reverb / Echo（即時訊息）
- Laravel Queue（通知及背景工作）

## 開發環境需求

請先安裝：

- PHP 8.2 或以上版本及 Laravel 所需 PHP 擴充套件
- Composer
- Node.js 與 npm
- MySQL

## 安裝方式

1. 複製專案並進入目錄：

   ```bash
   git clone https://github.com/Kevin9212/nhu-platform.git
   cd nhu-platform
   ```

2. 安裝後端與前端套件：

   ```bash
   composer install
   npm install
   ```

3. 建立環境設定檔及應用程式金鑰：

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Windows PowerShell 可改用：

   ```powershell
   Copy-Item .env.example .env
   ```

4. 在 MySQL 建立資料庫，並依照本機環境修改 `.env`：

   ```dotenv
   APP_NAME="NHU Platform"
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nhu
   DB_USERNAME=你的資料庫帳號
   DB_PASSWORD=你的資料庫密碼
   ```

5. 建立資料表、預設商品分類及公開儲存空間連結：

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

6. 啟動開發環境：

   ```bash
   composer run dev
   ```

   此指令會同時啟動 Laravel 開發伺服器、Queue Worker、應用程式日誌及 Vite。啟動後開啟 `http://127.0.0.1:8000`。

## Email 與即時聊天設定

預設 `.env.example` 會將郵件及廣播寫入日誌，適合先測試基本流程。若要實際寄送驗證信、密碼重設信或啟用 WebSocket 即時聊天，請在 `.env` 設定郵件服務，並將 `BROADCAST_CONNECTION` 改為 `reverb` 後補上 Reverb 連線資訊。

啟用 Reverb 時另開終端執行：

```bash
php artisan reverb:start
```

## 建立管理員

專案不會自動建立預設管理員。先完成一般帳號註冊，再透過資料庫將該使用者的 `role` 欄位改為 `admin`，即可使用 `/admin` 管理後台。

請勿在正式環境提交 `.env`、資料庫密碼、郵件憑證或 Reverb 金鑰。

## 測試

```bash
composer test
```

## 建置前端資源

```bash
npm run build
```

## 專案目錄

```text
app/                應用程式邏輯、模型與控制器
database/           Migration、Seeder 與 Factory
public/             公開入口與靜態資源
resources/          Blade、CSS 與 JavaScript 原始碼
routes/             Web、API 與廣播路由
storage/            上傳檔案、快取與日誌
tests/              自動化測試
```
