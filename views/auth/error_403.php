<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    <style>
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: linear-gradient(180deg, #f4f7fb, #edf2f9); color: #10233f; }
        .wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border: 1px solid #dde6f3; border-radius: 20px; padding: 28px; width: min(560px, 100%); box-shadow: 0 20px 50px rgba(16,35,63,.1); }
        a { display: inline-block; margin-top: 16px; padding: 10px 14px; background: #1677ff; color: #fff; text-decoration: none; border-radius: 10px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>403 - Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk membuka halaman ini.</p>
        <a href="<?= BASE_URL ?>/login">Kembali ke login</a>
    </div>
</div>
</body>
</html>