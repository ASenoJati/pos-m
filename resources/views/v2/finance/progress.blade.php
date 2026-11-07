@extends('v2.finance.template.app')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
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
                <div class="row my-3 mx-2">
                    <div class="col-md-2">
                        <button class="btn btn-primary filter" id="filter" type="submit">FILTER</button>
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
                                <th>TIME</th>
                                <th>PROGRESS</th>
                                <th>PAID STATUS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card-footer">
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script>
        $(document).ready(function() {
        $('input[name="daterange"]').daterangepicker({
            startDate: moment(),
            endDate: moment()
        });

        var csttable = $("#example").DataTable({
            ordering: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('finance/progress/table') }}",
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
                    data: "time",
                    name: "time",
                },
                {
                    data: "progress",
                    name: "progress",

                },
                {
                    data: "paidStatus",
                    name: "paidStatus",

                },
            ],
        });

        $(".filter").click(function() {
            console.log($('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'))
            csttable.draw();
        });
    });
    </script>
@endpush