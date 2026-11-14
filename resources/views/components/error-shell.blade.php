@props(['code', 'title', 'description', 'icon' => 'info'])
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} · {{ $title }} | ارتقاء</title>
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="error-page">
    <header class="error-header"><x-brand /></header>
    <main class="error-main">
        <section class="error-card" aria-labelledby="error-title">
            <div class="error-symbol" aria-hidden="true"><x-icon :name="$icon" :size="32" /></div>
            <p class="error-code" aria-label="رمز الحالة {{ $code }}">{{ $code }}</p>
            <h1 id="error-title">{{ $title }}</h1>
            <p class="error-description">{{ $description }}</p>
            <div class="error-actions">{{ $slot }}</div>
        </section>
        <p class="error-footer">ارتقاء · معرفة تُنمّي طموحك</p>
    </main>
</body>
</html>
