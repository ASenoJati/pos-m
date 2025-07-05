<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Customer;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FinanceController extends Controller
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

    public function recapPhoto(Request $request)
    {
        if ($request->filled('daterange')) {
            $date = Carbon::parse($request->daterange)->format('Y-m-d');
            $photo = DB::select("SELECT
            checkouts.id,
            checkouts.customer_id,
            checkouts.track_id,
            checkouts.item_id,
            checkouts.qty,
            checkouts.note,
            checkouts.discount,
            checkouts.created_at,
            checkouts.updated_at,
            items.price,
            items.`name`,
            items.type
            FROM
            checkouts
            INNER JOIN items ON checkouts.item_id = items.id
            WHERE
            checkouts.created_at BETWEEN '$date 00:00:00'
            AND '$date 23:59:59'
            AND items.type = 2
            ORDER BY
            checkouts.id ASC");
        } else {
            $date1 = date('Y-m-d');
            $photo = DB::select("SELECT
            checkouts.id,
            checkouts.customer_id,
            checkouts.track_id,
            checkouts.item_id,
            checkouts.qty,
            checkouts.note,
            checkouts.discount,
            checkouts.created_at,
            checkouts.updated_at,
            items.price,
            items.`name`,
            items.type
            FROM
            checkouts
            INNER JOIN items ON checkouts.item_id = items.id
            WHERE
            checkouts.created_at BETWEEN '$date1 00:00:00'
            AND '$date1 23:59:59'
            AND items.type = 2
            ORDER BY
            checkouts.id ASC");
        }

        $data = [
            "pageGroup" => "1",
            "pageTitle" => "Recap Photo",
            "photoOrder" => $photo,
        ];
        return view("v2.finance.recap-photo", $data);
    }

    public function recapNonPhoto(Request $request)
    {
        if ($request->filled('daterange')) {
            $date = Carbon::parse($request->daterange)->format('Y-m-d');
            $nonPhotoOrder = DB::select("SELECT
            checkouts.id,
            checkouts.customer_id,
            checkouts.track_id,
            checkouts.item_id,
            checkouts.qty,
            checkouts.note,
            checkouts.discount,
            checkouts.created_at,
            checkouts.updated_at,
            items.price,
            items.`name`,
            items.type
            FROM
            checkouts
            INNER JOIN items ON checkouts.item_id = items.id
            WHERE
            checkouts.created_at BETWEEN '$date 00:00:00'
            AND '$date 23:59:59'
            AND items.type = 3
            ORDER BY
            checkouts.id ASC");
        } else {
            $date1 = date('Y-m-d');
            $nonPhotoOrder = DB::select("SELECT
            checkouts.id,
            checkouts.customer_id,
            checkouts.track_id,
            checkouts.item_id,
            checkouts.qty,
            checkouts.note,
            checkouts.discount,
            checkouts.created_at,
            checkouts.updated_at,
            items.price,
            items.`name`,
            items.type
            FROM
            checkouts
            INNER JOIN items ON checkouts.item_id = items.id
            WHERE
            checkouts.created_at BETWEEN '$date1 00:00:00'
            AND '$date1 23:59:59'
            AND items.type = 3
            ORDER BY
            checkouts.id ASC");
        }

        $data = [
            "pageGroup" => "1",
            "pageTitle" => "Recap Non Photo",
            "nonPhotoOrder" => $nonPhotoOrder
        ];
        return view("v2.finance.recap-nonphoto", $data);
    }

    public function recapFrame(Request $request)
    {
        if ($request->filled('daterange')) {
            $date = Carbon::parse($request->daterange)->format('Y-m-d');
            $frame = DB::select("SELECT
            checkouts.id,
            checkouts.customer_id,
            checkouts.track_id,
            checkouts.item_id,
            checkouts.qty,
            checkouts.note,
            checkouts.discount,
            checkouts.created_at,
            checkouts.updated_at,
            items.price,
            items.`name`,
            items.type
            FROM
            checkouts
            INNER JOIN items ON checkouts.item_id = items.id
            WHERE
            checkouts.created_at BETWEEN '$date 00:00:00'
            AND '$date 23:59:59'
            AND items.type = 1
            ORDER BY
            checkouts.id ASC");
        } else {
            $date1 = date('Y-m-d');
            $frame = DB::select("SELECT
            checkouts.id,
            checkouts.customer_id,
            checkouts.track_id,
            checkouts.item_id,
            checkouts.qty,
            checkouts.note,
            checkouts.discount,
            checkouts.created_at,
            checkouts.updated_at,
            items.price,
            items.`name`,
            items.type
            FROM
            checkouts
            INNER JOIN items ON checkouts.item_id = items.id
            WHERE
            checkouts.created_at BETWEEN '$date1 00:00:00'
            AND '$date1 23:59:59'
            AND items.type = 1
            ORDER BY
            checkouts.id ASC");
        }

        $data = [
            "pageGroup" => "1",
            "pageTitle" => "Recap Frame",
            "frame" => $frame
        ];
        return view("v2.finance.recap-frame", $data);
    }

    public function customerProgress()
    {
        $data = [
            "pageGroup" => "2",
            "pageTitle" => "Customer Progress",
        ];
        return view("v2.finance.progress", $data);
    }

    public function prgTable(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $getCustomer = Customer::orderBy("id", "desc")->whereBetween('created_at', ["$request->from_date 00:00:00", "$request->to_date 23:59:59"])->get();
        } else {
            $startDate = date('Y-m-d 00:00:00');
            $endDate = date('Y-m-d 23:59:59');
            $getCustomer = Customer::orderBy("id", "desc")->whereBetween('created_at', [$startDate, $endDate])->get();
        }

        // return $getUnpaid;

        return DataTables::of($getCustomer)
            ->addIndexColumn()
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
            ->addColumn('paidStatus', function ($getCustomer) {
                if ($getCustomer->deficiency == "0") {
                    return "<span class='badge text-bg-success fs-5'>
                                            PAID
                                        </span>";
                } else {
                    function rupiah($angka)
                    {

                        $hasil_rupiah = "Rp " . number_format($angka, 2, ',', '.');
                        return $hasil_rupiah;
                    }
                    return "<span class='badge text-bg-danger fs-5'>
                    UNPAID " . rupiah($getCustomer->deficiency) . "
                </span>";
                }
            })
            ->rawColumns(['progress', 'paidStatus'])
            ->make(true);
    }
}
