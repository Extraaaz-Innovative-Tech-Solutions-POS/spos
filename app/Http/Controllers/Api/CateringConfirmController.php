<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KotResource;
use App\Http\Resources\OrderResource;
use App\Models\Item;
use App\Models\KOT;
use App\Models\KotItem;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\TableActive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CateringConfirmController extends Controller
{
    public function cateringConfirmOrder(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'table_id' => 'required',
            'items' => 'required',
            'orderType' => 'required',
            'customerId' => 'required',
        ]);

        return DB::transaction(function () use ($user, $request) {
            $oldKot = KOT::where('restaurant_id', $user->restaurant_id)
                ->where('table_id', $request->table_id)
                ->first();

            if ($oldKot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table already has an order with the same table_id'
                ]);
            }

            $todaysDate = now()->toDateString(); // Get today's date in 'Y-m-d' format
            $order_number = 1;

            $table_order_number = KOT::where('restaurant_id', $user->restaurant_id)
                ->whereDate('created_at', $todaysDate)
                ->latest()
                ->pluck('order_number')
                ->first();

            if ($table_order_number) {
                $order_number = $table_order_number + 1;
            }

            $advanceDate = $request->advance_order_date_time;
            $advanceDate = $advanceDate ? Carbon::createFromFormat('Y-m-d H:i:s', $advanceDate) : null;

            $kot = new KOT();
            $kot->table_id = $request->table_id;
            $kot->order_number = $order_number;
            $kot->sub_table_number = $request->sub_table ?? null;
            $kot->section_id = $request->section_id ?? null;
            $kot->table_number = $request->table ?? null;
            $kot->floor_number = $request->floor ?? null;
            $kot->order_type = $request->orderType;
            $kot->customer_id = $request->customerId;
            $kot->restaurant_id = $user->restaurant_id;
            $kot->status = "PENDING";
            $kot->advance_order_date_time = $advanceDate;
            $kot->save();

            $grand_total = 0;

            foreach ($request->items as $orderItem) {
                $kotItem = new KotItem();
                $kotItem->kot_id = $kot->id;
                $kotItem->table_id = $request->table_id;
                $kotItem->item_id = $orderItem['Id'];
                $kotItem->quantity = $orderItem['quantity'];
                $kotItem->price = $orderItem['price'];
                $kotItem->product_total = $orderItem['quantity'] * $orderItem['price'];
                $kotItem->name = $orderItem['name'];
                $kotItem->instruction = $orderItem['instruction'] ?? null;
                $kotItem->restaurant_id = $user->restaurant_id;
                $kotItem->save();

                $grand_total += $orderItem['quantity'] * $orderItem['price'];
            }

            $kot->total = $grand_total;
            $kot->grand_total = $grand_total;
            $kot->save();

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully'
            ], 200);
        });
    }

    public function cateringOrderBill(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'table_id' => 'required',
            'order_Type' => 'required',
        ]);

        $table_id = $request->input('table_id');
        $order_type = $request->input('order_Type');



        $kot = KOT::where(['restaurant_id' => $user->restaurant_id, 'order_type' => $order_type, 'table_id' => $table_id])->first();

        if ($kot) {
            $kot = new KotResource($kot);
            // $order = $kot ? $kot->kotItem->where(["table_id"=> $table_id,'status'=>'PENDING']) : null;
            return response()->json(["success" => true, "message" => "Orders List", "kot" => $kot]);
        } else {
            return response()->json(["success" => false, "message" => "No Orders Found"]);
        }
    }


    public function updateItemCat(Request $request)
    {

        $user = Auth::user();

        // Validate the request
        $request->validate([
            'table_id' => 'required',
            'item_id' => 'required',
            'Item' => 'required|array', // Ensure 'Item' is an array
            "orderType" => "required",
            // "instruction" =>""   
        ]);

        // Retrieve the KOT
        $kot = KOT::where('restaurant_id', $user->restaurant_id)
            ->where('table_id', $request->table_id)
            ->first();

        // Check if KOT exists
        if (!$kot) {
            return response()->json(['success' => false, 'message' => 'Data does not exist for this table_id']);
        }

        // Check if KOT is cancelled or completed
        if ($kot->is_cancelled == 1) {
            return response()->json(['success' => false, 'message' => 'Data does not exist for this table_id, since it has been cancelled']);
        }
        if ($kot->status == 'COMPLETED') {
            return response()->json(['success' => false, 'message' => 'Cannot update item, Order has already been completed']);
        }

        // Retrieve the KOT item
        $kotItem = KotItem::where("table_id", $request->table_id)
            ->where("item_id", $request->item_id)
            ->first();

        // Check if KOT item exists and is not cancelled
        if (!$kotItem || $kotItem->is_cancelled == 1) {
            return response()->json(['success' => false, 'message' => 'Data does not exist for this item_id in the table']);
        }

        // Calculate the total
        $total = $kot->total - $kotItem->product_total;

        // Retrieve the order item from the request
        $orderItem = $request->input('Item');

        // Update the KOT item with new values
        $kotItem->kot_id = $kot->id;
        $kotItem->table_id = $request->table_id;
        $kotItem->item_id = $orderItem['Id'];
        $kotItem->quantity = $orderItem['quantity'];
        $kotItem->price = $orderItem['price'];
        $kotItem->product_total = $orderItem['quantity'] * $orderItem['price'];
        $kotItem->name = $orderItem['name'];
        $kotItem->instruction = $orderItem['instruction'];
        $kotItem->restaurant_id = $user->restaurant_id;
        $kotItem->save();

        // Update the total
        $total += $kotItem->product_total;
        $kot->total = $total;
        $kot->save();

        return response()->json(["success" => true, "message" => "Item updated successfully"]);
    }

    public function additemCat(Request $request)
    {

        $user = Auth::user();
        $request->validate([
            'table_id' => 'required',
            'items' => 'required',
            "orderType" => "required",
            // "item_id" => '',
        ]);

        $kot = KOT::where('restaurant_id', $user->restaurant_id)->where('table_id', $request->table_id)->first();

        if (!$kot) {
            return response()->json(['success' => false, 'message' => 'Data does not exists for this table_id']);
        }

        if ($kot->is_cancelled == 1) {
            return response()->json(['success' => false, 'message' => 'Data does not exists for this table_id, since it has been cancelled']);
        }

        if ($kot->status == 'COMPLETED') {
            return response()->json(['success' => false, 'message' => 'Cannot add item, Order has been already Completed']);
        }

        $total = $kot->total;

        $orderItems = $request->items; //[0];

        foreach ($orderItems as $orderItem) {
            $kotItem = KotItem::where(['table_id' => $request->table_id, 'item_id' => $orderItem['Id'], 'restaurant_id' => $user->restaurant_id])->first();

            if (($kotItem) && ($kotItem->is_cancelled == 0)) {
                // $total = $total - $kotItem->product_total;
                $kotItem->quantity += $orderItem['quantity'];
                $kotItem->price = $orderItem['price'];
                $kotItem->product_total += $orderItem['quantity'] * $orderItem['price'];
                $kotItem->name = $orderItem['name'];
                $kotItem->save();

                $total += $orderItem['quantity'] * $orderItem['price'];

                $kot->total = $total;
                $kot->save();

                // return response()->json(['success'=> true, 'message' => 'Item upadeted to table_id : '. $request->table_id .' successfully'], 200);
            } else {
                $kotItem = new KotItem();
                $kotItem->kot_id = $kot->id;
                $kotItem->table_id = $request->table_id;
                $kotItem->item_id = $orderItem['Id'];
                $kotItem->quantity = $orderItem['quantity'];
                $kotItem->price = $orderItem['price'];
                $kotItem->product_total = $orderItem['quantity'] * $orderItem['price'];
                $kotItem->name = $orderItem['name'];
                $kotItem->status = "PENDING";
                $kotItem->restaurant_id = $user->restaurant_id;
                $kotItem->save();

                $total += $orderItem['quantity'] * $orderItem['price'];

                $kot->total = $total;
                $kot->save();
            }
        }
        return response()->json(['success' => true, 'message' => 'Items added to table_id : ' . $request->table_id . ' successfully'], 200);
    }


    public function cancelItemCatering(Request $request)
    {
        $user = Auth::user();

        // return $user->restaurant_id;

        $request->validate([
            "table_id" => "required",
            "item_id" => "required",
        ]);

        $table_id = $request->table_id;
        $item_id = $request->item_id;
        $cancel_reason = $request->cancel_reason;

        $kot = KOT::where("table_id", $table_id)->first();

        if (!$kot) {
            return response()->json(['success' => false, 'message' => 'Table Id does not exists in the KOT table']);
        }

        $kotItem = KotItem::where(['table_id' => $table_id, 'item_id' => $item_id, 'restaurant_id' => $user->restaurant_id])->first();

        if (!$kotItem) {
            return response()->json(['success' => false, 'message' => 'Item has not been found for this order']);
        }

        $item = Item::findOrFail($item_id); //->first();

        $itemName = $item->item_name;
        // $itemPrice = $item->price;


        if ($kotItem->is_cancelled == 0) {
            $kotItem->is_cancelled = 1;
            $kotItem->cancel_reason = $cancel_reason;
            $kotItem->save();

            $kot->total = $kot->total - $kotItem->product_total;
            $kot->save();
            return response()->json(['success' => true, 'message' => $itemName . ' item has been cancelled successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => $itemName . ' item has been already cancelled for this order'], 404);
        }
    }

    public function completeOrderCatering(Request $request)
    {
        $request->validate([
            'table_id' => 'required',
            'ispaid' => 'required',
            'payment_type' => 'required',
            'orderType' => '',
            // "sub_table" => "",
            "table" => "",
            // "section_id" => "",
            // "floor" => "",
            'is_partial_paid' => "",
            'is_full_paid' => "",
            'delivery_address_id' => "",
            "discount" => "",
            "thali_price" =>"",
            "no_of_thali"=> "",

        ]);

        $user = Auth::user();
        $table_id = $request->table_id;
        
        return DB::transaction(function () use ($user, $table_id, $request) {
            $thali_price = $request->thali_price;
            $no_of_thali = $request ->no_of_thali;
            $Cdiscount = $request ->discount;
            $Ctotal = $thali_price * $no_of_thali;
            $subTotal = $Ctotal - $Cdiscount;

            $kot = KOT::where("table_id", $table_id)->first(); // Do Eager Loading of Kotitems

            if (!$kot) {
                return response()->json(['success' => false, 'message' => 'Data does not exists for this table_id']);
            }

            if ($kot->is_cancelled == 1) {
                return response()->json(['success' => false, 'message' => 'Data does not exists for this table_id, since it has been cancelled']);
            }

            if ($kot->status == 'COMPLETED') {
                return response()->json(['success' => false, 'message' => 'Order has been already Completed'], 404);
            }

            // $kot->status = "COMPLETED";
            // if ($request->discounted_amount) {
            //     $kot->total_discount += $request->discounted_amount;
            //     $kot->save();
            //     $kot->grand_total = $kot->total - $kot->total_discount;
            //     $kot->save();
            // }

            $customer_id = $kot->customer_id;
            $kotItems = KotItem::where('table_id', $table_id)->get(); // Call kotitems from kot through relation

            if (!$kotItems) {
                return response()->json(['success' => false, 'message' => 'There are no kotItems present for this table id']);
            }

            $products = $this->mergedData($kotItems);
            $products = json_encode($products);

            $status = "COMPLETED";
            
            if($request->is_full_paid != 1)
            {
                $status = "PENDING";
            }

            $kot->status = $status;
            $kot->save();

            foreach ($kotItems as $kotItem) {
                $kotItem->status = $status;
                $kotItem->save();
            }
            
            $order = new Order();
            $order->table_id = $request->table_id;
            $order->ispaid = $request->ispaid;
            $order->sub_table_number = $kot->sub_table_number ?? null;
            $order->section_id = $kot->section_id ?? null;
            $order->table_number = $kot->table_number;
            $order->floor_number = $kot->floor_number ?? null;
            $order->order_type = $kot->order_type;
            $order->customer_id = $customer_id;
            $order->invoice_id = $kot->order_number;
            $order->restaurant_id = $user->restaurant_id;
            $order->product = $products;    
            $order->product_total = $Ctotal;    // Total before Tax and Discount
            $order->total_discount = $Cdiscount ??null;     // $request->total_discount; // Total Discount
            $order->subtotal = $subTotal; // Total after discount
            $order->restrotaxtotal = 0;     // $request->restrotaxtotal; // Total Tax
            $order->restro_tax = 0;         // $request->restro_tax;  // Tax Data
            $order->othertaxtotal = 0;      // $request->othertaxtotal; // Total of other tax
            $order->other_tax = 0;          // $request->other_tax; // Other tax data
            $order->total = $subTotal;    // Total after adding tax and substracting discount
            $order->status =  $status;
            $order->advance_order_date_time= $kot->advance_order_date_time;
            $order->thali_price = $thali_price;                
            $order->no_of_thali = $no_of_thali;
            $order->save();

            // if($request->is_full_paid == 1 and $kot->order_type == 'Advance')
            // {     
                // }
            // }


            // if ($request->is_partial_paid == 1) {
            //     $status = "PENDING";

            //     $kot->status = $status;
            //     $kot->save();

            //     foreach ($kotItems as $kotItem) {
            //         $kotItem->status = $status;
            //         $kotItem->save();
            //     }   
                

            //     $order->table_id = $request->table_id;
            //     $order->ispaid = $request->ispaid;
            //     $order->sub_table_number = $kot->sub_table_number ?? null;
            //     $order->section_id = $kot->section_id ?? null;
            //     $order->table_number = $kot->table_number;
            //     $order->floor_number = $kot->floor_number ?? null;
            //     $order->order_type = $kot->order_type;
            //     $order->customer_id = $customer_id;
            //     $order->invoice_id = $kot->order_number;
            //     $order->restaurant_id = $user->restaurant_id;
            //     $order->product = $products;    
            //     $order->product_total = $Ctotal;    // Total before Tax and Discount
            //     $order->total_discount = $Cdiscount ??null;     // $request->total_discount; // Total Discount
            //     $order->subtotal = $subTotal; // Total after discount
            //     $order->status =  $status;
            //     $order->restrotaxtotal = 0;     // $request->restrotaxtotal; // Total Tax
            //     $order->restro_tax = 0;         // $request->restro_tax;  // Tax Data
            //     $order->othertaxtotal = 0;      // $request->othertaxtotal; // Total of other tax
            //     $order->other_tax = 0;          // $request->other_tax; // Other tax data
            //     $order->total = $subTotal;    // Total after adding tax and substracting discount
            //     $order->advance_order_date_time= $kot->advance_order_date_time;

            //     $order->thali_price = $thali_price;                
            //     $order->no_of_thali = $no_of_thali;

            //     // if($request->is_full_paid == 1 and $kot->order_type == 'Advance')
            //     // {     
            //     $order->save();
            //     // }
            // }


            $orderPayment = new OrderPayment();
            $orderPayment->user_id = $user->id;
            $orderPayment->order_id = $order ? $order->id : null;
            $orderPayment->customer_id = $kot->customer_id;
            $orderPayment->table_id = $request->table_id;
            $orderPayment->order_number = $kot->order_number;
            $orderPayment->restaurant_id = $user->restaurant_id;
            $orderPayment->payment_type = $request->payment_type; // Cash/Online -- Compulsary
            $orderPayment->payment_method = $request->payment_method ?? null; // if ONline - UPI/Card/EMI, etc
            $orderPayment->amount = $kot->total;
            $orderPayment->status = $status;
            $orderPayment->transaction_id = $request->transaction_id ?? null;
            $orderPayment->payment_details = $request->payment_details ?? null;
            $orderPayment->money_given = $request->money_given ?? null;
            $orderPayment->is_partial_paid = $request->is_partial_paid ?? null;
            $orderPayment->is_full_paid = $request->is_full_paid ?? null;
            $orderPayment->save();

            // if ($request->is_full_paid == 1) {
            //     $orderPayments = OrderPayment::where('table_id', $request->table_id)->get();
            //     foreach ($orderPayments as $orderPayment) {
            //         $orderPayment->status = "COMPLETED";
            //         $orderPayment->save();
            //     }
            // }

            $order = new OrderResource($order);

            return response()->json(["success" => true, "data" => $order, "message" => "Order Completed Successfully"]);
        });

        // return response()->json(["success"=>false , "message"=>"Order Not Completed"]);
    }

    public function partialOrderPayment(Request $request)
    {
        $request->validate([
            'table_id' => 'required',
            // 'ispaid' => 'required',
            'payment_type' => 'required',
            // 'orderType' => '',
            // "table" => "",
            'is_partial_paid' => "",
            'is_full_paid' => "",
            "money_given" => "required",
            // 'delivery_address_id' => "",
            // "discount" => "",
            // "thali_price" =>"",
            // "no_of_thali"=> "",
        ]);

        $user = Auth::user();
        $table_id = $request->table_id;

        $kot = KOT::where("table_id",$table_id)->first();
        $order = Order::where("table_id",$table_id)->first();
        $status = "PENDING";
        
        $orderPayment = new OrderPayment();
        $orderPayment->user_id = $user->id;
        $orderPayment->order_id = $order ? $order->id : null;
        $orderPayment->customer_id = $kot->customer_id;
        $orderPayment->table_id = $request->table_id;
        $orderPayment->order_number = $kot->order_number;
        $orderPayment->restaurant_id = $user->restaurant_id;
        $orderPayment->payment_type = $request->payment_type; // Cash/Online -- Compulsary
        $orderPayment->payment_method = $request->payment_method ?? null; // if ONline - UPI/Card/EMI, etc
        $orderPayment->amount = $kot->total;
        $orderPayment->status = $status;
        $orderPayment->transaction_id = $request->transaction_id ?? null;
        $orderPayment->payment_details = $request->payment_details ?? null;
        $orderPayment->money_given = $request->money_given ?? null;
        $orderPayment->is_partial_paid = $request->is_partial_paid ?? null;
        $orderPayment->is_full_paid = $request->is_full_paid ?? null;
        $orderPayment->save();

        if ($request->is_full_paid == 1) {

            $orderPayments = OrderPayment::where('table_id', $request->table_id)->get();
            foreach ($orderPayments as $orderPayment) {
                $orderPayment->status = "COMPLETED";
                $orderPayment->save();
            }

            $kot->status = "COMPLETED";
            $kot->save();

            $kotItems = $kot->kotItems;
            foreach($kotItems as $kotItem)
            {
                $kotItem->status = "COMPLETED";
                $kotItem->save();
            }

            $orders = Order::where('table_id', $request->table_id)->get();
            foreach($orders as $order1)
            {
                $order1->status = "COMPLETED";
                $order1->save();
            }

        }

        $order = new OrderResource($order); 

        return response()->json(["success"=>true,"message"=>"Order Payment done Successfully","data" => $order]);

    }
    private function mergedData($kotItems)
    {
        $mergedData = [];

        foreach ($kotItems as $kotItem) {
            // Process each KOT item and merge data as needed
            // For example:
            $mergedData[] = [
                'id' => $kotItem->id,
                'name' => $kotItem->name,
                // Add more fields as needed
            ];
        }

        return $mergedData;
    }


    public function cancelOrderCatering(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            "table_id" => "required",
        ]);
        $table_id = $request->table_id;
        $cancel_reason = $request->cancel_reason;

        $kot = KOT::where(['restaurant_id' => $user->restaurant_id, 'table_id' => $table_id])->first();

        if ($kot) {
            if ($kot->is_cancelled == 0) {
                $kot->cancelled_reason = $cancel_reason;
                $kot->is_cancelled = 1;
                $kot->save();

                $kotItem = KotItem::where('table_id', $table_id)->get();
                foreach ($kotItem as $kotItem) {
                    $kotItem->is_cancelled = 1;
                    $kotItem->cancel_reason = $cancel_reason;
                    $kotItem->save();
                }

                $tableActive = TableActive::where('table_id', $table_id)->first();
                if ($tableActive) {
                    $tableActive->delete();
                }

                return response()->json(['success' => true, 'message' => 'Order has been cancelled successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Order has been already cancelled successfully']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Table does not exists in the KOT table']);
        }
    }

    function cateringPendingOrders()
    {
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;

        $data = Order::where('status','PENDING')->where('order_type','Catering')->where('restaurant_id', $restaurant_id)->get();

        $order = OrderResource::collection($data);

        return response()->json(["success" => true, "data" => $order, ]);


    }
    
}
