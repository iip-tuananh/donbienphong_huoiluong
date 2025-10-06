<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/pdf.css') }}" type="text/css" />
</head>

<body>
    <h4 style="text-transform:uppercase; text-align: center"><b>Danh sách xe</b></h4>
    <table class="table table-bordered pdf">
        <thead>
            <tr>
                <th>STT</th>
                <th>Biển số</th>
                <th>Chủ sở hữu</th>
                <th>Dòng xe</th>
                <th>Hãng sx</th>
                <th>Loại xe</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $item)
            <tr>
                <td style="text-align:center">{{ $key + 1 }}</td>
                <td>{{ $item->licensePlate->license_plate }}</td>
                <td>{{ $item->customers[0]->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->manufact->name }}</td>
                <td>{{ $item->type->name }}</td>
                <td>{{ $item->status == 1 ? 'Hoạt động' : 'Khóa' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
<style>
    body {
        font-family: DejaVu Sans;
    }

    .pdf {
        border-collapse: collapse;
        width: 100%;
    }

    .pdf td,
    .pdf th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .pdf tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .pdf tr:hover {
        background-color: #ddd;
    }

    .pdf th {
        padding-top: 12px;
        padding-bottom: 12px;
        background-color: #4CAF50;
        color: white;
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    td,
    th {
        border: 1px solid black;
        padding: 5px 8px !important;
    }

    table {
        border-collapse: collapse;
    }

    .no-border td {
        border: none !important;
    }

    @media print {
        .d-print-none {
            display: none !important;
        }

        .block {
            page-break-inside: avoid;
        }
    }

    /* // barcode */

    .barcode {
        text-align: center;
    }

    .mb-2 {
        margin-bottom: 0.5rem !important;
    }

    .border {
        border: 1px solid #e9ecef !important;
    }

    .p-2 {
        padding: 0.5rem !important;
    }

    .d-flex {
        display: flex !important;
    }

    .promo-group-item-name {
        width: 300px;
    }

    .ml-2 {
        margin-left: 0.5rem !important;
    }

    .align-items-center {
        align-items: center;
    }

    th {
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    td>p {
        padding: 0;
        margin: 0;
    }
</style>

</html>