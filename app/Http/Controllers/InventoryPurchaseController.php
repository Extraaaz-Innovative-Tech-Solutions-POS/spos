<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
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

        $data = new PurchaseOrder;
        $data->supplier_id = $request->supplier_id;
        $data->product_name = $request->product_name;
        $data->invoice_number = $request->invoice_number;
        $data->quantity = $request->quantity;
        $data->unit = $request->unit;
        $data->cgst = $request->cgst ?? null;
        $data->sgst = $request->sgst ?? null;
        $data->tax = $request->tax ?? null;
        $data->discount = $request->discount ?? null;
        $data->restaurant_id = $user->restaurant_id;
        $data->rate = $request->rate;
        $data->amount = $request->rate *  $request->quantity;
        $data->reason = $request->reason ?? null;
        $data->original_quantity = $request->quantity;

        if ($request->tax) {
            $totalTaxPercentage = ($request->cgst ?? 0) + ($request->sgst ?? 0);
            $data->net_payable = $data->amount + ($data->amount * ($totalTaxPercentage / 100));
        } else {
            $data->net_payable = $data->amount;
        }

        $data->save();

        return response()->json(['success' => true, 'message' => "Purchase Order added Successfully", "data" => $data]);
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

        $data->supplier_id = $request->supplier_id;
        $data->product_name = $request->product_name;
        $data->invoice_number = $request->invoice_number;
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
}
