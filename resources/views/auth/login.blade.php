<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · TokoKu POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tokoku.css') }}">
</head>
<body>
<div class="login">
    <div class="card">
        <div class="lh">
            <div class="logo">T</div>
            <h2>Sistem POS TokoKu</h2>
            <p>Masuk ke sistem kasir &amp; manajemen penjualan</p>
        </div>
        <div class="lb">
            @if($errors->any())<div class="flash err">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="field"><label for="username">Username Akun</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="admin / budi / siti" required autofocus autocomplete="username"></div>
                <div class="field"><label for="password">Kata Sandi (Password)</label>
                    <input id="password" type="password" name="password" placeholder="••••••" required autocomplete="current-password"></div>
                <button class="btn btn-pri btn-blk" type="submit">Masuk ke Sistem</button>
            </form>
        </div>
        @if(config('app.debug'))
        <div class="demo">
            <p>Akun Pengujian Demo (1-Klik Masuk):</p>
            <div class="g">
                <button type="button" data-u="admin"><b>Admin</b><small>user: admin | pass: 123</small></button>
                <button type="button" data-u="budi"><b>Budi</b><small>user: budi | pass: 123</small></button>
                <button type="button" data-u="siti"><b>Siti</b><small>user: siti | pass: 123</small></button>
                <button type="button" data-u="ahmad"><b>Ahmad (Nonaktif)</b><small>Tes tolak login nonaktif</small></button>
            </div>
        </div>
        @endif
    </div>
</div>
<script>
document.querySelectorAll('[data-u]').forEach(function (b) {
  b.addEventListener('click', function () {
    document.getElementById('username').value = b.dataset.u;
    document.getElementById('password').value = '123';
    document.getElementById('loginForm').submit();
  });
});
</script>
</body>
</html>
