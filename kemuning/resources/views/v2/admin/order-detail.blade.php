@extends('v2.admin.template.app-sidebarn')

@section('main')
@if ($customerData->hold_order)
<div class="alert alert-warning bg-warning text-white text-center fs-3 fw-bold p-0" role="alert">
    HOLD ORDER
</div>
@endif
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Item Data</h3>
    </div> <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th style="width: 10px">QTY</th>
                    <th>NOTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($itemCustomer as $checkouts)
                <tr class="align-middle">
                    <td>{{ $checkouts->item->name }}</td>
                    <td>{{ $checkouts->qty }}</td>
                    <td>{{ $checkouts->note }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> <!-- /.card-body -->
</div>
<a href="https://web.whatsapp.com/send/?phone={{ $customerData->whatsapp }}" target='__blank'><img src="{{ asset('vendor/ChatOnWhatsAppButton/WhatsAppButtonGreenMedium.svg') }}" alt="" class="mb-4"></a>

<a href="{{ url('invoice/customer/'.$customerData->track.'') }}" class="btn btn-dark mb-4" target="__black">Print Invoice</a>
<div class="card mb-4 {{ $customerData->hold_order=='1' ? '' : 'card-primary' }}">
    <div class="card-header">
        <h3 class="card-title">Customer Data</h3>
    </div> <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table">
            <tbody>
                <tr>
                    <th>NAME</th>
                    <td>{{ $customerData->name }}</td>
                </tr>
                <tr>
                    <th>WHATSAPP</th>
                    <td>{{ $customerData->whatsapp }}</td>
                </tr>
                <tr>
                    <th>EMAIL</th>
                    <td>{{ $customerData->email }}</td>
                </tr>
                <tr>
                    <th>ENTER ORDER</th>
                    <td>{{ $customerData->enter_order }}</td>
                </tr>
                <tr>
                    <th>FINISH ORDER</th>
                    <td>{{ $customerData->finished_order }}</td>
                </tr>
                <tr>
                    <th>SUBTOTAL</th>
                    <td>@currency($customerData->subtotal)</td>
                </tr>
                <tr>
                    <th>PAY</th>
                    <td>@currency($customerData->pay)</td>
                </tr>
                <tr>
                    <th>RETURN</th>
                    <td>@currency($customerData->return_pay)</td>
                </tr>
                <tr>
                    <th>DEFICIENCY</th>
                    <td>@currency($customerData->deficiency)</td>
                </tr>
            </tbody>
        </table>
    </div> <!-- /.card-body -->
</div>
@endsection