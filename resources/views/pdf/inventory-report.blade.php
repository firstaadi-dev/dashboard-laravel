<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
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
        .summary-stats {
            display: table;
            width: 100%;
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
        .alert-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .alert-box h2 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #92400e;
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
            font-size: 10px;
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
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
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
        <h1>Inventory Report</h1>
        <p>Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-stats">
            <div class="stat">
                <div class="stat-value">{{ number_format($total_products) }}</div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat">
                <div class="stat-value">Rp {{ number_format($total_stock_value, 0, ',', '.') }}</div>
                <div class="stat-label">Stock Value</div>
            </div>
            <div class="stat">
                <div class="stat-value">{{ number_format($low_stock_count) }}</div>
                <div class="stat-label">Low Stock Items</div>
            </div>
            <div class="stat">
                <div class="stat-value">{{ number_format($out_of_stock_count) }}</div>
                <div class="stat-label">Out of Stock</div>
            </div>
        </div>
    </div>

    @if($low_stock_products->count() > 0)
    <div class="alert-box">
        <h2>Low Stock Products Alert</h2>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th class="text-right">Current Stock</th>
                    <th class="text-right">Min Stock</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($low_stock_products as $product)
                <tr>
                    <td>{{ $product->SKU }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($product->stock) }}</td>
                    <td class="text-right">{{ number_format($product->min_stock ?? 20) }}</td>
                    <td class="text-center">
                        @if($product->stock <= 0)
                            <span class="badge badge-danger">Out of Stock</span>
                        @else
                            <span class="badge badge-warning">Low Stock</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <h2>All Products Inventory</h2>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product Name</th>
                <th>Category</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->SKU }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category?->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($product->stock) }}</td>
                <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($product->stock * $product->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
