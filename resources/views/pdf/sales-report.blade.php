<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-box {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .summary-box h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        .summary-stats {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        .stat {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
        <p>Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="summary-box">
        <h2>Sales Summary</h2>
        <div class="summary-stats">
            <div class="stat">
                <div class="stat-value">Rp {{ number_format($total_sales, 0, ',', '.') }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="stat">
                <div class="stat-value">{{ number_format($transaction_count) }}</div>
                <div class="stat-label">Transactions</div>
            </div>
            <div class="stat">
                <div class="stat-value">Rp {{ number_format($average_transaction, 0, ',', '.') }}</div>
                <div class="stat-label">Average Transaction</div>
            </div>
        </div>
    </div>

    <h2>Top Selling Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th class="text-right">Quantity Sold</th>
                <th class="text-right">Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_products as $product)
            <tr>
                <td>{{ $product['product'] }}</td>
                <td class="text-right">{{ number_format($product['quantity']) }}</td>
                <td class="text-right">Rp {{ number_format($product['total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Payment Methods Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="text-right">Transaction Count</th>
                <th class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payment_methods as $method => $data)
            <tr>
                <td>{{ ucfirst($method) }}</td>
                <td class="text-right">{{ number_format($data['count']) }}</td>
                <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No payment methods found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
