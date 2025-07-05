<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>
<body class="bg-[#379777]">
    <div class="navbar-container w-full h-[70px] bg-[#FFDE95] drop-shadow-sm flex justify-center fixed">
        <div class="navbar-wrapper w-[1838px] flex items-center">
            <p class="text-5xl font-black">MUNING PHOTO</p>
        </div>
    </div>
    <div class="transaction-container w-full h-screen flex justify-center">
        <div class="transaction-wrapper w-[1838px] flex flex-row gap-7">
            <div class="left-transaction w-[1263px] h-[751px] mt-[129px] mb-[60px] rounded-[10px] bg-[#F5F7F8]">
                <table id="example" class="" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>QTY</th>
                            <th>Price</th>
                            <th>Information</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td>FRAME 12Rs GOLD LINEN WHITE</td>
                            <td>
                                <button><img src="{{ asset('assets/MINUS.png') }}" alt="DeleteButton"></button>
                                1
                                <button><img src="{{ asset('assets/PLUS.png') }}" alt="DeleteButton"></button>
                            </td>
                            <td>Rp. 350.000</td>
                            <td></td>
                            <td><img src="{{ asset('assets/DELETE.png') }}" alt="DeleteButton" class="w-[58px] h-[38px] mt-2"></td>
                        </tr>
                        <tr>
                            <td>FRAME 12Rs GOLD LINEN WHITE</td>
                            <td>
                                <button><img src="{{ asset('assets/MINUS.png') }}" alt="DeleteButton"></button>
                                1
                                <button><img src="{{ asset('assets/PLUS.png') }}" alt="DeleteButton"></button>
                            </td>
                            <td>Rp. 350.000</td>
                            <td></td>
                            <td><img src="{{ asset('assets/DELETE.png') }}" alt="DeleteButton" class="w-[58px] h-[38px] mt-2"></td>
                        </tr> --}}
                    </tbody>
                </table>
              
                <div class="payment-transaction w-[1263px] h-[170px] bg-[#45474B] mt-5 flex justify-center items-center">
                    <form action="">
                         <div class="payment-transaction-form grid grid-cols-3 grid-rows-2 gap-3">
                            <input type="text" name="" id="" placeholder="Discount" class="w-[363px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                            <input type="text" name="" id="" placeholder="Pay" class="w-[363px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                            <input type="text" name="" id="" placeholder="Return" class="w-[363px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                            <input type="text" name="" id="" placeholder="Subtotal" class="w-[363px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                            <br>
                            <input type="text" name="" id="" placeholder="Deficiency" class="w-[363px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    </div>
                    </form>
                   
                </div>
            </div>
            <div class="right-transaction w-[542px] h-[700px] mt-[129px] mb-[60px] rounded-[10px] bg-[#F5F7F8]">
                <p class="text-2xl font-bold my-3 text-center">Customer Data</p>
                <form action="" class="flex flex-col items-center gap-3 border-b-2 border-black pb-6">
                    <input type="text" name="" id="" placeholder="Name" class="w-[475px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    <input type="text" name="" id="" placeholder="WhatsApp*" class="w-[475px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    <input type="text" name="" id="" placeholder="Email" class="w-[475px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    <div class="inline-flex gap-3 items-center">
                        <label for="enterOrder" class="text-start">Enter Order</label>
                        <input type="datetime-local" name="" id="enterOrder" placeholder="Enter Order" class="w-[380px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    </div>
                    <div class="inline-flex gap-9 items-center">
                        <label for="enterOrder" class="text-start">Finished</label>
                        <input type="datetime-local" name="" id="enterOrder" placeholder="Enter Order" class="w-[380px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    </div>
                </form>
                <p class="text-2xl font-bold my-3 text-center">Item Data</p>
                <form action="" class="flex flex-col items-center gap-3">
                    <div class="inline-flex gap-3 items-center">
                        <label for="enterOrder" class="text-start">Order Item</label>
                        <select id='sel_depart' name='sel_depart' class="w-[380px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                            <option value='0'>-- Select department --</option>
                            @foreach($departmentData['data'] as $department)
                                <option value='{{ $department->id }}'>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inline-flex gap-[57px] items-center">
                        <label for="enterOrder" class="text-start">Price</label>
                        <select id='sel_emp' name='sel_emp' class="w-[380px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                            {{-- <option value='0'>-- Select Employee --</option> --}}
                        </select>
                    </div>
                    <input type="text" name="" id="" placeholder="Discount" class="w-[475px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    <input type="text" name="" id="" placeholder="Keterangan" class="w-[475px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    <button class="text-white text-2xl font-light bg-[#C80036] w-[240px] h-[46px] my-3 rounded-xl">Add Item</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                processing: true,
                serverside: true,
                ajax:'{{ url()->current() }}',
                columns: [
                    {
                        data:'id',
                        name:'id'
                    },
                    {
                        data:'item_id',
                        name:'item_id'
                    },
                ]
            });
        });

        $(document).ready(function(){
     
          $('#sel_depart').change(function(){
             // Department id
             var id = $(this).val();
     
             // Empty the dropdown
             $('#sel_emp').find('option').not(':first').remove();
     
             // AJAX request 
             $.ajax({
               url: '/getEmployees/'+id,
               type: 'get',
               dataType: 'json',
               success: function(response){
                    var len = 0;
                    if(response['data'] != null){
                        len = response['data'].length;
                    }
     
                    if(len > 0){
                        // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var id = response['data'][i].id;
                            var name = response['data'][i].price;
                            var option = "<option value='"+id+"'>"+name+"</option>"; 
                            $("#sel_emp").append(option); 
                        }
                    }
               }
            });
          });
        });
    </script>
</body>
</html>