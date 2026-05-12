<h2>GSTR-1 Report</h2>

<p><strong>Business:</strong> {{ $business->business_name ?? '' }}</p>
<p><strong>Mobile:</strong> {{ $business->mobile ?? '' }}</p>
<p><strong>GST:</strong> {{ $business->gst_number ?? '' }}</p>

<p><strong>Date:</strong> {{ $start }} to {{ $end }}</p>

<table border="1" width="100%" cellpadding="5">
    <tr>
        <th>Customer</th>
        <th>Invoice</th>
        <th>Date</th>
        <th>Value</th>
        <th>Taxable</th>
        <th>GST %</th>
        <th>CGST</th>
        <th>SGST</th>
        <th>Total GST</th>
    </tr>

    @foreach($invoices as $inv)
    <tr>
        <td>{{ $inv->party->party_name ?? '' }}</td>
        <td>{{ $inv->invoice_number }}</td>
        <td>{{ $inv->invoice_date }}</td>
        <td>{{ $inv->grand_total }}</td>
        <td>{{ $inv->subtotal }}</td>
        <td>
            {{ $inv->subtotal > 0 ? round(($inv->total_tax / $inv->subtotal)*100,2) : 0 }}%
        </td>
        <td>{{ $inv->total_tax/2 }}</td>
        <td>{{ $inv->total_tax/2 }}</td>
        <td>{{ $inv->total_tax }}</td>
    </tr>
    @endforeach
</table>