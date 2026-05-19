<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LKB_{{ $monthName }}_{{ $year }}</title>
    <style>
        @page {
            size: A4 {{ $orientation }};
            margin: 12mm 10mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: {{ $fontSize == 'small' ? '8.5pt' : ($fontSize == 'medium' ? '10pt' : '11.5pt') }};
            color: #1e293b;
            line-height: 1.4;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header-container {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-logo {
            float: left;
            width: 54px;
            height: 54px;
            margin-right: 15px;
        }
        .header-text {
            float: left;
            margin-top: 5px;
        }
        .header-text h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.2px;
        }
        .header-text p {
            margin: 3px 0 0 0;
            font-size: 9.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }
        .clear {
            clear: both;
        }
        .info-table {
            width: 100%;
            max-width: 550px;
            margin-bottom: 20px;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .info-table td {
            padding: 6px 10px;
            font-size: 8.5pt;
            color: #334155;
            vertical-align: top;
        }
        .info-table td.label {
            width: 110px;
            color: #64748b;
            font-weight: 500;
        }
        .info-table td.colon {
            width: 8px;
            text-align: center;
        }
        .info-table td.value {
            font-weight: bold;
            color: #0f172a;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .main-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: {{ $fontSize == 'small' ? '7.5pt' : ($fontSize == 'medium' ? '8.5pt' : '9.5pt') }};
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        .main-table td {
            padding: 8px 8px;
            border: 1px solid #cbd5e1;
            font-size: {{ $fontSize == 'small' ? '8pt' : ($fontSize == 'medium' ? '9pt' : '10pt') }};
            vertical-align: top;
            color: #334155;
        }
        .text-center {
            text-align: center !important;
        }
        .font-semibold {
            font-weight: 600;
        }
        .signature-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-col {
            width: 50%;
            float: left;
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            color: #1e293b;
        }
        .signature-space {
            height: 75px;
        }
        .signature-name {
            text-decoration: underline;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .signature-nip {
            font-weight: normal;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- Header Block -->
    <div class="header-container">
        <div class="header-logo">
            <!-- 100% Vector Local Offline Kemenag Emblem -->
            <svg viewBox="0 0 100 100" width="54" height="54" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="46" fill="#0b5e3a" stroke="#d4af37" stroke-width="2.5" />
                <circle cx="50" cy="50" r="42" fill="none" stroke="#d4af37" stroke-width="1" stroke-dasharray="3,2" />
                <path d="M 50 25 Q 68 28 65 52 Q 62 72 50 82 Q 38 72 35 52 Q 32 28 50 25 Z" fill="none" stroke="#d4af37" stroke-width="1.5" />
                <polygon points="50,29 52.5,35.5 59.5,35.5 54,39.5 56.5,46 50,42 43.5,46 46,39.5 40.5,35.5 47.5,35.5" fill="#d4af37" />
                <line x1="33" y1="50" x2="67" y2="50" stroke="#d4af37" stroke-width="2" stroke-linecap="round" />
                <line x1="50" y1="42" x2="50" y2="68" stroke="#d4af37" stroke-width="2" stroke-linecap="round" />
                <path d="M 45 42 L 55 42" stroke="#d4af37" stroke-width="1.5" />
                <line x1="33" y1="50" x2="27" y2="60" stroke="#d4af37" stroke-width="0.8" />
                <line x1="33" y1="50" x2="39" y2="60" stroke="#d4af37" stroke-width="0.8" />
                <path d="M 25 60 Q 33 66 41 60 Z" fill="#d4af37" />
                <line x1="67" y1="50" x2="61" y2="60" stroke="#d4af37" stroke-width="0.8" />
                <line x1="67" y1="50" x2="73" y2="60" stroke="#d4af37" stroke-width="0.8" />
                <path d="M 59 60 Q 67 66 75 60 Z" fill="#d4af37" />
                <path d="M 38 70 Q 50 74 62 70 L 62 67 Q 50 71 38 67 Z" fill="#ffffff" stroke="#d4af37" stroke-width="1" />
                <line x1="50" y1="67" x2="50" y2="72" stroke="#d4af37" stroke-width="1" />
            </svg>
        </div>
        <div class="header-text">
            <h2>LAPORAN KINERJA BULANAN (LKB) PEGAWAI</h2>
            <p>BULAN {{ $monthName }} TAHUN {{ $year }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Personal Information Block -->
    <table class="info-table">
        <tr>
            <td class="label">Nama Pegawai</td>
            <td class="colon">:</td>
            <td class="value" style="text-transform: uppercase;">{{ $pegawaiName ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIP</td>
            <td class="colon">:</td>
            <td class="value">{{ $pegawaiNip ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $pegawaiJabatan ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Unit Kerja</td>
            <td class="colon">:</td>
            <td class="value">{{ $satkerName ?: '-' }}</td>
        </tr>
    </table>

    <!-- Main Performance Records Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                @if(isset($showColumns['status']) && $showColumns['status'])
                    <th style="width: 75px;">Status</th>
                @endif
                @if(isset($showColumns['perkin']) && $showColumns['perkin'])
                    <th style="width: 140px;">SK / Perkin</th>
                @endif
                @if(isset($showColumns['iksk']) && $showColumns['iksk'])
                    <th style="width: 150px;">Indikator Kinerja</th>
                @endif
                @if(isset($showColumns['volume']) && $showColumns['volume'])
                    <th style="width: 40px;">Vol</th>
                @endif
                <th style="width: 60px;">Satuan</th>
                @if(isset($showColumns['uraian']) && $showColumns['uraian'])
                    <th>Uraian Kegiatan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td class="text-center font-semibold" style="color: #64748b;">{{ $index + 1 }}</td>
                    <td class="text-center font-semibold">
                        {{ date('d-m-Y', strtotime($record->tanggal)) }}
                    </td>
                    @if(isset($showColumns['status']) && $showColumns['status'])
                        <td class="text-center">{{ $record->status_kehadiran }}</td>
                    @endif
                    @if(isset($showColumns['perkin']) && $showColumns['perkin'])
                        <td style="font-weight: 500;">
                            {{ $record->iksk && $record->iksk->sasaran_kegiatan && $record->iksk->sasaran_kegiatan->perkin ? $record->iksk->sasaran_kegiatan->perkin->label_perkin : '-' }}
                        </td>
                    @endif
                    @if(isset($showColumns['iksk']) && $showColumns['iksk'])
                        <td>
                            {{ $record->iksk ? $record->iksk->indikator : '-' }}
                        </td>
                    @endif
                    @if(isset($showColumns['volume']) && $showColumns['volume'])
                        <td class="text-center font-semibold" style="color: #0f172a;">
                            {{ $record->iksk && $record->iksk->sasaran_kegiatan ? $record->iksk->sasaran_kegiatan->target_vol : '-' }}
                        </td>
                    @endif
                    <td class="text-center">
                        {{ $record->iksk && $record->iksk->sasaran_kegiatan ? $record->iksk->sasaran_kegiatan->target_satuan : '-' }}
                    </td>
                    @if(isset($showColumns['uraian']) && $showColumns['uraian'])
                        <td>1. {{ $record->uraian_pekerjaan }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #64748b; font-style: italic; padding: 20px;">
                        Tidak ada data kinerja harian untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature block -->
    <div class="signature-section">
        <div class="signature-col">
            <p>Mengetahui,</p>
            <p>Atasan Langsung</p>
            <div class="signature-space"></div>
            <div class="signature-name">{{ $atasanName ?: '..........................................' }}</div>
            <div class="signature-nip">NIP. {{ $atasanNip ?: '..........................................' }}</div>
        </div>
        <div class="signature-col">
            <p>{{ $signatureDate ?: '..........................................' }}</p>
            <p>{{ $pegawaiJabatan ?: 'Pegawai Negeri Sipil' }},</p>
            <div class="signature-space"></div>
            <div class="signature-name">{{ $pegawaiName }}</div>
            <div class="signature-nip">NIP. {{ $pegawaiNip }}</div>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
