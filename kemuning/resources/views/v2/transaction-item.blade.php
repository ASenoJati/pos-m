@php $totalPayment=0 @endphp @foreach ($getCustomerItem as $item)
<tr>
    <td>{{ $item->item->name }}</td>
    <td>{{ $item->qty }}</td>
    <td>@currency($item->item->price)</td>
    <td>@currency($item->discount)</td>
    <td>@currency($item->item->price*$item->qty-$item->discount)</td>
    <td>{{ $item->note }}</td>
    <td>
        <a
            href="#"
            class="btn btn-danger delete"
            data-id="{{ $item->id }}"
            id="deleteItem{{ $item->id }}"
        >
        <i class="bi bi-trash3"></i></a
        >
    </td>
</tr>
@php $totalPayment+=$item->item->price*$item->qty-$item->discount @endphp
@endforeach
<tr>
    <td class="bg-info" colspan="7"><h4>TOTAL @currency($totalPayment)</h4></td>
</tr>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
    localStorage.setItem("pay", {{ $totalPayment }})
</script>
@foreach ($getCustomerItem as $item)
<script>
    $("#deleteItem{{ $item->id }}").click(function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/customer/itemdel/{{ $track_id }}/" + id,
            method: "DELETE",
            success: function (result) {
                if (result.errors) {
                    console.log(result.errors);
                } else {
                    console.log("ok");
                    showMember();
                }
            },
        });
    });
</script>
@endforeach