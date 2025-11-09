<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
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
        .section {
            margin: 30px 0;
        }
        .section h2 {
            font-size: 16px;
            color: #333;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .indent {
            padding-left: 20px;
        }
        .total-row {
            background: #f5f5f5;
            font-weight: bold;
        }
        .grand-total-row {
            background: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .positive {
            color: #10b981;
        }
        .negative {
            color: #ef4444;
        }
        .balance-sheet {
            display: table;
            width: 100%;
        }
        .balance-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
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
        <h1>Financial Report</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
        <p>Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="section">
        <h2>Profit & Loss Statement</h2>
        <table>
            <tr>
                <td><strong>Revenue</strong></td>
                <td class="text-right">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="indent">Cost of Goods Sold</td>
                <td class="text-right">(Rp {{ number_format($cogs, 0, ',', '.') }})</td>
            </tr>
            <tr class="total-row">
                <td><strong>Gross Profit</strong></td>
                <td class="text-right">Rp {{ number_format($gross_profit, 0, ',', '.') }} ({{ number_format($gross_profit_margin, 2) }}%)</td>
            </tr>
            <tr>
                <td><strong>Operating Expenses</strong></td>
                <td class="text-right">(Rp {{ number_format($operating_expenses, 0, ',', '.') }})</td>
            </tr>
            <tr class="grand-total-row">
                <td><strong>Net Profit</strong></td>
                <td class="text-right">Rp {{ number_format($net_profit, 0, ',', '.') }} ({{ number_format($net_profit_margin, 2) }}%)</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Balance Sheet</h2>
        <p style="text-align: center; color: #666;">As of {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>

        <table style="margin-top: 20px;">
            <tr>
                <td colspan="2" style="background: #2563eb; color: white; font-weight: bold; padding: 8px;">ASSETS</td>
            </tr>
            @foreach($assets as $asset)
            <tr>
                <td>{{ $asset['account'] }} <span style="color: #666; font-size: 9px;">({{ $asset['code'] }})</span></td>
                <td class="text-right">Rp {{ number_format($asset['balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>Total Assets</strong></td>
                <td class="text-right">Rp {{ number_format($total_assets, 0, ',', '.') }}</td>
            </tr>
        </table>

        <table style="margin-top: 20px;">
            <tr>
                <td colspan="2" style="background: #2563eb; color: white; font-weight: bold; padding: 8px;">LIABILITIES</td>
            </tr>
            @foreach($liabilities as $liability)
            <tr>
                <td>{{ $liability['account'] }} <span style="color: #666; font-size: 9px;">({{ $liability['code'] }})</span></td>
                <td class="text-right">Rp {{ number_format($liability['balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>Total Liabilities</strong></td>
                <td class="text-right">Rp {{ number_format($total_liabilities, 0, ',', '.') }}</td>
            </tr>
        </table>

        <table style="margin-top: 20px;">
            <tr>
                <td colspan="2" style="background: #2563eb; color: white; font-weight: bold; padding: 8px;">EQUITY</td>
            </tr>
            @foreach($equity as $eq)
            <tr>
                <td>{{ $eq['account'] }} <span style="color: #666; font-size: 9px;">({{ $eq['code'] }})</span></td>
                <td class="text-right">Rp {{ number_format($eq['balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td>Current Period Net Profit</td>
                <td class="text-right">Rp {{ number_format($net_profit, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Equity</strong></td>
                <td class="text-right">Rp {{ number_format($total_equity, 0, ',', '.') }}</td>
            </tr>
            <tr class="grand-total-row">
                <td><strong>Total Liabilities & Equity</strong></td>
                <td class="text-right">Rp {{ number_format($total_liabilities + $total_equity, 0, ',', '.') }}</td>
            </tr>
        </table>

        @if(abs($total_assets - ($total_liabilities + $total_equity)) < 0.01)
        <p style="margin-top: 20px; padding: 10px; background: #d1fae5; border: 1px solid #10b981; border-radius: 5px; text-align: center;">
            <strong style="color: #065f46;">Balance Sheet: BALANCED ✓</strong>
        </p>
        @else
        <p style="margin-top: 20px; padding: 10px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 5px; text-align: center;">
            <strong style="color: #991b1b;">Balance Sheet: OUT OF BALANCE ✗</strong>
        </p>
        @endif
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
