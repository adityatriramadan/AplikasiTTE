<?php $error = $data['error'] ?? null; $expired = !empty($data['expired']); ?>
<div class="brand">
    <div>
        <h1>Masuk ke Sistem</h1>
        <div class="muted">E-Office Tanda Tangan Digital Prodi TI UNPAM</div>
    </div>
    <span class="badge">Login Aman</span>
</div>

<div class="card" style="max-width: 460px; margin: 0 auto;">
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($expired): ?>
        <div class="alert error">Sesi Anda telah berakhir. Silakan login kembali.</div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/auth/proses">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Security::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="field">
            <label for="nip">NIP</label>
            <input type="text" id="nip" name="nip" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button class="btn" type="submit">Masuk</button>
    </form>
</div>
</div>
</body>
</html>