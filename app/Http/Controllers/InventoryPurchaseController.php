<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\InventoryHistory;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderPayment;
use Illuminate\Support\Facades\Auth;

class InventoryPurchaseController extends Controller
{
    public function purchaseList()
    {
        $user = Auth::user();

        $data = PurchaseOrder::where('restaurant_id', $user->restaurant_id)->get();

        return response()->json(["success" => true, 'data' => $data]);
    }

    public function createPurchase(Request $request)
    {
        $user = Auth::user();

        $validator = $request->validate([
            'supplier_id' => 'required|numeric',
            'product_name' => 'required|string',
            'invoice_number' => '',
            'unit' => 'required',
            'quantity' => 'required',
            'rate' => 'required',
            'cgst' => 'numeric|nullable',
            'sgst' => 'numeric|nullable',
            'vat' => 'numeric|nullable',
            'tax' => 'boolean|nullable',
            'amount' => 'numeric|nullable',
            'discount' => 'numeric|nullable',
            'restaurant_id' => 'numeric|nullable',
            'net_payable' => 'numeric|nullable',
            'reason' => 'string|nullable',
            'original_quantity' => 'numeric|nullable',
        ]);

        // Generate a unique invoice number
        $lastOrder = PurchaseOrder::orderBy('invoice_number', 'desc')->first();
        $newInvoiceNumber = $lastOrder ? $lastOrder->invoice_number + 1 : 1;

        $discount = $request->discount ?? 0;

        $data = new PurchaseOrder;
        $data->supplier_id = $request->supplier_id;
        $data->product_name = $request->product_name;
        $data->invoice_number = $newInvoiceNumber;
        $data->quantity = $request->quantity;
        $data->unit = $request->unit;
        $data->cgst = $request->cgst ?? null;
        $data->sgst = $request->sgst ?? null;
        $data->tax = $request->tax ?? null;
        $data->discount = $discount;
        $data->restaurant_id = $user->restaurant_id;
        $data->rate = $request->rate;
        $data->amount = $request->rate *  $request->quantity;
        $data->reason = $request->reason ?? null;
        $data->original_quantity = $request->quantity;

        $discountedAmount = $data->amount - $discount;

        

        if ($request->tax) {
            $totalTaxPercentage = ($request->cgst ?? 0) + ($request->sgst ?? 0);
            $data->net_payable = $discountedAmount + ($discountedAmount * ($totalTaxPercentage / 100));

            

        } else {
            $data->net_payable = $discountedAmount;
        }

        $data->save();


        $payment = new PurchaseOrderPayment;

        $payment->supplier_id = $request->supplier_id;
        $payment->purchase_order_id = $data->id;

        if ($request->tax) {
            $totalTaxPercentage = ($request->cgst ?? 0) + ($request->sgst ?? 0);
            $payment->amount = $discountedAmount + ($discountedAmount * ($totalTaxPercentage / 100));
        } else {
            $payment->amount = $discountedAmount;
        }

        $payment->discount = $request->discount ?? null;
        $payment->is_full_paid = $request->is_full_paid;
        $payment->is_partial = $request->is_partial;
        $payment->payment_type = $request->payment_type;
        $payment->status = $request->status; // enum('Pending', 'Partial', 'Completed')	
        $payment->amount_paid = $request->amount_paid;
        $payment->restaurant_id = $user->restaurant_id;

        if($request->is_partial == 1)
        {
            $payment->outstanding_amount = ($payment->amount -  $request->amount_paid );
        }
        else
        {
            $payment->outstanding_amount = 0;
        }

        $payment->save();

        $existingHistory = InventoryHistory::where('product_name', $request->product_name)->exists();        

        $history = new InventoryHistory;
        $history->product_name = $request->product_name;;
        $history->qty_change = $request->quantity;
        $history->restaurant_id = $user->restaurant_id;

        if ($existingHistory) {
            $history->change_type = 'restock';
        } else {
            $history->change_type = 'stock added';
        }
        
        $history->save();




        return response()->json(['success' => true, 'message' => "Purchase Order added Successfully", "data" => $data,"paymentDetails"=>$payment]);
    }

    public function updatePurchase(Request $request, $id)
    {
        $user = Auth::user();

        $validator = $request->validate([
            'supplier_id' => 'required|numeric',
            'product_name' => 'required|string',
            'invoice_number' => 'string|nullable',
            'unit' => 'required',
            'quantity' => 'required|numeric',
            'rate' => 'required|numeric',
            'cgst' => 'numeric|nullable',
            'sgst' => 'numeric|nullable',
            'vat' => 'numeric|nullable',
            'tax' => 'boolean|nullable',
            'amount' => 'numeric|nullable',
            'discount' => 'numeric|nullable',
            'restaurant_id' => 'numeric|nullable',
            'net_payable' => 'numeric|nullable',
            'reason' => 'string|nullable',
            'original_quantity' => 'numeric|nullable',
        ]);

        $data = PurchaseOrder::where('restaurant_id', $user->restaurant_id)->findOrFail($id);

        $invoice =  $data->invoice_number;

        $data->supplier_id = $request->supplier_id;
        $data->product_name = $request->product_name;
        $data->invoice_number = $invoice;
        $data->quantity = $request->quantity;
        $data->unit = $request->unit;
        $data->cgst = $request->cgst ?? null;
        $data->sgst = $request->sgst ?? null;
        $data->tax = $request->tax ?? null;
        $data->discount = $request->discount ?? null;
        $data->restaurant_id = $user->restaurant_id;
        $data->rate = $request->rate;
        $data->amount = $request->rate * $request->quantity;
        $data->reason = $request->reason ?? null;
        $data->original_quantity = $request->quantity;

        if ($request->tax) {
            $totalTaxPercentage = ($request->cgst ?? 0) + ($request->sgst ?? 0);
            $data->net_payable = $data->amount + ($data->amount * ($totalTaxPercentage / 100));
        } else {
            $data->net_payable = $data->amount;
        }

        $data->save();

       

        return response()->json(['success' => true, 'message' => "Purchase Order updated Successfully", "data" => $data]);
    }

    public function deletePurchase($id)
    {
        $user = Auth::user();

        $purchaseOrder = PurchaseOrder::where('restaurant_id', $user->restaurant_id)->findOrFail($id);

        $purchaseOrder->delete();

        return response()->json(['success' => true, 'message' => "Purchase Order deleted successfully"]);
    }


    public function addPayment(Request $request,$id)
    {
        $user = Auth::user();

        $order = PurchaseOrder::where('id', $id)->where('restaurant_id', $user->restaurant_id)->first();

        

        $amountPaid = PurchaseOrderPayment::where('purchase_order_id', $id)
        ->where('restaurant_id', $user->restaurant_id)
        ->sum('amount_paid');
        
        $pay = new PurchaseOrderPayment;
        $pay->purchase_order_id = $id;
        $pay->supplier_id = $order->supplier_id;
        $pay->restaurant_id = $user->restaurant_id;
        $pay->amount = $order->net_payable;
        $pay->discount = $order->discount;
        $pay->outstanding_amount = ($order->net_payable -  $request->amount_paid - $amountPaid );
        $pay->payment_type = $request->payment_type;
        $pay->status = $request->status;
        $pay->amount_paid = $request->amount_paid;
        $pay->is_full_paid = $request->is_full_paid;
        $pay->is_partial = $request->is_partial;

        $pay->save();

        if($request->is_full_paid == 1)
        {
             PurchaseOrderPayment::where('purchase_order_id', $id)
                ->where('restaurant_id', $user->restaurant_id)
                ->update(['status' => 'Completed']);
        }

        
        return response()->json(['success' => true, 'message' => "Purchase Order payment successfully","paymentDetails"=> $pay]);


    }

    public function viewPaymentDetailsList($id)
    {
        $user = Auth::user();

        $listPayments = PurchaseOrderPayment::where('purchase_order_id', $id)
        ->where('restaurant_id', $user->restaurant_id)
        ->get();

        return response()->json(['success' => true, "paymentDetails"=> $listPayments]);


    }

    public function inventoryHistory()
    {
        $user = Auth::user();

        $history = InventoryHistory::where('restaurant_id', $user->restaurant_id)->get();

        return response()->json(['sucess'=>true,'history'=>$history]);

    }
}
