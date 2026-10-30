<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\Customer;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function itemData()
    {
        $data = [
            "pageGroup" => "0",
            "pageTitle" => "Item Data",
        ];
        return view('v2.admin.item', $data);
    }

    public function itemTable()
    {
        $getItem = Item::get();

        return DataTables::of($getItem)
            ->addIndexColumn()
            ->addColumn('price', function ($getItem) {
                return "Rp " . number_format($getItem->price, 2, ',', '.');
            })
            ->addColumn('type', function ($getItem) {
                if ($getItem->type == 1) {
                    return "FRAME";
                } else if ($getItem->type == 2) {
                    return "FOTO";
                } else if ($getItem->type == 3) {
                    return "NON FOTO";
                }
            })
            ->addColumn('action', function ($getItem) {
                return "
                <a href='/admin/updateItemData/" . $getItem->id . "' class='btn btn-warning'>EDIT</a>
                ";
            })
            ->rawColumns(['action', 'type', 'price'])
            ->make(true);
    }

    public function updateItemData($id)
    {
        $data = [
            "pageGroup" => "0",
            "pageTitle" => "Item Data",
            "itemdata" => Item::find($id),
        ];
        return view('v2.admin.item-edit', $data);
    }

    public function saveUpdateItemData(Request $request)
    {
        Item::find($request->id)->update([
            "name" => $request->name,
            "price" => $request->price,
            "type" => $request->type,
        ]);
    }
}
