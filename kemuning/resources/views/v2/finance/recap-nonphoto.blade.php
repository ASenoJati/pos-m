@extends('v2.finance.template.app') 

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
@endpush

@section('main')
<div class="card card-primary collapsed-card mb-4">
    <div class="card-header">
        <h3 class="card-title">FILTER DATE</h3>
        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> </div> <!-- /.card-tools -->
    </div> <!-- /.card-header -->
    <div class="card-body" style="display: none; box-sizing: border-box;">
            <form action="{{ url('finance/recap/nonPhoto') }}" method="POST">
                @csrf
                <div class="row my-3 mx-2">
                    <div class="col-md-12">
                        <input type="date" name="daterange" class="form-control" />
                    </div>
                </div>
                <div class="row my-3 mx-2">
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="submit">FILTER</button>
                    </div>
                </div>
            </form>
    </div>
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">RECAPITULATION</h3>
    </div>
    <div class="card-body">
        <table class="table table-striped" id="example" style="width:100%">
            <thead>
                <tr>
                    <th width="100">CS ID</th>
                    <th>CUSTOMER ORDER</th>
                    <th width="60" class="text-center">QTY</th>
                    <th>PRICE</th>
                    <th>TIME</th>
                </tr>
            </thead>
            <tbody>
                @php $totalRecap=0 @endphp 
                @foreach ($nonPhotoOrder as $item)
                <tr>
                    <td>{{ $item->customer_id }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td>@currency($item->price*$item->qty-$item->discount)</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
                @php $totalRecap+=$item->price*$item->qty-$item->discount @endphp 
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <h4 class="mt-1 float-start">TOTAL</h4>
        <h4 class="mt-1 float-end fw-bold">@currency($totalRecap)</h4>
    </div>
</div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#example');
    </script>
@endpush
