<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Customer;

class InvoiceController extends Controller
{
    public function customerInvoice($id)
    {

        $customer = Customer::where("track", $id)->firstOrFail();
        $itemCustomer = Checkout::where("track_id", $id)->get();
        $data = [
            "customer" => $customer,
            "invoiceDate" => $customer->created_at->toDayDateTimeString(),
            // "dueDate" => $customer->updated->isoFormat('dddd, D MMMM Y'),
            "itemCustomer" => $itemCustomer
        ];
        // $pdf = Pdf::loadView('v2.invoice.customer-invoice', $data);
        // return $pdf->download('invoice.pdf');
        return view('v2.invoice.customer-invoice', $data);
    }
}
