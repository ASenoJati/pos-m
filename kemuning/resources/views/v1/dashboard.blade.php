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
    <div class="dashboard-container w-full h-screen flex justify-center">
        <div class="dashboard-wrapper w-[1838px] mt-[129px] mb-[60px] rounded-[10px] bg-[#F5F7F8] flex justify-center">
            <div class="dashboard-box w-[1457px] h-full flex flex-row justify-center items-center">
                <div class="dashboard-left w-1/2">
                    <p class="text-3xl">Senin, 24 Juni 2024</p>
                    <p class="text-2xl">19:24</p>
                    <div class="recent-customer grid grid-rows-4 my-5 gap-3">
                        <div class="card-customer w-[427px] h-[80px] bg-[#45474B] inline-flex rounded-xl">
                            <img src="{{ asset('assets/MALEUSER.png') }}" class="w-[42px] h-[42px] mx-6 my-4">
                            <div class="customer">
                                <p class="text-white text-3xl">Lightning mcqueen</p>
                                <p class="text-white text-2xl">19:20</p>
                            </div>
                        </div>
                        <div class="card-customer w-[427px] h-[80px] bg-[#45474B] inline-flex rounded-xl">
                            <img src="{{ asset('assets/MALEUSER.png') }}" class="w-[42px] h-[42px] mx-6 my-4">
                            <div class="customer">
                                <p class="text-white text-3xl">Hudson hornet</p>
                                <p class="text-white text-2xl">18:30</p>
                            </div>
                        </div>
                        <div class="card-customer w-[427px] h-[80px] bg-[#45474B] inline-flex rounded-xl">
                            <img src="{{ asset('assets/MALEUSER.png') }}" class="w-[42px] h-[42px] mx-6 my-4">
                            <div class="customer">
                                <p class="text-white text-3xl">Tow mater</p>
                                <p class="text-white text-2xl">17:45</p>
                            </div>
                        </div>
                        <div class="card-customer w-[427px] h-[80px] bg-[#45474B] inline-flex rounded-xl">
                            <img src="{{ asset('assets/MALEUSER.png') }}" class="w-[42px] h-[42px] mx-6 my-4">
                            <div class="customer">
                                <p class="text-white text-3xl">Cruz ramirez</p>
                                <p class="text-white text-2xl">16:20</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-right w-1/2 flex justify-end">
                    <div class="navigation-button grid grid-cols-2 grid-rows-2 gap-4">
                        <form action="{{ url('dashboard') }}" method="POST">
                            @csrf
                            <button type="submit" class="button-nav w-[150px] h-[150px] bg-[#F4CE14] rounded-xl mx-2">
                                <img src="{{ asset('assets/DASHBOARD.png') }}" alt="DashboardIcon" class="m-auto py-6">
                            </button>
                            <p class="text-center mt-1 font-semibold">Dashboard</p>
                        </form>
                        
                        <a href="">
                            <div class="button-nav w-[150px] h-[150px] bg-[#F4CE14] rounded-xl">
                                <img src="{{ asset('assets/PAYPAL.png') }}" alt="DashboardIcon" class="m-auto py-6">
                            </div>
                            <p class="text-center mt-1 font-semibold">Transaction</p>
                        </a>
                        
                        <a href="">
                            <div class="button-nav w-[150px] h-[150px] bg-[#F4CE14] rounded-xl mx-2">
                                <img src="{{ asset('assets/EDITFILE.png') }}" alt="DashboardIcon" class="m-auto py-6">
                            </div>
                            <p class="text-center mt-1 font-semibold">Edit Order</p>
                        </a>

                        <a href="">
                            <div class="button-nav w-[150px] h-[150px] bg-[#F4CE14] rounded-xl">
                                <img src="{{ asset('assets/PAGE.png') }}" alt="DashboardIcon" class="m-auto py-6">
                            </div>
                            <p class="text-center mt-1 font-semibold">Order List</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>