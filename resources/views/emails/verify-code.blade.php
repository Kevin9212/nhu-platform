<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>註冊驗證碼</title>
</head>
<body>
    <p>您好 {{ $user->nickname ?? $user->account }}，</p>

    <p>感謝您註冊 NHU 二手交易平台，以下是您的信箱驗證碼：</p>

    <p style="font-size:20px;font-weight:bold;letter-spacing:2px;">{{ $code }}</p>

    <p>驗證碼有效時間 10 分鐘，請在驗證頁面輸入以完成註冊。</p>

    <p>若您沒有提出註冊請求，請忽略這封信。</p>
</body>
</html>