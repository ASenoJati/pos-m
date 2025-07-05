<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>{{ $customer->id }} {{ $customer->name }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
{{-- 
  <link rel="stylesheet" href="./web/modern-normalize.css">
  <link rel="stylesheet" href="./web/web-base.css"> --}}
  <link rel="stylesheet" href="{{ asset('css/inv.css') }}">
  {{-- <script type="text/javascript" src="./web/scripts.js"></script> --}}
</head>
<body>

<div class="web-container">

  <div class="page-container">
    Page
    <span class="page"></span>
    of
    <span class="pages"></span>
  </div>
  
  <div class="logo-container">
    <img
      style="height: 30px"
      src="{{ asset('dist/assets/img/new/logo.png') }}"
    >
  </div>
  
  <table class="invoice-info-container">
    <tr>
      <td rowspan="2" class="client-name">
        {{ $customer->name }}
      </td>
      <td>
        MUNING Photo
      </td>
    </tr>
    <tr>
      <td>
        Jalan Raung 5 
      </td>
    </tr>
    <tr>
      <td>
        Tanggal Faktur / <i>Invoice Date</i>: <strong>{{ $invoiceDate }}</strong>
      </td>
      <td>
        Kota Kediri
      </td>
    </tr>
    <tr>
      <td>
        Nomor Faktur / <i>Invoice No</i>: <strong>{{ $customer->id }}</strong>
      </td>
      <td>
        082285000038
      </td>
    </tr>
  </table>
  
  
  <table class="line-items-container">
    <thead>
      <tr>
        <th class="heading-description">Item</th>
        <th class="heading-quantity">Qty</th>
        <th class="heading-price">Price</th>
        <th class="heading-price">Discount</th>
        <th class="heading-subtotal">Subtotal</th>
      </tr>
    </thead>
    <tbody>
     @foreach ($itemCustomer as $item)
     <tr>
      <td>{{ $item->item->name }}</td>
      <td>{{ $item->qty }}</td>
      <td class="right">@currency($item->item->price)</td>
      <td class="right">@currency($item->discount)</td>
      <td class="bold">@currency($item->item->price*$item->qty-$item->discount)</td>
    </tr>
     @endforeach
    </tbody>
  </table>
  
  
  <table class="line-items-container has-bottom-border">
    <thead>
      <tr>
        {{-- <th>Due By</th> --}}
        {{-- <th>Total Due</th> --}}
        <th width="100">Payment Info</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        {{-- <td class="large total">$105.00</td> --}}
        <td class="payment-info">
          <div class="large total">
            TOTAL : <strong>@currency($customer->subtotal)</strong>
          </div>
          <div class="large total">
            BAYAR / PAY : <strong>@currency($customer->pay)</strong>
          </div>
          <div class="large total">
            KEMBALI / CHANGE : <strong>@currency($customer->return_pay)</strong>
          </div>
          <div class="large total">
            KEKURANGAN / DEFICIENCY : <strong>@currency($customer->deficiency)</strong>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  
  <div class="footer">
    <div class="footer-info">
     <span>© 2024 IT Development Team MUNING Photo</span>
    </div>
    {{-- <div class="footer-thanks">
      <img src="https://github.com/anvilco/html-pdf-invoice-template/raw/main/img/heart.png" alt="heart">
      <span>Thank you!</span>
    </div> --}}
  </div>

</div>

</body></html>