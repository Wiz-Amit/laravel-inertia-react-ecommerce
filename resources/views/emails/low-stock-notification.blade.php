@component('mail::message')
# Low Stock Alert

The following product is running low on stock:

@component('mail::table')
| Product | Current Stock | Price |
|:--------|:--------------|:------|
| {{ $product->name }} | {{ $stockQuantity }} units | ${{ number_format($product->price, 2) }} |
@endcomponent

Please consider restocking this product soon.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
