@extends('v2.template.app') @push('js')
<link
    rel="stylesheet"
    href="{{ asset('vendor/datatable/dataTables.bootstrap5.css') }}"
/>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
@endpush @section('main')
<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table m-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Progress</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="results"></tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        <form action="{{ url('/') }}" method="POST">
            @csrf
            <button
                type="submit"
                class="btn btn-sm btn-primary float-start fs-5"
            >
                PLACE NEW ORDER
            </button>
        </form>

        <button
            type="button"
            class="btn btn-sm btn-secondary float-start mx-2 fs-5"
            data-bs-toggle="modal"
            data-bs-target="#exampleModal"
        >
            EDIT ORDER
        </button>

        <div
            class="modal fade"
            id="exampleModal"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ url('editorder') }}" method="post">
                        @csrf
                        <div class="modal-header">
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <input
                                type="number"
                                name="idOrder"
                                id="idOrder"
                                class="form-control"
                                placeholder="ID Order"
                            />
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-danger"
                                data-bs-dismiss="modal"
                            >
                                BATAL
                            </button>
                            <button type="submit" class="btn btn-primary">
                                EDIT ORDER
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @push('js')
<script src="{{ asset('vendor/datatable/dataTables.js') }}"></script>
<script src="{{ asset('vendor/datatable/dataTables.bootstrap5.js') }}"></script>
<script>
    new DataTable("#example");
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $.ajaxSetup({ cache: false });
        setInterval(function () {
            $("#results").load("{{ url('recent_customer') }}");
        }, 2000);
    });
</script>
@endpush
