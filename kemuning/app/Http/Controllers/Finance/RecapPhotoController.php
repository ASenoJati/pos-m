<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecapPhotoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('daterange')) {
            $date = Carbon::parse($request->daterange)->format('Y-m-d');
            $photo = DB::table('checkouts')
                ->join('items', 'checkouts.item_id', '=', 'items.id')
                ->select(
                    'checkouts.id',
                    'checkouts.customer_id',
                    'checkouts.track_id',
                    'checkouts.item_id',
                    'checkouts.qty',
                    'checkouts.note',
                    'checkouts.discount',
                    'checkouts.created_at',
                    'checkouts.updated_at',
                    'items.price',
                    'items.name',
                    'items.type'
                )
                ->whereNull('checkouts.deleted_at')
                ->whereBetween('checkouts.created_at', ["$date 00:00:00", "$date 23:59:59"])
                ->where('items.type', 2)
                ->orderBy('checkouts.id', 'ASC')
                ->get();
        } else {
            $date1 = date('Y-m-d');
            $photo = DB::table('checkouts')
                ->join('items', 'checkouts.item_id', '=', 'items.id')
                ->select(
                    'checkouts.id',
                    'checkouts.customer_id',
                    'checkouts.track_id',
                    'checkouts.item_id',
                    'checkouts.qty',
                    'checkouts.note',
                    'checkouts.discount',
                    'checkouts.created_at',
                    'checkouts.updated_at',
                    'items.price',
                    'items.name',
                    'items.type'
                )
                ->whereNull('checkouts.deleted_at')
                ->whereBetween('checkouts.created_at', ["$date1 00:00:00", "$date1 23:59:59"])
                ->where('items.type', 2)
                ->orderBy('checkouts.id', 'ASC')
                ->get();
        }

        $data = [
            "pageGroup" => "1",
            "pageTitle" => "Recap Photo",
            "photoOrder" => $photo,
        ];
        return view("v2.finance.recap-photo", $data);
    }
}
