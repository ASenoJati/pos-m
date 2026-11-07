@extends('v2.admin.template.app')

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/datatable/dataTables.bootstrap5.css') }}">
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
@endpush

@section('main')
<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box text-bg-success"> <span class="info-box-icon"> <i class="bi bi-cart-check-fill"></i> </span>
            <div class="info-box-content"> <span class="info-box-text">FINISH</span> <span class="info-box-number">{{ $customerFinish }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: {{ $customerFinish }}%"></div>
                </div> <span class="progress-description">
                    Increase 2024 - {{ date('Y') }}
                </span>
            </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
    </div> <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box text-bg-primary"> <span class="info-box-icon"> <i class="bi bi-cpu"></i> </span>
            <div class="info-box-content"> <span class="info-box-text">ON PROCESS</span> <span class="info-box-number">{{ $customerOnProcess }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: {{ $customerOnProcess }}%"></div>
                </div> <span class="progress-description">
                    Increase 2024 - {{ date('Y') }}
                </span>
            </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
    </div> <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box text-bg-info"> <span class="info-box-icon"> <i class="bi bi-people-fill"></i> </span>
            <div class="info-box-content"> <span class="info-box-text">WAITING BY CUSTOMER</span> <span class="info-box-number">{{ $customerWaitingByCustomer }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: {{ $customerWaitingByCustomer }}%"></div>
                </div> <span class="progress-description">
                    Increase 2024 - {{ date('Y') }}
                </span>
            </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
    </div> <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box text-bg-danger"> <span class="info-box-icon"> <i class="bi bi-question-square-fill"></i> </span>
            <div class="info-box-content"> <span class="info-box-text">WAITING FOR QUEUE</span> <span class="info-box-number">{{ $customerWaitingForQueue }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: {{ $customerWaitingForQueue }}%"></div>
                </div> <span class="progress-description">
                    Increase 2024 - {{ date('Y') }}
                </span>
            </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
    </div> <!-- /.col -->
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">CUSTOMER NOT FINISHED</h3>
        {{-- <div class="card-tools">
            <ul class="pagination pagination-sm float-end">
                <li class="page-item"> <a class="page-link" href="#">«</a> </li>
                <li class="page-item"> <a class="page-link" href="#">1</a> </li>
                <li class="page-item"> <a class="page-link" href="#">2</a> </li>
                <li class="page-item"> <a class="page-link" href="#">3</a> </li>
                <li class="page-item"> <a class="page-link" href="#">»</a> </li>
            </ul>
        </div> --}}
    </div> <!-- /.card-header -->
    <div class="card-body">
        <table class="table" id="example">
            <thead>
                <tr>
                    <th>CUSTOMER NAME</th>
                    <th>PROGRESS</th>
                    <th>STATUS</th>
                    <th>TIME</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer as $item)
                <tr class="align-middle">
                    <td>{{ $item->name }}</td>
                    <td>@if ($item->hold_order==1)

                        @else @if ($item->progress==1)
                        <div class="progress mt-2" role="progressbar" aria-label="Info striped example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-striped bg-success" style="width: 100%; border-radius: 0.375rem;"></div>
                        </div>
                        @endif @if ($item->progress==2)
                        <div class="progress mt-2" role="progressbar" aria-label="Danger striped example" aria-valuenow="60" aria-valuemin="30" aria-valuemax="100">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 60%; border-radius: 0.375rem;"></div>
                        </div>
                        @endif @if ($item->progress==3)
                        <div class="progress mt-2" role="progressbar" aria-label="Danger striped example" aria-valuenow="90" aria-valuemin="30" aria-valuemax="100">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: 90%; border-radius: 0.375rem;"></div>
                        </div>
                        @endif @if ($item->progress==null)
                        <div class="progress mt-2" role="progressbar" aria-label="Danger striped example" aria-valuenow="30" aria-valuemin="30" aria-valuemax="100">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" style="width: 30%; border-radius: 0.375rem;"></div>
                        </div>
                        @endif @endif</td>
                    <td>
                        @if ($item->hold_order==1)
                <span class="badge text-bg-warning fs-5"> HOLD ORDER </span>
                @else @if ($item->progress==1)
                <span class="badge text-bg-success fs-5"> FINISH </span>
                @endif @if ($item->progress==2)
                <span class="badge text-bg-primary fs-5"> ON PROCESS </span>
                @endif @if ($item->progress==3)
                <span class="badge text-bg-info fs-5">
                    WAITING BY CUSTOMER
                </span>
                @endif @if ($item->progress==null)
                <span class="badge text-bg-danger fs-5">
                    WAITING FOR QUEUE
                </span>
                @endif @endif
                    </td>
                    <td>{{ $item->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> <!-- /.card-body -->
</div>
@endsection

@push('js')
<script src="{{ asset('vendor/datatable/dataTables.js') }}"></script>
<script src="{{ asset('vendor/datatable/dataTables.bootstrap5.js') }}"></script>
<script>
    new DataTable('#example');
</script>
@endpush