<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — POS KTKK</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,sans-serif;color:#172033;background:#f4f6fa}*{box-sizing:border-box}
        body{min-height:100vh;display:grid;place-items:center;margin:0;padding:20px}.card{width:min(100%,420px);background:white;padding:28px;border:1px solid #e2e7f0;border-radius:12px;box-shadow:0 8px 28px #14213d12}
        h1{margin:0 0 8px}p{color:#68758b;margin-bottom:22px}label{display:block;font-weight:600;margin:14px 0 6px}
        input{font:inherit;width:100%;border:1px solid #cbd3e1;border-radius:7px;padding:11px}button{width:100%;margin-top:20px;border:0;border-radius:7px;background:#2458c6;color:white;padding:12px;font:inherit;cursor:pointer}
        .errors{background:#fff0ef;color:#9b1c14;padding:12px;border-radius:7px}.hint{margin:18px 0 0;font-size:13px}
    </style>
</head>
<body>
<main class="card">
    <h1>POS KTKK</h1>
    <p>Masuk dengan akun admin, kasir, atau owner.</p>
    @if ($errors->any())
        <div class="errors"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="post" action="{{ route('login.store') }}">
        @csrf
        <label for="username">Username</label>
        <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" required autofocus>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit">Masuk</button>
    </form>
    <p class="hint">Akun awal dibuat dengan menjalankan <code>php artisan db:seed</code>.</p>
</main>
</body>
</html>
