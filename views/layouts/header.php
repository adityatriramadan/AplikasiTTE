<?php $pageTitle = $data['title'] ?? APP_NAME; $currentUser = $data['current_user'] ?? Auth::user(); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
    <style>
        :root { --bg: #f3f5f9; --panel: #ffffff; --text: #10233f; --muted: #667085; --line: #d9e2f2; --brand: #123a63; --brand-2: #1d6fd8; --ok: #1f7a4d; --warn: #b45309; --danger: #b42318; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: linear-gradient(180deg, #eef3fb 0%, #f7f9fc 100%); color: var(--text); }
        a { color: var(--brand-2); text-decoration: none; }
        .topbar { background: linear-gradient(90deg, var(--brand), #0f2744); color: #fff; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .topbar h1 { margin: 0; font-size: 18px; }
        .topbar .meta { font-size: 13px; opacity: .9; }
        .shell { display: grid; grid-template-columns: 260px 1fr; min-height: calc(100vh - 64px); }
        .sidebar { background: rgba(255,255,255,.92); border-right: 1px solid var(--line); padding: 20px; }
        .sidebar .user-box { background: #f7fbff; border: 1px solid var(--line); padding: 16px; border-radius: 16px; margin-bottom: 18px; }
        .nav a { display: block; padding: 11px 14px; border-radius: 12px; color: var(--text); margin-bottom: 8px; border: 1px solid transparent; }
        .nav a:hover { background: #eef5ff; border-color: #d5e6ff; }
        .content { padding: 24px; }
        .container { max-width: 1200px; }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: 18px; padding: 18px; box-shadow: 0 10px 30px rgba(16,35,63,.04); margin-bottom: 18px; }
        .grid { display: grid; gap: 16px; }
        .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .stat { padding: 18px; border-radius: 16px; background: linear-gradient(180deg, #fff 0%, #f7fbff 100%); border: 1px solid var(--line); }
        .stat .value { font-size: 28px; font-weight: 700; margin: 6px 0 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border-bottom: 1px solid var(--line); padding: 12px 10px; text-align: left; vertical-align: top; }
        .table th { font-size: 13px; color: var(--muted); }
        .btn { display: inline-block; padding: 10px 14px; border-radius: 10px; border: 1px solid transparent; background: var(--brand-2); color: #fff; cursor: pointer; }
        .btn.secondary { background: #fff; color: var(--brand); border-color: var(--line); }
        .btn.danger { background: var(--danger); }
        .btn.ok { background: var(--ok); }
        .field { margin-bottom: 14px; }
        .field label { display: block; margin-bottom: 6px; font-weight: 600; }
        .field input, .field select, .field textarea { width: 100%; padding: 11px 12px; border: 1px solid var(--line); border-radius: 10px; font: inherit; }
        .alert { padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; }
        .alert.success { background: #ecfdf3; color: var(--ok); border: 1px solid #abefc6; }
        .alert.error { background: #fef3f2; color: var(--danger); border: 1px solid #fecdca; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; background: #eef5ff; color: var(--brand); font-size: 12px; }
        .muted { color: var(--muted); }
        @media (max-width: 980px) { .shell { grid-template-columns: 1fr; } .sidebar { border-right: 0; border-bottom: 1px solid var(--line); } .grid.cols-2, .grid.cols-3, .grid.cols-4 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header class="topbar">
    <div>
        <h1>E-Office Tanda Tangan Digital</h1>
        <div class="meta"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="meta">
        <?= htmlspecialchars($currentUser['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        <?php if (!empty($currentUser['role'])): ?>
            | <?= htmlspecialchars(ucfirst($currentUser['role']), ENT_QUOTES, 'UTF-8') ?>
        <?php endif; ?>
    </div>
</header>
<div class="shell">