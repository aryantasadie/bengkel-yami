<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $transaksi->no_nota }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #333;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 13px;
            color: #666;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-section .left, .info-section .right {
            width: 48%;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
        }
        .info-label {
            width: 130px;
            font-weight: 600;
            color: #555;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-size: 13px;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .totals .row.total {
            border-top: 2px solid #333;
            padding-top: 8px;
            margin-top: 5px;
            font-weight: 700;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #888;
        }
        .print-btn {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn button {
            background: #2563EB;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        .print-btn button:hover {
            background: #1D4ED8;
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()">🖨 Cetak Nota</button>
    </div>

    <div class="header">
        <h1>BENGKEL YAMI</h1>
        <p>Jl. Contoh Alamat No. 123, Kota</p>
        <p>Telp: (021) 1234-5678</p>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600;">NOTA PEMBAYARAN</h2>
    </div>

    <div class="info-section">
        <div class="left">
            <div class="info-row">
                <span class="info-label">No. Nota</span>
                <span class="info-value">: {{ $transaksi->no_nota }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">: {{ $transaksi->tanggal ? $transaksi->tanggal->format('d M Y, H:i') : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Metode Bayar</span>
                <span class="info-value">: {{ ucfirst($transaksi->metode_bayar ?? '-') }}</span>
            </div>
        </div>
        <div class="right">
            <div class="info-row">
                <span class="info-label">No. Pesanan</span>
                <span class="info-value">: {{ $transaksi->pesanan->no_pesanan ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer</span>
                <span class="info-value">: {{ $transaksi->pesanan->customer->nama ?? '-' }}</span>
            </div>
            @if($transaksi->pesanan->customer->no_telp ?? null)
            <div class="info-row">
                <span class="info-label">Telp</span>
                <span class="info-value">: {{ $transaksi->pesanan->customer->no_telp }}</span>
            </div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">No</th>
                <th>Nama Jasa</th>
                <th style="width: 120px; text-align: right;">Harga</th>
                <th style="width: 50px; text-align: center;">Qty</th>
                <th style="width: 130px; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->pesanan->pesananJasa as $index => $pj)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $pj->jasa->nama_jasa ?? '-' }}</td>
                <td style="text-align: right;">Rp {{ number_format($pj->harga_snapshot, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $pj->qty }}</td>
                <td style="text-align: right;">Rp {{ number_format($pj->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Total Harga</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
        @if($transaksi->diskon_nominal > 0 || $transaksi->diskon_persen > 0)
        <div class="row">
            <span>Diskon{{ $transaksi->diskon_persen > 0 ? ' ('.$transaksi->diskon_persen.'%)' : '' }}</span>
            <span>- Rp {{ number_format($transaksi->diskon_nominal, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($transaksi->dp > 0)
        <div class="row">
            <span>DP</span>
            <span>Rp {{ number_format($transaksi->dp, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="row total">
            <span>Total Bayar</span>
            <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
        </div>
        @if($transaksi->sisa_bayar > 0)
        <div class="row">
            <span>Sisa Bayar</span>
            <span>Rp {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda.</p>
        <p>Bengkel Yami - {{ now()->format('d M Y') }}</p>
    </div>
</body>
</html>
