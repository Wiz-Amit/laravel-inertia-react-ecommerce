<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
</head>
<body>
    <h1>Daily Sales Report - {{ $salesData['date'] }}</h1>
    
    <h2>Summary</h2>
    <ul>
        <li><strong>Total Products Sold:</strong> {{ $salesData['total_products_sold'] }}</li>
        <li><strong>Total Revenue:</strong> ${{ number_format($salesData['total_revenue'], 2) }}</li>
    </ul>
    
    @if(count($salesData['top_products']) > 0)
    <h2>Top Products</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity Sold</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData['top_products'] as $item)
            <tr>
                <td>{{ $item['product']->name }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>${{ number_format($item['revenue'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No sales recorded for this date.</p>
    @endif
</body>
</html>




