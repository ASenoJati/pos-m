@extends('v2.admin.template.app') @section('main')
<div class="card card-warning card-outline mb-4">
    <div class="card-header">
        <div class="card-title">Horizontal Form</div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <label for="name" class="col-sm-2 col-form-label">ITEM NAME</label>
            <div class="col-sm-10">
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    id="name"
                    value="{{ $itemdata->name }}"
                />
            </div>
        </div>
        <div class="row mb-3">
            <label for="price" class="col-sm-2 col-form-label">PRICE</label>
            <div class="col-sm-10">
                <input
                    type="number"
                    class="form-control"
                    id="price"
                    value="{{ $itemdata->price }}"
                    name="price"
                />
            </div>
        </div>
        <div class="row mb-3">
            <label for="type" class="col-sm-2 col-form-label">TYPE</label>
            <div class="col-sm-10">
                <input
                    type="number"
                    class="form-control"
                    id="type"
                    value="{{ $itemdata->type }}"
                    name="type"
                />
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button
            type="submit"
            id="updateItem"
            data-id="{{ $itemdata->id }}"
            class="btn btn-danger"
        >
            SAVE
        </button>
        <a href="{{ url('admin/itemData') }}" class="btn btn-warning float-end"
            >CANCEL</a
        >
    </div>
</div>
@endsection @push('js')
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
    $("#updateItem").click(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        var id = $(this).data("id");
        $.ajax({
            url: "/admin/saveUpdateItemData",
            method: "POST",
            data: {
                name: $("#name").val(),
                price: $("#price").val(),
                type: $("#type").val(),
                id: id,
            },
            success: function (result) {
                if (result.errors) {
                    console.log(result.errors);
                } else {
                    window.location.href = "{{ url('admin/itemData') }}";
                }
            },
        });
    });
</script>
@endpush
