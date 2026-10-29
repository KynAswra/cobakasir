<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export ke Google Sheets</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff7ed; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .card { background: white; border-radius: 1.5rem; padding: 2rem; max-width: 420px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(249,115,22,.15); }
        .icon { width: 64px; height: 64px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; }
        h1 { font-size: 1.25rem; font-weight: 800; color: #1c1917; margin-bottom: .5rem; }
        p { color: #78716c; font-size: .875rem; line-height: 1.6; margin-bottom: 1.5rem; }
        .steps { text-align: left; background: #fafaf9; border-radius: .75rem; padding: 1rem; margin-bottom: 1.5rem; font-size: .8rem; color: #57534e; }
        .steps li { margin-bottom: .4rem; padding-left: .25rem; }
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 1.5rem; border-radius: .75rem; font-weight: 700; font-size: .875rem; text-decoration: none; border: none; cursor: pointer; transition: all .2s; }
        .btn-green { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; box-shadow: 0 4px 14px rgba(34,197,94,.35); }
        .btn-green:hover { transform: translateY(-1px); }
        .btn-outline { background: white; color: #57534e; border: 2px solid #e7e5e4; margin-top: .75rem; }
        .status { margin-top: 1rem; font-size: .8rem; font-weight: 600; color: #16a34a; }
        .error { color: #dc2626; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📊</div>
        <h1>Export ke Google Sheets</h1>
        <p>Periode: <strong>{{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</strong></p>

        <ol class="steps">
            <li>1. File CSV laporan otomatis diunduh</li>
            <li>2. Google Sheets akan terbuka di tab baru</li>
            <li>3. Klik <strong>File → Import</strong></li>
            <li>4. Pilih tab <strong>Upload</strong>, upload file CSV</li>
            <li>5. Klik <strong>Import data</strong></li>
        </ol>

        <button id="btnExport" class="btn btn-green" onclick="startExport()">
            Mulai Export
        </button>
        <br>
        <a href="{{ route('reports.index', ['date_from' => $from->format('Y-m-d'), 'date_to' => $to->format('Y-m-d')]) }}" class="btn btn-outline">
            ← Kembali ke Laporan
        </a>

        <p id="status" class="status"></p>
    </div>

    <script>
        const csvContent = @json($csv);

        function startExport() {
            const status = document.getElementById('status');
            const filename = @json($filename);

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            URL.revokeObjectURL(link.href);

            setTimeout(() => {
                window.open('https://sheets.google.com/create', '_blank');
            }, 500);

            status.textContent = '✅ File CSV diunduh! Import file ke Google Sheets yang terbuka.';
            status.classList.remove('error');
        }

        window.addEventListener('load', () => {
            setTimeout(startExport, 300);
        });
    </script>
</body>
</html>
