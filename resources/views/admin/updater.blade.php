<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application updater</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 42rem; margin: 3rem auto; padding: 0 1rem; }
        .message { padding: .75rem 1rem; border-radius: .5rem; margin-bottom: 1rem; background: #f1f5f9; }
        .error { background: #fee2e2; color: #991b1b; }
        .success { background: #dcfce7; color: #166534; }
        button { padding: .65rem 1rem; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Application updater</h1>

    @if (session('success')) <div class="message success">{{ session('success') }}</div> @endif
    @if (session('status')) <div class="message">{{ session('status') }}</div> @endif
    @if (session('error')) <div class="message error">{{ session('error') }}</div> @endif

    @if (isset($status['error']))
        <div class="message error">{{ $status['error'] }}</div>
    @elseif ($status['available'] ?? false)
        <div class="message">A newer version is available from the remote repository.</div>
        <form method="post" action="{{ route('admin.updater.pull') }}">
            @csrf
            <button type="submit">Fetch and update application</button>
        </form>
    @else
        <div class="message">The application is up to date.</div>
    @endif
</body>
</html>
