<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>در دست تعمیر — {{ $siteName }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="lebasino-page maintenance-page">
    <main class="maintenance-card">
        <p class="maintenance-eyebrow">{{ $siteName }}</p>
        <h1>فروشگاه در دست تعمیر است</h1>
        <p class="maintenance-message">{{ $message }}</p>
    </main>
</body>
</html>