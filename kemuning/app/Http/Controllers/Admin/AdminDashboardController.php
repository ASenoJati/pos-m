<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $data = [
            "pageGroup" => "0",
            "pageTitle" => "Admin Home",
            "customer" => Customer::where("progress", null)->orWhere("progress", 2)->orWhere("progress", 3)->orWhere("hold_order", 1)->get(),
            "customerFinish" => Customer::where("progress", 1)->count(),
            "customerOnProcess" => Customer::where("progress", 2)->count(),
            "customerWaitingByCustomer" => Customer::where("progress", 3)->count(),
            "customerWaitingForQueue" => Customer::where("progress", null)->count(),
        ];
        return view('v2.admin.home', $data);
    }
}
