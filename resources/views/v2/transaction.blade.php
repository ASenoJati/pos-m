@extends('v2.template.app') @push('css')
<link
    rel="stylesheet"
    href="{{ asset('vendor/datatable/dataTables.bootstrap5.css') }}"
/>
<link rel="stylesheet" href="{{ asset('dist/select2/css/select2.min.css') }}" />
<style>
    .navbar {
        display: none;
    }
</style>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert/sweetalert2@11.js') }}"></script>
@endpush @section('main')
<iframe
    src="/invoice/customer/{{ $track_id }}"
    frameborder="0"
    style="display: none"
    name="frame"
    id="iframe"
></iframe>
<div class="row">
    <div class="col-lg-8 my-2">
        <div class="card card-warning mb-2">
            <form action="" class="needs-validation">
                <div class="card-body m-0">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <img
                                src="{{
                                    asset('dist/assets/img/new/logo.png')
                                }}"
                                alt=""
                                width="200"
                                class="float-start"
                            />
                            <a
                                href="{{ url('/') }}"
                                class="btn btn-success float-start mx-2 mt-1"
                                >RETURN TO HOME</a
                            >
                            {{--
                            <button
                                class="btn btn-info float-end mx-1 mt-1"
                                onclick="window.frames['frame'].print();"
                                type="button"
                            >
                                SAVE INV
                            </button>
                            --}}
                            <button
                                class="btn btn-success float-end mx-1 mt-1"
                                id="payOrder"
                                type="submit"
                            >
                                PAY (F3)
                            </button>
                            <button
                                class="btn btn-danger float-end mx-1 mt-1"
                                id="cancelOrder"
                                type="submit"
                            >
                                CANCEL ORDER
                            </button>
                            <button
                                class="btn btn-warning float-end mx-1 mt-1"
                                id="holdOrder"
                                type="submit"
                            >
                                HOLD ORDER
                            </button>
                            @if ($customer_data->hold_order==1)
                            <button
                                class="btn btn-primary float-end mx-1 mt-1"
                                id="continueOrder"
                                type="submit"
                            >
                                CONTINUE ORDER
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div
            class="card"
            style="height: auto; width: auto; transition: all 0.15s ease 0s"
        >
            <div class="card-body p-0">
                <table
                    id="example"
                    class="table table-striped calculation-table"
                    style="width: 100%"
                >
                    <thead>
                        <tr>
                            <th>NAME</th>
                            <th>QTY</th>
                            <th>PRICE</th>
                            <th>DISCOUNT</th>
                            <th>TOTAL</th>
                            <th>INFORMATION</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="memberBody"></tbody>
                </table>
            </div>
        </div>

        <div class="card my-2">
            <form action="" class="needs-validation">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="subtotal" class="form-label"
                                >SUBTOTAL</label
                            >
                            <input
                                type="number"
                                class="form-control"
                                id="subtotal"
                                disabled
                            />
                        </div>
                        <div class="col-md-6">
                            <label for="return" class="form-label"
                                >KEMBALI</label
                            >
                            <input
                                type="number"
                                class="form-control"
                                id="return"
                            />
                        </div>
                        <div class="col-md-6">
                            <label for="payment" class="form-label"
                                >BAYAR</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="payment"
                                value="{{ $customer_data->pay=='' ? '0' : $customer_data->pay }}"
                            />
                        </div>
                        <div class="col-md-6">
                            <label for="deficiency" class="form-label"
                                >KEKURANGAN</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="deficiency"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4 mt-2">
        <div class="card card-warning">
            <form action="" class="needs-validation">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="enter_order" class="form-label"
                                >ENTER ORDER</label
                            >
                            <input
                                type="datetime-local"
                                class="form-control"
                                id="enter_order"
                                value="{{ $customer_data->enter_order=='' ? date('Y-m-d H:i:s') : $customer_data->enter_order }}"
                            />
                        </div>
                        <div class="col-md-6">
                            <label for="finished_order" class="form-label"
                                >FINISHED ORDER</label
                            >
                            <input
                                type="datetime-local"
                                class="form-control"
                                id="finished_order"
                                value="{{ $customer_data->finished_order=='' ? date('Y-m-d H:i:s') : $customer_data->finished_order }}"
                            />
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">NAME</label>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                aria-describedby="inputGroupPrepend"
                                oninput="this.value = this.value.toUpperCase()"
                                value="{{ $customer_data->name }}"
                            />
                        </div>
                        <div class="col-md-6">
                            <label for="whatsapp" class="form-label"
                                >WHATSAPP</label
                            >
                            <input
                                type="number"
                                class="form-control"
                                id="whatsapp"
                                value="{{ $customer_data->whatsapp=='' ? '62' : $customer_data->whatsapp }}"
                            />
                        </div>
                        <div class="col-md-12">
                            <label for="email" class="form-label">EMAIL</label>
                            <input
                                type="text"
                                class="form-control"
                                id="email"
                                aria-describedby="inputGroupPrepend"
                                value="{{ $customer_data->email }}"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card my-2">
            <input
                type="hidden"
                name="customer_id"
                id="customer_id"
                value="{{ $customer_id }}"
            />
            <input
                type="hidden"
                name="track_id"
                id="track_id"
                value="{{ $track_id }}"
            />
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="sel_depart" class="form-label">ITEM</label>
                        <select
                            class="form-control select2"
                            id="sel_depart"
                            name="sel_depart"
                            style="width: 100%"
                        >
                            <option value="0">Select Item</option>
                            @foreach($getItemAll['data'] as $itemRow)
                            <option value="{{ $itemRow->id }}">
                                {{ $itemRow->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="sel_emp" class="form-label">PRICE</label>
                        <input
                            type="text"
                            class="form-control"
                            id="price3"
                            name="price3"
                            aria-describedby="inputGroupPrepend"
                            disabled
                        />
                        {{--
                        <select
                            class="form-select"
                            id="sel_emp"
                            name="sel_emp"
                            data-placeholder="Choose one thing"
                        >
                            <option value="" id="fselect">PRICE</option>
                        </select>
                        --}}
                    </div>
                    <div class="col-md-6">
                        <label for="qty" class="form-label">QTY</label>
                        <input
                            type="number"
                            value="1"
                            class="form-control"
                            id="qty"
                            name="qty"
                            aria-describedby="inputGroupPrepend"
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="discount" class="form-label"
                            >DISCOUNT</label
                        >
                        <input
                            type="text"
                            class="form-control"
                            value="0"
                            id="discount"
                            name="discount"
                            aria-describedby="inputGroupPrepend"
                        />
                    </div>

                    <input type="hidden" value="0" id="price2" name="price2" />

                    {{--
                    <div class="col-md-6">
                        <label for="discount" class="form-label"
                            >DISCOUNT2</label
                        >
                        <input
                            type="text"
                            class="form-control"
                            id="price3"
                            name="price3"
                            aria-describedby="inputGroupPrepend"
                            disabled
                        />
                    </div>
                    --}}
                    <div class="col-md-12">
                        <label for="note" class="form-label">INFORMATION</label>
                        <input
                            type="text"
                            class="form-control"
                            name="note"
                            id="note"
                        />
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-info" type="submit" id="addItem">
                    ADD ITEM (F2)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection @push('js')

<script src="{{ asset('dist/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('vendor/hotkey/hotkeys.min.js') }}"></script>
<script>
    // SELECT 2
    $(function () {
        $(".select2").select2();
    });

    // CALCULATE
    $(document).ready(function () {
        sum();
        $("#subtotal, #return, #payment, #deficiency").on(
            "keydown keyup",
            function () {
                sum();
            }
        );
        itemSys();
        $("#discount, #qty, #price2, #price3").on("keydown keyup", function () {
            itemSys();
        });
    });

    

    function sum() {
        var subtotal = document.getElementById("subtotal").value;
        var return11 = document.getElementById("return").value;
        var payment = document.getElementById("payment").value;
        var deficiency = document.getElementById("deficiency").value;
        var bilang = 1000;

        var result = parseInt(payment) - parseInt(subtotal);
        document.getElementById("return").value = 0;

        var result1 = parseInt(subtotal) - parseInt(payment);
        document.getElementById("deficiency").value = result1;

        if (result < 0) {
            document.getElementById("return").value = 0;
        } else {
            document.getElementById("return").value = result;
        }

        if (result1 < 0) {
            document.getElementById("deficiency").value = 0;
        } else {
            document.getElementById("deficiency").value = result1;
        }
    }

    function itemSys() {
        var disc = document.getElementById("discount").value;
        var qty1 = document.getElementById("qty").value;
        var price2 = document.getElementById("price2").value;
        let num = price2 * qty1 - disc;
                            let rupiahFormat = new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            }).format(num);
        document.getElementById("price3").value = rupiahFormat;
    }

    $(document).ready(function () {
        // GET SUBTOTAL
        setInterval(() => {
            document.getElementById("subtotal").value =
                localStorage.getItem("pay");
            sum();
            itemSys();
        }, 100);

        // ITEM SELECT
        $("#sel_depart").change(function () {
            var id = $(this).val();
            $("#fselect").remove();
            $("#price2").val("0");
            $("#qty").val("1");
            $("#discount").val("0");
            $.ajax({
                url: "/getEmployees/" + id,
                type: "get",
                dataType: "json",
                success: function itemok(response) {
                    var len = 0;
                    if (response["data"] != null) {
                        len = response["data"].length;
                    }

                    if (len > 0) {
                        // Read data and create <option >
                        for (var i = 0; i < len; i++) {
                            var id = response["data"][i].id;
                            var name = response["data"][i].price;
                            // var option =
                            //     "<option value='" +
                            //     id +
                            //     "' id='fselect'>" +
                            //     name +
                            //     "</option>";
                            // $("#sel_emp").append(option);
                            
                            var qtyItem = (document.getElementById(
                                "price2"
                            ).value = name);
                        }
                    }
                },
            });
        });
    });

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        showMember();

        hotkeys("f2", function (event, handler) {
            event.preventDefault();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            var info = $("#note").val();
            if (info == "") {
                var information = "-";
            } else {
                var information = $("#note").val();
            }

            $.ajax({
                url: "{{ url('/transaction/additem') }}",
                method: "POST",
                data: {
                    customer_id: $("#customer_id").val(),
                    track_id: $("#track_id").val(),
                    item_id: $("#sel_depart").val(),
                    qty: $("#qty").val(),
                    note: information,
                    discount: $("#discount").val(),
                },
                success: function (result) {
                    if (result.errors) {
                        console.log(result.errors);
                    } else {
                        showMember();
                    }
                },
            });
        });

        hotkeys("f3", function (event, handler) {
            event.preventDefault();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            var pay = $("#payment").val();
            if (pay == "") {
                var paymentcst = "0";
            } else {
                var paymentcst = $("#payment").val();
            }

            var email = $("#email").val();
            if (email == "") {
                var emailcst = "-";
            } else {
                var emailcst = $("#email").val();
            }
            $.ajax({
                url: "/transaction/edit/{{ $track_id }}",
                method: "POST",
                data: {
                    name: $("#name").val(),
                    whatsapp: $("#whatsapp").val(),
                    email: emailcst,
                    subtotal: $("#subtotal").val(),
                    pay: paymentcst,
                    return_pay: $("#return").val(),
                    deficiency: $("#deficiency").val(),
                    hold_order: "0",
                    enter_order: $("#enter_order").val(),
                    finished_order: $("#finished_order").val(),
                },
                success: function (result) {
                    if (result.errors) {
                        console.log(result.errors);
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "Payment Success",
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                },
            });
        });

        $("#addItem").click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            var info = $("#note").val();
            if (info == "") {
                var information = "-";
            } else {
                var information = $("#note").val();
            }
            $.ajax({
                url: "{{ url('/transaction/additem') }}",
                method: "POST",
                data: {
                    customer_id: $("#customer_id").val(),
                    track_id: $("#track_id").val(),
                    item_id: $("#sel_depart").val(),
                    qty: $("#qty").val(),
                    note: information,
                    discount: $("#discount").val(),
                },
                success: function (result) {
                    if (result.errors) {
                        console.log(result.errors);
                    } else {
                        showMember();
                    }
                },
            });
        });

        $("#payOrder").click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            var pay = $("#payment").val();
            if (pay == "") {
                var paymentcst = "0";
            } else {
                var paymentcst = $("#payment").val();
            }

            var email = $("#email").val();
            if (email == "") {
                var emailcst = "-";
            } else {
                var emailcst = $("#email").val();
            }

            $.ajax({
                url: "/transaction/edit/{{ $track_id }}",
                method: "POST",
                data: {
                    name: $("#name").val(),
                    whatsapp: $("#whatsapp").val(),
                    email: emailcst,
                    subtotal: $("#subtotal").val(),
                    pay: paymentcst,
                    return_pay: $("#return").val(),
                    deficiency: $("#deficiency").val(),
                    hold_order: "0",
                    enter_order: $("#enter_order").val(),
                    finished_order: $("#finished_order").val(),
                },
                success: function (result) {
                    if (result.errors) {
                        console.log(result.errors);
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "Payment Success",
                            showConfirmButton: false,
                            timer: 1500,
                        });
                        reloadIFrame();
                        window.setInterval(function () {
                            window.frames["frame"].print();
                            window.location.href = "/";
                        }, 2000);
                    }
                },
            });
        });

        function reloadIFrame() {
            console.log("reloading..");
            document.getElementById("iframe").contentWindow.location.reload();
        }

        $("#cancelOrder").click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            Swal.fire({
                title: "APAKAH YAKIN ANDA MENG-CANCEL ORDERAN INI!",
                //   text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: "RASIDOO",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "YA, HAPUS ORDERAN INI!",
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/transaction/cancel/{{ $track_id }}",
                        method: "POST",
                        success: function (result) {
                            if (result.errors) {
                                console.log(result.errors);
                                Swal.fire(result.errors, "", "error");
                            } else {
                                Swal.fire(
                                    "Changes are not saved",
                                    "",
                                    "success"
                                );
                                setTimeout("location.href = '/';", 2000);
                            }
                        },
                    });
                }
            });
        });

        $("#holdOrder").click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "/transaction/edit/{{ $track_id }}",
                method: "POST",
                data: {
                    hold_order: "1",
                },
                success: function (result) {
                    if (result.errors) {
                        console.log(result.errors);
                    } else {
                        window.location.href = "/";
                    }
                },
            });
        });

        $("#continueOrder").click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "/transaction/edit/{{ $track_id }}",
                method: "POST",
                data: {
                    hold_order: "0",
                },
                success: function (result) {
                    if (result.errors) {
                        console.log(result.errors);
                    } else {
                        window.location.href = "/";
                    }
                },
            });
        });
    });

    function showMember() {
        var track_id = $("#track_id").val();
        $.get("/customer/item/{{ $track_id }}", function (data) {
            $("#memberBody").empty().html(data);
        });
    }
</script>
@endpush
