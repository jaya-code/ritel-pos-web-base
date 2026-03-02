<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $transaction->invoice }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 58mm;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .dashed-line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            vertical-align: top;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body {
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>

<body onload="window.print(); setTimeout(() => window.close(), 500);">
    @if (isset($transaction->store->logo) && $transaction->store->logo)
        <div class="center" style="margin-bottom: 10px;">
            <img src="{{ asset('storage/' . $transaction->store->logo) }}" alt="Logo"
                style="max-width: 40mm; display: block; margin: 0 auto;">
        </div>
    @endif
    <div class="center bold" style="font-size: 16px;">
        {{ $transaction->store->name ?? 'Toko Saya' }}
    </div>
    <div class="center" style="margin-bottom: 5px;">
        {{ $transaction->store->address ?? 'Alamat Toko' }}<br>
        Telp: {{ $transaction->store->phone ?? '-' }}
    </div>

    <div class="dashed-line"></div>

    <div class="flex">
        <span>No: {{ $transaction->invoice }}</span>
    </div>
    <div class="flex">
        <span>Tgl: {{ \Carbon\Carbon::parse($transaction->tgl_penjualan)->format('d/m/y H:i') }}</span>
    </div>
    <div class="flex">
        <span>Ksr: {{ substr($transaction->user->name ?? 'Kasir', 0, 10) }}</span>
    </div>

    <div class="dashed-line"></div>

    <table class="table">
        @foreach ($transaction->details as $item)
            <tr>
                <td colspan="3">{{ $item->product->product_name ?? 'Produk' }}</td>
            </tr>
            <tr>
                <td>{{ $item->qty_jual }}x</td>
                <td class="right">{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($item->sub_total, 0, ',', '.') }}</td>
            </tr>
            @if ($item->diskon > 0)
                <tr>
                    <td></td>
                    <td>Disc</td>
                    <td class="right">-{{ number_format($item->diskon, 0, ',', '.') }}</td>
                </tr>
            @endif
        @endforeach
    </table>

    <div class="dashed-line"></div>

    <div class="flex bold">
        <span>Total:</span>
        <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
    </div>

    <div class="flex">
        <span>Bayar ({{ $transaction->metode_pembayaran }}):</span>
        <span>Rp {{ number_format($transaction->jumlah_uang, 0, ',', '.') }}</span>
    </div>

    <div class="flex">
        <span>Kembali:</span>
        <span>Rp {{ number_format($transaction->uang_kembali, 0, ',', '.') }}</span>
    </div>

    <div class="dashed-line"></div>

    <div class="center" style="margin-top: 10px;">
        Terima Kasih<br>
        Barang yang sudah dibeli<br>
        tidak dapat ditukar/dikembalikan
    </div>
</body>

</html>
