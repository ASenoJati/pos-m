<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('v2.dashboard');
    }

    public function editCustomer(Request $request)
    {
        $getCustomer = Customer::findOrFail($request->idOrder);
        return redirect('/transaction/' . $getCustomer->track . '');
    }

    public function getRecentCustomer()
    {
        $startDate = date('Y-m-d 00:00:00');
        $endDate = date('Y-m-d 23:59:59');
        $data = [
            'recent_customer' => Customer::orderBy("id", "desc")->whereBetween('created_at', [$startDate, $endDate])->limit(5)->get(),
        ];
        return view('v2.recent-customer', $data);
    }

    public function createNewCustomer()
    {
        $trackId = date('YmdHis');
        Customer::create([
            'track'     => $trackId
        ]);
        return redirect('/transaction/' . $trackId . '');
    }
}
