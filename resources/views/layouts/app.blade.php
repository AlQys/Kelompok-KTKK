<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'POS KTKK')</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#172033;background:#f4f6fa}
        *{box-sizing:border-box}body{margin:0}a{color:#2458c6;text-decoration:none}a:hover{text-decoration:underline}
        header{background:#172b4d;color:white;padding:16px max(20px,calc((100% - 1120px)/2));display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap}
        header strong{font-size:20px}nav{display:flex;gap:18px;flex-wrap:wrap}nav a{color:#e8edfa}
        main{max-width:1120px;margin:30px auto;padding:0 20px}.page-head{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:20px}
        h1{margin:0 0 8px;font-size:27px}h2{font-size:19px;margin-top:0}.muted{color:#68758b}.card{background:white;border:1px solid #e2e7f0;border-radius:12px;padding:20px;margin-bottom:18px;box-shadow:0 2px 8px #14213d08}
        .cards{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:16px}.cards>.card{min-width:0;margin-bottom:0}.card-stat{grid-column:span 3}.card-financial{grid-column:span 6}.metric{font-size:28px;font-weight:700;line-height:1.25;margin-top:8px;overflow-wrap:anywhere;font-variant-numeric:tabular-nums}.metric-currency{white-space:nowrap}
        table{border-collapse:collapse;width:100%;background:white}th,td{text-align:left;padding:12px 10px;border-bottom:1px solid #e7ebf2;vertical-align:top}th{font-size:13px;color:#59677d;background:#f8f9fc}
        .table-wrap{overflow:auto}form.inline{display:inline}.actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
        label{display:block;font-weight:600;margin-bottom:6px}input,select{font:inherit;width:100%;border:1px solid #cbd3e1;border-radius:7px;padding:10px;background:white;color:#172033}
        .field{margin-bottom:16px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}
        button,.button{display:inline-block;border:0;border-radius:7px;background:#2458c6;color:white;padding:10px 14px;font:inherit;cursor:pointer}
        .secondary{background:#e9eef8;color:#172b4d}.danger{background:#b42318}.success{padding:12px 14px;background:#e8f7ee;color:#116432;border-radius:8px;margin-bottom:16px}
        .errors{padding:12px 14px;background:#fff0ef;color:#9b1c14;border-radius:8px;margin-bottom:16px}.errors ul{margin:6px 0 0;padding-left:20px}
        .pagination{display:flex;gap:12px;align-items:center;margin-top:16px}.empty{padding:24px;text-align:center;color:#68758b}
        @media(max-width:900px){.card-stat{grid-column:span 6}.card-financial{grid-column:span 12}}
        @media(max-width:640px){main{margin-top:20px;padding:0 14px}header{padding:14px}th,td{padding:9px 7px}.card-stat,.card-financial{grid-column:span 12}}
    </style>
    @stack('head')
</head>
<body>
<header>
    <strong>POS KTKK</strong>
    <nav>
        <a href="{{ route('dashboard') }}">Ringkasan</a>
        @if (session('pos_role') === 'admin')
            <a href="{{ route('barang.index') }}">Barang</a>
            <a href="{{ route('pelanggan.index') }}">Pelanggan</a>
        @endif
        <a href="{{ route('transaksi.index') }}">Transaksi</a>
        @if (in_array(session('pos_role'), ['admin', 'kasir'], true))
            <a href="{{ route('transaksi.create') }}">Buat transaksi</a>
        @endif
    </nav>
    <form method="post" action="{{ route('logout') }}">@csrf<button class="secondary" type="submit">Keluar ({{ ucfirst(session('pos_role')) }})</button></form>
</header>
<main>
    @if (session('status'))
        <div class="success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">
            <strong>Periksa kembali data berikut:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
