<?php $pageTitle = $data['title'] ?? APP_NAME; ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        :root { --bg: #0f172a; --panel: #ffffff; --text: #10233f; --muted: #667085; --brand: #0f4c81; --brand-2: #1677ff; --line: #dde6f3; --ok: #1f7a4d; --danger: #b42318; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Arial, Helvetica, sans-serif; background: radial-gradient(circle at top, rgba(22,119,255,.20), transparent 35%), linear-gradient(180deg, #f4f7fb, #edf2f9); color: var(--text); }
        .public-shell { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .public-card { width: min(980px, 100%); background: rgba(255,255,255,.94); backdrop-filter: blur(8px); border: 1px solid var(--line); border-radius: 22px; box-shadow: 0 20px 50px rgba(16,35,63,.12); padding: 28px; }
        .brand { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
        .brand h1 { margin: 0; font-size: 24px; }
        .muted { color: var(--muted); }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; background: #eef5ff; color: var(--brand); font-size: 12px; }
        .card { background: #fff; border: 1px solid var(--line); border-radius: 18px; padding: 20px; }
        .btn { display: inline-block; padding: 10px 14px; border-radius: 10px; border: 0; background: var(--brand-2); color: #fff; text-decoration: none; }
        .alert { padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; }
        .alert.success { background: #ecfdf3; color: var(--ok); border: 1px solid #abefc6; }
        .alert.error { background: #fef3f2; color: var(--danger); border: 1px solid #fecdca; }
        .field { margin-bottom: 14px; }
        .field label { display: block; margin-bottom: 6px; font-weight: 600; }
        .field input, .field textarea, .field select { width: 100%; padding: 11px 12px; border: 1px solid var(--line); border-radius: 10px; font: inherit; }
        .grid { display: grid; gap: 16px; }
        .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        @media (max-width: 800px) { .brand { flex-direction: column; align-items: flex-start; } .grid.cols-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="public-shell">
    <div class="public-card">