@php
    $totalJasa = $pesanan->pesananJasa->sum('subtotal');
    $totalSparepart = $pesanan->pesananSparepart->sum('subtotal');
    
    $nominalDiskonJasa = $withDiskon ? ($totalJasa * ($pesanan->diskon_persen / 100)) : 0;
    $nominalDiskonSparepart = $withDiskon ? ($totalSparepart * ($pesanan->diskon_sparepart_persen / 100)) : 0;
    
    $subtotalJasa = $totalJasa - $nominalDiskonJasa;
    $subtotalSparepart = $totalSparepart - $nominalDiskonSparepart;
    
    $totalKeseluruhan = $subtotalJasa + $subtotalSparepart;
    $sisaTagihan = max(0, $totalKeseluruhan - $pesanan->dp);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimasi - {{ $pesanan->no_pesanan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; padding: 20px; max-width: 400px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px dashed #000; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin-bottom: 4px; }
        .header p { font-size: 11px; white-space: pre-wrap; }
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
        .footer { text-align: center; margin-top: 20px; border-top: 2px dashed #000; padding-top: 10px; font-size: 11px; white-space: pre-wrap; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 24px; font-size: 14px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 4px;">Cetak Estimasi</button>
        <button onclick="window.close()" style="padding: 8px 24px; font-size: 14px; cursor: pointer; margin-left: 8px; border: 1px solid #ccc; border-radius: 4px;">Tutup</button>
    </div>

    <div class="header">
        <h1>{{ $settings['nama_bengkel'] }}</h1>
        <p>{{ $settings['alamat_bengkel'] }}</p>
        <p>Telp: {{ $settings['no_telp_bengkel'] }}</p>
    </div>

    <div style="text-align: center; font-weight: bold; margin-bottom: 15px; font-size: 14px;">ESTIMASI BIAYA</div>

    <div class="info-row">
        <span class="label">Tanggal Cetak:</span>
        <span>{{ now()->format('d/m/Y H:i') }}</span>
    </div>
    <div class="info-row">
        <span class="label">Customer:</span>
        <span>{{ $pesanan->customer->nama ?? '-' }}</span>
    </div>

    <div class="divider"></div>

    <div style="font-weight: bold; font-size: 11px;">JASA</div>
    <div class="items">
        <table>
            <tbody>
                @foreach($pesanan->pesananJasa as $pj)
                <tr>
                    <td>{{ $pj->nama_snapshot ?? $pj->jasa->nama_jasa ?? '-' }} (x{{ $pj->qty }})</td>
                    <td>Rp{{ number_format($pj->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($totalJasa > 0)
        <div style="text-align: right; margin-top: 4px; padding-top: 4px; border-top: 1px dotted #ccc;">
            Total Jasa: Rp{{ number_format($totalJasa, 0, ',', '.') }}
            @if($withDiskon && $pesanan->diskon_persen > 0)
            <br><small>Diskon ({{ $pesanan->diskon_persen }}%): -Rp{{ number_format($nominalDiskonJasa, 0, ',', '.') }}</small>
            <br><strong>Sub Jasa: Rp{{ number_format($subtotalJasa, 0, ',', '.') }}</strong>
            @endif
        </div>
        @endif
    </div>

    <div class="divider"></div>

    @if($pesanan->pesananSparepart->count() > 0)
    <div style="font-weight: bold; font-size: 11px;">SPAREPART</div>
    <div class="items">
        <table>
            <tbody>
                @foreach($pesanan->pesananSparepart as $ps)
                <tr>
                    <td>{{ $ps->nama_snapshot ?? $ps->sparepart->nama ?? '-' }} (x{{ $ps->qty }})</td>
                    <td>Rp{{ number_format($ps->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($totalSparepart > 0)
        <div style="text-align: right; margin-top: 4px; padding-top: 4px; border-top: 1px dotted #ccc;">
            Total Sparepart: Rp{{ number_format($totalSparepart, 0, ',', '.') }}
            @if($withDiskon && $pesanan->diskon_sparepart_persen > 0)
            <br><small>Diskon ({{ $pesanan->diskon_sparepart_persen }}%): -Rp{{ number_format($nominalDiskonSparepart, 0, ',', '.') }}</small>
            <br><strong>Sub Sparepart: Rp{{ number_format($subtotalSparepart, 0, ',', '.') }}</strong>
            @endif
        </div>
        @endif
    </div>
    <div class="divider"></div>
    @endif

    <div class="total-section">
        <div class="total-row grand">
            <span>TOTAL ESTIMASI</span>
            <span>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</span>
        </div>
        @if($pesanan->dp > 0)
        <div class="total-row">
            <span>DP</span>
            <span>Rp {{ number_format($pesanan->dp, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Sisa Tagihan</span>
            <span>Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>{{ $settings['catatan_kaki'] }}</p>
    </div>
</body>
</html>

