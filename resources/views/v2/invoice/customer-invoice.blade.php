<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>{{ $customer->id }} {{ $customer->name }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
{{-- 
  <link rel="stylesheet" href="./web/modern-normalize.css">
  <link rel="stylesheet" href="./web/web-base.css"> --}}
  <link rel="stylesheet" href="{{ asset('css/inv.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  {{-- <script type="text/javascript" src="./web/scripts.js"></script> --}}
</head>
<body>

<div class="web-container">
<!-- Invoice -->
<div class="w-full ">
  <div class="sm:w-11/12 lg:w-3/4 mx-auto">
    <!-- Card -->
    <div class="flex flex-col p-4 sm:p-10 bg-white shadow-md rounded-xl dark:bg-neutral-800">
      <!-- Grid -->
      <div class="flex justify-between">
        <div>
          {{-- <svg class="size-10" width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 26V13C1 6.37258 6.37258 1 13 1C19.6274 1 25 6.37258 25 13C25 19.6274 19.6274 25 13 25H12" class="stroke-blue-600 dark:stroke-white" stroke="currentColor" stroke-width="2"/>
            <path d="M5 26V13.16C5 8.65336 8.58172 5 13 5C17.4183 5 21 8.65336 21 13.16C21 17.6666 17.4183 21.32 13 21.32H12" class="stroke-blue-600 dark:stroke-white" stroke="currentColor" stroke-width="2"/>
            <circle cx="13" cy="13.0214" r="5" fill="currentColor" class="fill-blue-600 dark:fill-white"/>
          </svg>

          <h1 class="mt-2 text-lg md:text-xl font-semibold text-blue-600 dark:text-white">Preline Inc.</h1> --}}
          <img
      style="height: 40px"
      src="{{ asset('dist/assets/img/new/logoblack.png') }}"
    >
        </div>
        <!-- Col -->

        <div class="text-end">
          <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-neutral-200">Invoice #</h2>
          <span class="mt-1 block text-gray-500 dark:text-neutral-500">{{ $customer->id }}</span>

          <address class="mt-4 not-italic text-gray-800 dark:text-neutral-200">
           Jalan Raung 5 Kota Kediri
          </address>
        </div>
        <!-- Col -->
      </div>
      <!-- End Grid -->

      <!-- Grid -->
      <div class="grid sm:grid-cols-2 gap-3">
        <div>
          <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">Bill to:</h3>
          <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">{{ $customer->name }}</h3>
          <address class="mt-2 not-italic text-gray-500 dark:text-neutral-500">
            {{ $customer->whatsapp=='62' ? '' : $customer->whatsapp }}<br>
            {{ $customer->email=='-' ? '' : $customer->email }}<br>
          </address>
        </div>
        <!-- Col -->

        <div class="text-end space-y-2">
          <!-- Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-1 gap-2">
            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Invoice date:</dt>
              <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $invoiceDate }}</dd>
            </dl>
            {{-- <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Due date:</dt>
              <dd class="col-span-2 text-gray-500 dark:text-neutral-500">03/11/2018</dd>
            </dl> --}}
          </div>
          <!-- End Grid -->
        </div>
        <!-- Col -->
      </div>
      <!-- End Grid -->

      <!-- Table -->
      <div class="">
        <div class="border border-gray-200 p-4 rounded-lg space-y-4 dark:border-neutral-700">
          <div class="hidden sm:grid sm:grid-cols-5">
            <div class="sm:col-span-2 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Item</div>
            <div class="text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Qty</div>
            <div class="text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Price</div>
            <div class="text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Amount</div>
          </div>

          <div class="hidden sm:block border-b border-gray-200 dark:border-neutral-700"></div>

          @foreach ($itemCustomer as $item)
          <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
            <div class="col-span-full sm:col-span-2">
              <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Item</h5>
              <p class="font-medium text-gray-800 dark:text-neutral-200">{{ $item->item->name }} <br><p class="text-gray-800 text-xs font-medium">
                @if ($item->discount==0)
                  
              @else
                 Discount @currency($item->discount)
              @endif</p></p>
            </div>
            <div>
              <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Qty</h5>
              <p class="text-gray-800 dark:text-neutral-200">{{ $item->qty }}</p>
            </div>
            <div>
              <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Price</h5>
              <p class="text-gray-800 dark:text-neutral-200">@currency($item->item->price)</p>
            </div>
            <div>
              <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Amount</h5>
              <p class="sm:text-end text-gray-800 dark:text-neutral-200">@currency($item->item->price*$item->qty-$item->discount)</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      <!-- End Table -->

      <!-- Flex -->
      <div class="mt-2 flex sm:justify-end">
        <div class="w-full max-w-2xl sm:text-end space-y-2">
          <!-- Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Subtotal</dt>
              <dd class="col-span-2 text-gray-500 dark:text-neutral-500">@currency($customer->subtotal)</dd>
            </dl>

            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Pay</dt>
              <dd class="col-span-2 text-gray-500 dark:text-neutral-500">@currency($customer->pay)</dd>
            </dl>

            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Change</dt>
              <dd class="col-span-2 text-gray-500 dark:text-neutral-500">@currency($customer->return_pay)</dd>
            </dl>

            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Deficiency</dt>
              <dd class="col-span-2 text-gray-500 dark:text-neutral-500">@currency($customer->deficiency)</dd>
            </dl>
          </div>
          <!-- End Grid -->
        </div>
      </div>
      <!-- End Flex -->

      <div class="mt-1">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-neutral-200 inline-flex gap-3">Thank you! <img src="https://github.com/anvilco/html-pdf-invoice-template/raw/main/img/heart.png" alt="heart"></h4>
        {{-- <p class="text-gray-500 dark:text-neutral-500">If you have any questions concerning this invoice, use the following contact information:</p> --}}
        {{-- <div class="mt-2">
          <p class="block text-sm font-medium text-gray-800 dark:text-neutral-200">example@site.com</p>
          <p class="block text-sm font-medium text-gray-800 dark:text-neutral-200">+62-8228-5000-038</p>
        </div> --}}
      </div>

      <p class=" text-sm text-gray-500 dark:text-neutral-500">© 2024 IT Development Team MUNING Photo</p>
    </div>
    <!-- End Card -->

    <!-- Buttons -->
    <!-- End Buttons -->
  </div>
</div>
<!-- End Invoice -->

</div>

</body></html>