<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $transaksi->no_nota }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; padding: 20px; max-width: 400px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px dashed #000; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin-bottom: 4px; }
        .header p { font-size: 11px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .info-row .label { color: #666; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .items table { width: 100%; border-collapse: collapse; }
        .items th, .items td { text-align: left; padding: 4px 0; }
        .items th { border-bottom: 1px solid #000; font-size: 11px; }
        .items td:last-child, .items th:last-child { text-align: right; }
        .total-section { margin-top: 10px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .total-row.grand { font-weight: bold; font-size: 14px; border-top: 2px dashed #000; padding-top: 8px; margin-top: 8px; }
        .footer { text-align: center; margin-top: 20px; border-top: 2px dashed #000; padding-top: 10px; font-size: 11px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 24px; font-size: 14px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 4px;">Cetak Nota</button>
        <button onclick="window.close()" style="padding: 8px 24px; font-size: 14px; cursor: pointer; margin-left: 8px; border: 1px solid #ccc; border-radius: 4px;">Tutup</button>
    </div>

    <div class="header">
        <h1>BENGKEL YAMI</h1>
        <p>Jl. Contoh Alamat Bengkel No. 123</p>
        <p>Telp: (021) 123-4567</p>
    </div>

    <div class="info-row">
        <span class="label">No Nota:</span>
        <span>{{ $transaksi->no_nota }}</span>
    </div>
    <div class="info-row">
        <span class="label">Tanggal:</span>
        <span>{{ $transaksi->tanggal ? $transaksi->tanggal->format('d/m/Y H:i') : '-' }}</span>
    </div>
    <div class="info-row">
        <span class="label">No Pesanan:</span>
        <span>{{ $transaksi->pesanan->no_pesanan ?? '-' }}</span>
    </div>
    <div class="info-row">
        <span class="label">Customer:</span>
        <span>{{ $transaksi->pesanan->customer->nama ?? '-' }}</span>
    </div>

    <div class="divider"></div>

    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Jasa</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->pesanan->pesananJasa as $pj)
                <tr>
                    <td>{{ $pj->jasa->nama_jasa ?? '-' }}</td>
                    <td>{{ $pj->qty }}</td>
                    <td>Rp {{ number_format($pj->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($transaksi->pesanan->pesananSparepart->count() > 0)
    <div style="margin-top: 10px; font-weight: bold; font-size: 11px;">Sparepart</div>
    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->pesanan->pesananSparepart as $ps)
                <tr>
                    <td>{{ $ps->sparepart->nama ?? '-' }}</td>
                    <td>Rp{{ number_format($ps->harga_snapshot, 0, ',', '.') }}</td>
                    <td>{{ $ps->qty }}</td>
                    <td>Rp{{ number_format($ps->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="divider"></div>

    <div class="total-section">
        <div class="total-row">
            <span>Total Harga</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
        @if($transaksi->diskon_nominal > 0)
        <div class="total-row">
            <span>Diskon</span>
            <span>- Rp {{ number_format($transaksi->diskon_nominal, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row grand">
            <span>Total Bayar</span>
            <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
        </div>
        @if($transaksi->dp > 0)
        <div class="total-row">
            <span>DP</span>
            <span>Rp {{ number_format($transaksi->dp, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Sisa Bayar</span>
            <span>Rp {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row">
            <span>Metode</span>
            <span>{{ ucfirst($transaksi->metode_bayar) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda</p>
        <p>Bengkel Yami</p>
    </div>
</body>
</html>
