<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>LKB_{{ $monthName }}_{{ $year }}</title>
    <style>
        @page {
            size: A4
                {{ $orientation ?? 'landscape' }}
            ;
            margin: 12mm 10mm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size:
                {{ ($fontSize ?? 'medium') == 'small' ? '8.5pt' : (($fontSize ?? 'medium') == 'medium' ? '10pt' : '11.5pt') }}
            ;
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
            font-size:
                {{ ($fontSize ?? 'medium') == 'small' ? '7.5pt' : (($fontSize ?? 'medium') == 'medium' ? '8.5pt' : '9.5pt') }}
            ;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }

        .main-table td {
            padding: 8px 8px;
            border: 1px solid #cbd5e1;
            font-size:
                {{ ($fontSize ?? 'medium') == 'small' ? '8pt' : (($fontSize ?? 'medium') == 'medium' ? '9pt' : '10pt') }}
            ;
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
            height: 130px;
            /* Ruang untuk barcode TTE (Tanda Tangan Elektronik) */
        }

        .signature-name {
            text-decoration: underline;
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
            <!-- Official Kemenag Logo from local public asset -->
            <img src="{{ public_path('v2/logo-kemenag.png') }}" width="54" height="54" style="object-fit: contain;" />
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
            <td class="value">{{ $pegawaiName ?: '-' }}</td>
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
                @if(!isset($showColumns['status']) || $showColumns['status'])
                    <th style="width: 75px;">Status</th>
                @endif
                @if(!isset($showColumns['perkin']) || $showColumns['perkin'])
                    <th style="width: 140px;">SK / Perkin</th>
                @endif
                @if(!isset($showColumns['iksk']) || $showColumns['iksk'])
                    <th style="width: 150px;">Indikator Kinerja</th>
                @endif
                @if(!isset($showColumns['volume']) || $showColumns['volume'])
                    <th style="width: 40px;">Vol</th>
                @endif
                <th style="width: 60px;">Satuan</th>
                @if(!isset($showColumns['uraian']) || $showColumns['uraian'])
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
                    @if(!isset($showColumns['status']) || $showColumns['status'])
                        <td class="text-center">{{ $record->status_kehadiran }}</td>
                    @endif
                    @if(!isset($showColumns['perkin']) || $showColumns['perkin'])
                        <td style="font-weight: 500;">
                            {{ $record->iksk && $record->iksk->sasaran_kegiatan && $record->iksk->sasaran_kegiatan->perkin ? $record->iksk->sasaran_kegiatan->perkin->label_perkin : '-' }}
                        </td>
                    @endif
                    @if(!isset($showColumns['iksk']) || $showColumns['iksk'])
                        <td>
                            {{ $record->iksk ? $record->iksk->indikator : '-' }}
                        </td>
                    @endif
                    @if(!isset($showColumns['volume']) || $showColumns['volume'])
                        <td class="text-center font-semibold" style="color: #0f172a;">
                            {{ $record->iksk ? $record->iksk->target_vol : '-' }}
                        </td>
                    @endif
                    <td class="text-center">
                        {{ $record->iksk ? $record->iksk->target_satuan : '-' }}
                    </td>
                    @if(!isset($showColumns['uraian']) || $showColumns['uraian'])
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
            <div class="signature-space">
                @if(isset($enableAnchorAtasan) && $enableAnchorAtasan)
                    <div
                        style="font-size: 8.5pt; color: #0f172a; font-weight: normal; padding-top: 35px; text-align: center; font-family: Courier, monospace;">
                        {{ $anchorAtasanText }}
                    </div>
                @endif
            </div>
            <div class="signature-name">{{ $atasanName ?: '..........................................' }}</div>
            <div class="signature-nip">NIP. {{ $atasanNip ?: '..........................................' }}</div>
        </div>
        <div class="signature-col">
            <p>{{ $signatureDate ?: '..........................................' }}</p>
            <p>{{ $pegawaiJabatan ?: 'Pegawai Negeri Sipil' }},</p>
            <div class="signature-space">
                @if(isset($enableAnchorPegawai) && $enableAnchorPegawai)
                    <div
                        style="font-size: 8.5pt; color: #0f172a; font-weight: normal; padding-top: 35px; text-align: center; font-family: Courier, monospace;">
                        {{ $anchorPegawaiText }}
                    </div>
                @endif
            </div>
            <div class="signature-name">{{ $pegawaiName }}</div>
            <div class="signature-nip">NIP. {{ $pegawaiNip }}</div>
        </div>
        <div class="clear"></div>
    </div>

</body>

</html>