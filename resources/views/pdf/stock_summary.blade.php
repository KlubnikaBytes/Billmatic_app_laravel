<meta charset="UTF-8">

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
    }
</style>

<h2>Stock Summary Report</h2>
<p>Date: {{ $date }}</p>
<p>Total Stock Value: ₹ {{ $total }}</p>

<table border="1" width="100%" cellspacing="0" cellpadding="5">
    <tr>
        <th>Name</th>
        <th>Item Code</th>
        <th>Purchase Price</th>
        <th>Selling Price</th>
        <th>Stock Qty</th>
        <th>Stock Value</th>
    </tr>

    @foreach($items as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->item_code }}</td>
        <td>{{ $item->purchase_price }}</td>
        <td>{{ $item->sales_price }}</td>
        <td>{{ $item->opening_stock }} {{ $item->unit }}</td>
        <td>{{ $item->stock_value }}</td>
    </tr>
    @endforeach

        <!-- ✅ TOTAL ROW -->
    <tr>
        <td colspan="4"><strong>Total</strong></td>
        <td><strong>{{ $totalQty }}</strong></td>
        <td><strong>₹{{ $total }}</strong></td>
    </tr>
</table>