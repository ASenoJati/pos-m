@extends('v2.admin.template.app') 

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/datatable/dataTables.bootstrap5.css') }}">
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary collapsed-card mb-4">
            <div class="card-header">
                <h3 class="card-title">FILTER DATE</h3>
                <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> </div> <!-- /.card-tools -->
            </div>
            <div class="card-body" style="display: none; box-sizing: border-box;">
                <div class="row my-3 mx-2">
                    <div class="col-md-12">
                        <input type="text" name="daterange" value="" class="form-control" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-primary card-outline">
            <div class="card-body p-0">
                <div class="table-responsive mailbox-messages my-3 mx-1">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>CUSTOMER NAME</th>
                                <th>PROGRESS</th>
                                <th>TIME</th>
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
        $('input[name="daterange"]').daterangepicker({
            startDate: moment(),
            endDate: moment()
        });

        var csttable = $("#example").DataTable({
            ordering: true,
            serverSide: true,
            // processing: true,
            ajax: {
                url: "{{ url('admin/customerData/table') }}",
                data: function(d) {
                    d.from_date = $('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.to_date = $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
            },
            columns: [{
                    data: "id",
                    name: "id",
                    width: "10px",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "name",
                    name: "name",
                },
                {
                    data: "progress",
                    name: "progress",
                },
                {
                    data: "time",
                    name: "time",
                },
                {
                    data: "action",
                    name: "action",
                },
            ],
        });

        setInterval(() => {
            csttable.draw()
        }, 3000);

        $(".filter").click(function() {
            console.log($('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'))
            csttable.draw();
        });

        $('body').on('click', '.finishOrder', function() {
            var track_id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "/transaction/edit" + '/' + track_id,
                data: {
                    progress: "1",
                },
                success: function(data) {
                    csttable.draw();
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });

        $('body').on('click', '.processOrder', function() {
            var track_id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "/transaction/edit" + '/' + track_id,
                data: {
                    progress: "2",
                },
                success: function(data) {
                    csttable.draw();
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });

        $('body').on('click', '.waitingByCstOrder', function() {
            var track_id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "/transaction/edit" + '/' + track_id,
                data: {
                    progress: "3",
                },
                success: function(data) {
                    csttable.draw();
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });

        $('body').on('click', '.viewCstOrder', function() {
            var track_id = $(this).data("id");
            window.open("/admin/orderDetail/" + track_id, "socialPopupWindow", "location=no,width=900,height=900,scrollbars=yes,top=100,left=700,resizable = no");
        });

    });
</script>
@endpush