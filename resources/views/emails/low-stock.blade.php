@component('mail::message')
# Low Stock Alert

Hello {{ $notifiable->name }},

This is an automated notification to inform you that the following product is running low on stock:

@component('mail::panel')
**Product Name:** {{ $product->name }}
**SKU:** {{ $product->SKU }}
**Current Stock:** {{ $product->stock }} {{ $product->unit_name }}
**Minimum Stock Level:** {{ $minimumStock }} {{ $product->unit_name }}
@if($product->category)
**Category:** {{ $product->category->name }}
@endif
@endcomponent

Please take action to reorder this product to maintain adequate inventory levels.

@component('mail::button', ['url' => config('app.url') . '/admin/products/' . $product->id . '/edit'])
View Product
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
