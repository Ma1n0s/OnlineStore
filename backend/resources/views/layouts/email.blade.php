<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Ответ на обращение' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { color: #2d3748; font-size: 24px; margin-bottom: 20px; }
        .message-block { margin: 15px 0; padding: 15px; border-radius: 5px; }
        .original { background: #f5f5f5; }
        .response { background: #e8f4fc; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>