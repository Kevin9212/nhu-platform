<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>密碼重設通知</title>
</head>
<body>
    <p>您好 {{ $user->name ?? $user->account }}，</p>

    <p>您收到這封郵件是因為我們收到了您帳號的密碼重設請求。請點擊以下連結完成重設：</p>

    <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>

    <p>如果這不是您本人提出的請求，請忽略這封郵件，您的密碼將保持不變。</p>

    <p>祝使用愉快，<br>NHU 二手交易平台團隊</p>
</body>
</html>