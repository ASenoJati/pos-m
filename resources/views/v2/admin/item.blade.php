@extends('v2.admin.template.app') @section('main')

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/datatable/dataTables.bootstrap5.css') }}">
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
@endpush

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-body p-0">
                <div class="table-responsive mailbox-messages my-3 mx-1">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ITEM NAME</th>
                                <th>PRICE</th>
                                <th>TYPE</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('vendor/datatable/dataTables.js') }}"></script>
<script src="{{ asset('vendor/datatable/dataTables.bootstrap5.js') }}"></script>
<script>
    // new DataTable('#example');
    $(document).ready(function() {

        var itemtable = $("#example").DataTable({
            ordering: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('admin/itemData/table') }}",
            },
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    width: "10px",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "name",
                    name: "name",
                },
                {
                    data: "price",
                    name: "price",
                    
                },
                {
                    data: "type",
                    name: "type",
                    
                },
                {
                    data: "action",
                    name: "action",
                    width:"390px"
                },
            ],
        });

    });
</script>
@endpush