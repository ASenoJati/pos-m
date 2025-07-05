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
    <div class="login-container w-full h-screen flex justify-center">
        <div class="login-wrapper w-[1838px] mt-[129px] mb-[60px] rounded-[10px] bg-[#F5F7F8]">
            <div class="login-box w-full h-full flex flex-row justify-center items-center">
                <div class="login-left w-1/2">
                    <img src="{{ asset('assets/MCQUEEN.png') }}" alt="" class="w-[829px] h-[518px]">
                </div>
                <div class="login-right w-1/2">
                    {{-- <img src="{{ asset('assets/HORNET.png') }}" alt="" class="w-[469px] h-[352px]"> --}}
                    <p class="text-3xl font-semibold">Please log in using your account</p>
                    <form action="" class="py-5 gap-3 flex flex-col">
                        <input type="text" name="" id="" class="w-[679px] h-[56.79px] bg-[#FFF5E1] rounded-[5px] outline-none border-[#FF6969] py-3 px-5 text-black" placeholder="Username">
                        <input type="text" name="" id="" class="w-[679px] h-[56.79px] bg-[#FFF5E1] rounded-[5px] outline-none border-[#FF6969] py-3 px-5 text-black mt-4" placeholder="Password">
                        <button class="text-white font-semibold bg-[#C80036] w-[324px] h-[68px] rounded-xl my-5">LOGIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>