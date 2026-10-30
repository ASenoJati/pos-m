<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
                <table width="100%">
                    <thead class="bg-[#45474B] h-[46px]">
                        <tr class="text-white text-[20px]">
                            <th class="text-start px-5">ID</th>
                            <th class="text-start">Customer Name</th>
                            <th class="text-start">Date & Time</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b-[1px] border-black">
                            <td class="px-5 py-2">1279789</td>
                            <td>Lightning mcqueen</td>
                            <td>24 Jun 24, 19:20</td>
                            <td class="text-center">
                                <button><img src="{{ asset('assets/EDIT.png') }}" class="mt-2"></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="right-transaction w-[542px] h-[700px] mt-[129px] mb-[60px] rounded-[10px] bg-[#F5F7F8]">
                <p class="text-2xl font-bold my-3 text-center">Customer Data</p>
                <form action="" class="flex flex-col items-center gap-3">
                    <input type="text" name="" id="" placeholder="ID Order" class="w-[475px] h-[40px] bg-[#FFF5E1] rounded-md px-4 border-[2px] border-[#FF6969] outline-none">
                    <button class="text-white text-2xl font-light bg-[#C80036] w-[240px] h-[46px] my-3 rounded-xl">Edit Order</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>