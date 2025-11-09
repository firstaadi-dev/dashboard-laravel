@component('mail::message')
# Transaction Receipt

Dear {{ $transaction->customer_name }},

Thank you for your purchase! Your transaction has been completed successfully.

## Transaction Details

**Transaction Number:** {{ $transaction->transaction_number }}
**Transaction Date:** {{ $transaction->transaction_date->format('d M Y, H:i') }}
**Payment Method:** {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}

---

## Items Purchased

@component('mail::table')
| Product | Quantity | Price | Subtotal |
|:--------|:---------|:------|:---------|
@foreach($transaction->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} {{ $item->product->unit_name }} | Rp {{ number_format($item->unit_price, 0, ',', '.') }} | Rp {{ number_format($item->subtotal, 0, ',', '.') }} |
@endforeach
@endcomponent

---

@component('mail::panel')
### Total Amount: Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
@endcomponent

@if($transaction->notes)
**Notes:** {{ $transaction->notes }}
@endif

Thank you for shopping with us!

Best regards,<br>
{{ config('app.name') }}
@endcomponent
