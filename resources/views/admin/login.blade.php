<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Buku Tamu Diskominfo</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-header">
            <div class="login-icon" style="backgorund: transparent;">
                <img src="{{ asset('img/logokominfo.png') }}" alt="Logo Kominfo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <h1>Login Admin</h1>
            <p>Buku Tamu Digital &mdash; Diskominfo Lumajang</p>
        </div>

        {{-- Tampilkan error jika login gagal --}}
        @if ($errors->any())
            <div class="error-box">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       class="input-field" placeholder="Masukkan Email Admin!!"
                       value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       class="input-field" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">
                Masuk ke Dashboard
            </button>
        </form>

        <a href="{{ route('guestbook.create') }}" class="back-link">
            &larr; Kembali ke Halaman Buku Tamu
        </a>
    </div>

</body>
</html>