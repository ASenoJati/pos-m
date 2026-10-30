<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerProgressController extends Controller
{
    public function index()
    {
        $data = [
            "pageGroup" => "2",
            "pageTitle" => "Customer Progress",
        ];
        return view("v2.finance.progress", $data);
    }

    public function getCustomerProgress(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $getCustomer = Customer::orderBy("id", "desc")->whereBetween('created_at', ["$request->from_date 00:00:00", "$request->to_date 23:59:59"])->get();
        } else {
            $startDate = date('Y-m-d 00:00:00');
            $endDate = date('Y-m-d 23:59:59');
            $getCustomer = Customer::orderBy("id", "desc")->whereBetween('created_at', [$startDate, $endDate])->get();
        }

        return DataTables::of($getCustomer)
            ->addIndexColumn()
            ->addColumn('time', function ($getCustomer) {
                return $getCustomer->created_at->isoFormat('dddd, D MMMM Y');
            })
            ->addColumn('progress', function ($getCustomer) {
                if ($getCustomer->hold_order == '1') {
                    return " <span class='badge text-bg-warning fs-5'>HOLD ORDER</span> ";
                } else {
                    if ($getCustomer->progress == '1') {
                        return " <span class='badge text-bg-success fs-5'>FINISH</span>";
                    } else if ($getCustomer->progress == '2') {
                        return "<span class='badge text-bg-primary fs-5'>ON PROCESS</span>";
                    } else if ($getCustomer->progress == '3') {
                        return " <span class='badge text-bg-info fs-5'>WAITING BY CUSTOMER</span> ";
                    } else {
                        return " <span class='badge text-bg-danger fs-5'>WAITING FOR QUEUE</span> ";
                    }
                }
            })
            ->addColumn('paidStatus', function ($getCustomer) {
                if ($getCustomer->deficiency == 0) {
                    return "<span class='badge text-bg-success fs-5'>PAID</span>";
                } else {
                    return "<span class='badge text-bg-danger fs-5'>UNPAID Rp " . number_format($getCustomer->deficiency, 2, ',', '.') . "</span>";
                }
            })
            ->rawColumns(['time', 'progress', 'paidStatus'])
            ->make(true);
    }
}
