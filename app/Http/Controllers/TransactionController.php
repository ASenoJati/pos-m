<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function new_transaction($id)
    {
        $getItemAll['data'] = Item::orderby("name", "asc")->select('id', 'name')->get();
        $getCustomer = Customer::where("track", $id)->first();
        $data = [
            'customer_id' => $getCustomer->id,
            'customer_data' => $getCustomer,
            'track_id' => $id,
        ];
        return view('v2.transaction', $data)->with("getItemAll", $getItemAll);
    }

    public function getEmployees($departmentid = 0)
    {
        $empData['data'] = Item::orderby("name", "asc")
            ->where('id', $departmentid)
            ->get();
        return response()->json($empData);
    }

    public function getCustomerItem($id)
    {
        $getCustomerItem = Checkout::where("track_id", $id)->get();
        $data = [
            'track_id' => $id
        ];
        return view('v2.transaction-item', $data, compact('getCustomerItem'));
    }

    public function addNewCustomerItem(Request $request)
    {
        Checkout::create($request->all());
    }

    public function deleteCustomerItem($id, $item_id)
    {
        Checkout::where("id", $item_id)->delete();
    }

    public function updateDataCustomer(Request $request, $id)
    {
        Customer::where("track", $id)->update($request->all());
    }

    public function cancelCustomer($id)
    {
        Checkout::where("track_id", $id)->delete();
        Customer::where("track", $id)->delete();
    }
}
