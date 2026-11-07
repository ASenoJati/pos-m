<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function customerManagement()
    {
        $data = [
            "pageGroup" => "1",
            "pageTitle" => "Customer Management",
            "today" => Carbon::now()->isoFormat('dddd, D MMMM Y'),
        ];
        return view("v2.admin.customer", $data);
    }

    public function customerTable(Request $request)
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
                return "<h5>" . $getCustomer->created_at->isoFormat('dddd, D MMMM Y') . "</h5>";
            })
            ->addColumn('progress', function ($getCustomer) {
                if ($getCustomer->hold_order == '1') {
                    return " <span class='badge text-bg-warning fs-5'>
                                        HOLD ORDER
                                    </span> ";
                } else {
                    if ($getCustomer->progress == '1') {
                        return " <span class='badge text-bg-success fs-5'>
                                            FINISH
                                        </span>";
                    } else if ($getCustomer->progress == '2') {
                        return "<span class='badge text-bg-primary fs-5'>
                                            ON PROCESS
                                        </span>";
                    } else if ($getCustomer->progress == '3') {
                        return " <span class='badge text-bg-info fs-5'>
                                            WAITING BY CUSTOMER
                                        </span> ";
                    } else {
                        return " <span class='badge text-bg-danger fs-5'>
                                            WAITING FOR QUEUE
                                        </span> ";
                    }
                }
            })
            ->addColumn('action', function ($getCustomer) {
                if ($getCustomer->hold_order) {
                    return "
                <a href='javascript:void(0)' class='btn btn-secondary btn-sm viewCstOrder' id='viewCstOrder' data-id=" . $getCustomer->track . " type='submit'><i class='bi bi-eye-fill'></i></a>
                ";
                } else if ($getCustomer->whatsapp == '62' || $getCustomer->whatsapp == null) {
                    return "
                <a href='javascript:void(0)' class='btn btn-success btn-sm finishOrder' id='finishOrder' data-id=" . $getCustomer->track . " type='submit'>FINISH</a> 
                <a href='javascript:void(0)' class='btn btn-primary btn-sm processOrder' id='processOrder' data-id=" . $getCustomer->track . " type='submit'>ON PROCESS</a>
                <a href='javascript:void(0)' class='btn btn-secondary btn-sm viewCstOrder' id='viewCstOrder' data-id=" . $getCustomer->track . " type='submit'><i class='bi bi-eye-fill'></i></a>
                ";
                } else {
                    return "
                    <a href='javascript:void(0)' class='btn btn-success btn-sm finishOrder' id='finishOrder' data-id=" . $getCustomer->track . " type='submit'>FINISH</a> 
                    <a href='javascript:void(0)' class='btn btn-primary btn-sm processOrder' id='processOrder' data-id=" . $getCustomer->track . " type='submit'>ON PROCESS</a>
                    <a href='https://web.whatsapp.com/send/?phone=$getCustomer->whatsapp' target='__blank' class='btn btn-dark btn-sm ' type='submit'><i class='bi bi-chat-fill'></i> $getCustomer->whatsapp</a>
                    <a href='javascript:void(0)' class='btn btn-secondary btn-sm viewCstOrder' id='viewCstOrder' data-id=" . $getCustomer->track . " type='submit'><i class='bi bi-eye-fill'></i></a>
                    ";
                }
            })
            ->rawColumns(['time', 'action', 'progress'])
            ->make(true);
    }

    public function customerDetail($id)
    {
        $data = [
            "pageGroup" => "0",
            "pageTitle" => "Order Detail",
            "track_id" => $id,
            "itemCustomer" => Checkout::where("track_id", $id)->get(),
            "customerData" => Customer::where("track", $id)->first()
        ];
        return view('v2.admin.order-detail', $data);
    }
}
