<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class FinanceDashboardController extends Controller
{
    public function index()
    {
        $startDate = date('Y-m-d 00:00:00');
        $endDate = date('Y-m-d 23:59:59');
        $getCustomer = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $getCustomerFinish = Customer::where('progress', '1')->whereBetween('created_at', [$startDate, $endDate])->count();
        $getCustomerPayment = Customer::whereBetween('created_at', [$startDate, $endDate])->sum("subtotal");
        $getCustomerNotPaid = Customer::whereBetween('created_at', [$startDate, $endDate])->sum("deficiency");

        $data = [
            "pageGroup" => "0",
            "pageTitle" => "Finance",
            "customerToday" => $getCustomer,
            "customerFinish" => $getCustomerFinish,
            "customerPayment" => $getCustomerPayment,
            "customerNotPaid" => $getCustomerNotPaid
        ];
        return view("v2.finance.home", $data);
    }
}
