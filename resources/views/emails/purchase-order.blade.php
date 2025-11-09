@component('mail::message')
# New Purchase Order

Dear {{ $supplier->name }},

We would like to place a purchase order with your company. Please review the details below:

## Purchase Order Details

**PO Number:** {{ $purchaseOrder->po_number }}
**Order Date:** {{ $purchaseOrder->order_date->format('d M Y') }}
**Expected Delivery Date:** {{ $purchaseOrder->expected_delivery_date->format('d M Y') }}

---

## Items Ordered

@component('mail::table')
| Product | Quantity | Unit Price | Subtotal |
|:--------|:---------|:-----------|:---------|
@foreach($purchaseOrder->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} {{ $item->product->unit_name }} | Rp {{ number_format($item->unit_price, 0, ',', '.') }} | Rp {{ number_format($item->subtotal, 0, ',', '.') }} |
@endforeach
@endcomponent

---

## Order Summary

**Subtotal:** Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}
@if($purchaseOrder->tax_amount > 0)
**Tax:** Rp {{ number_format($purchaseOrder->tax_amount, 0, ',', '.') }}
@endif
@if($purchaseOrder->discount_amount > 0)
**Discount:** Rp {{ number_format($purchaseOrder->discount_amount, 0, ',', '.') }}
@endif
@if($purchaseOrder->shipping_cost > 0)
**Shipping Cost:** Rp {{ number_format($purchaseOrder->shipping_cost, 0, ',', '.') }}
@endif

@component('mail::panel')
### Total Amount: Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
@endcomponent

@if($purchaseOrder->notes)
**Additional Notes:**
{{ $purchaseOrder->notes }}
@endif

Please confirm receipt of this purchase order and provide an estimated delivery timeline.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
