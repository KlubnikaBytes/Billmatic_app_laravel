<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #333;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .left {
            float: left;
        }

        .right {
            float: right;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        .box {
            border: 1px solid #ccc;
            padding: 8px;
            display: inline-block;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="title">Sales Summary</div>

<!-- HEADER -->
<div class="header">
    <div class="left">
        <strong>Business Name</strong><br>
        Phone no: 6205857707
    </div>

    <div class="right">
        <strong>Invoice Report</strong>

        <div class="box">
            <div>Date: {{ $start }} - {{ $end }}</div>
            <div><strong>Total Invoice Amount: ₹ {{ number_format($total, 2) }}</strong></div>
        </div>
    </div>

    <div class="clear"></div>
</div>

<hr>

<!-- TABLE -->
<table>
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Invoice Date</th>
            <th>Party Name</th>
            <th>Amount</th>
            <th>Remaining</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Invoice Link</th>
        </tr>
    </thead>

    <tbody>
        @foreach($invoices as $inv)
        <tr>
            <td>{{ $inv->invoice_number }}</td>
            <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d/m/Y') }}</td>
            <td>{{ $inv->party->party_name ?? 'Cash Sale' }}</td>
            <td>₹ {{ number_format($inv->grand_total, 2) }}</td>
            <td>₹ {{ number_format($inv->balance_amount, 2) }}</td>
            <td>
                {{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') : '-' }}
            </td>
            <td>{{ ucfirst($inv->status) }}</td>
            <td>{{ $inv->payment_link ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>