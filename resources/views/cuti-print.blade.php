<!DOCTYPE html>
<html lang="en">

<head>
    <style>
                @page {
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            transform: scale(0.75);
        }

        .container {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid black;
            height: auto;
            position: relative;
            transform: translate(-40mm, -30mm);
        }

        h2 {
            text-align: left;
            margin: 0;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        h3 {
            text-align: center;
            margin: 0;
            text-decoration: underline;
        }

        .form-content {
            margin-top: 30px;
            font-size: 18px;
        }

        .form-row {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .form-row .label {
            width: 200px;
            flex-shrink: 0;
            display: inline-block;
            vertical-align: top;
        }

        .form-row .label1 {
            flex-shrink: 0;
            display: inline-block;
            vertical-align: top;
            font-size: 18px;
        }

        .form-row .titikdua {
            display: inline-block;
            text-align: right;
            vertical-align: top;
        }

        .form-row input[type="text"] {
            flex-grow: 1;
            border: none;
            border-bottom: 1px solid black;
            margin-left: 10px;
            width: 65%;
            max-width: 600px;
        }

        .form-row .jam {
            flex-grow: 1;
            border: none;
            border-bottom: 1px solid black;
            margin-left: 5px;
            width: 100px;
        }

        .checkbox-group {
            display: inline-block;
            width: 150px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            transform: scale(1.5);
            margin-right: 5px;
            margin-left: 10px;
        }

        .footer {
            margin-top: 30px;
            font-size: 18px;
            text-align: center;
            width: 100%;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            width: 33%;
            vertical-align: top;
            text-align: center;
        }

        .signature {
            margin-top: 50px;
            width: 70%;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }

        .signature:before,
        .signature:after {
            content: "(";
            position: absolute;
            font-weight: bold;
            font-size: 20px;
            top: -8px;
            line-height: 0;
        }

        .signature:after {
            content: ")";
            right: -12px;
        }

        .signature:before {
            left: -12px;
        }

        .signature-line {
            border-top: 1px solid black;
            display: inline-block;
            width: 150px;
        }

        img{
            width: 45px;
            height: 30px;
            padding-bottom: 10px;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h2 style="text-align: center;"><strong>PT. INDRA ERAMULTI LOGAM INDUSTRI</strong></h2>
        <h3><strong>SURAT PERMOHONAN CUTI</strong></h3>
        <div class="form-content">
            <div class="form-row">
                <span class="label1">MOHON DIBERI IJIN KEPADA </span>
            </div>

            <div class="form-content">
                <div class="form-row">
                    <span class="label">NAMA </span>
                    <span class="titikdua">:</span>
                    <input type="text" value="{{ $cuti->user->name }}">
                </div>
            </div>

            <div class="form-row">
                <span class="label">NIP </span> <span class="titikdua">:</span><input type="text"
                    value="{{ $cuti->user->uid }}">
            </div>

            <div class="form-row">
                <span class="label">TANGGAL </span> <span class="titikdua">:</span> <input type="text"
                    value="{{ \Carbon\Carbon::parse($cuti->created_at)->format('d-m-Y') }}">
            </div>

            <div class="form-row">
                <span class="label">KETERANGAN </span> <span class="titikdua">:</span> <input type="text"
                    value="{{ $cuti->keterangan }}">
            </div>

            <div class="form-row">
                <span class="label">MULAI CUTI :</span>
                <input type="text" class="jam"
                    value="{{ \Carbon\Carbon::parse($cuti->start_date)->format('d M Y') }}">
                <span class="label" style="margin-left: 50px;">AKHIR CUTI :</span>
                <input type="text" class="jam"
                    value="{{ \Carbon\Carbon::parse($cuti->end_date)->format('d M Y') }}">
            </div>

            <div class="footer">
                <table class="footer-table">
                    <tr>
                        <td>PEMOHON</td>
                        <td>DISETUJUI OLEH</td>
                        {{-- <td>MENGETAHUI</td> --}}
                    </tr>
                    <tr>
                        <td>
                            <div class="">
                                <img src="{{$cuti->user->signature}}" >
                            </div>
                        </td>
                        <td>
                            <div class="" @if ($cuti->status == 'acc1' && $cuti->user->department->leader->signature)>
                                <img src="{{$cuti->user->department->leader->signature}}" >
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="signature-line"></div>
                        </td>
                        <td>
                            <div class="signature-line"></div>
                        </td>
                        {{-- <td>
                            <div class="signature-line"></div>
                        </td> --}}
                    </tr>
                </table>
            </div>
        </div>


        </div>
</body>

</html>
