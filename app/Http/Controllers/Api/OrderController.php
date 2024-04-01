<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KotResource;
use App\Http\Resources\TableActiveResource;
use App\Models\Item;
use App\Models\KOT;
use App\Models\KotItem;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Section;
use App\Models\TableActive;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = Auth::user();

        $restaurant_id  = $request->input('restaurant_id');

        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id

        $order = Order::where('restaurant_id', $user->restaurant_id)->get();

        return response()->json(["success" => true, "data" => $order]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = Auth::user();
        // $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        $restaurant_id = $user->restaurant_id;
        $order = new Order();
        $order->ispaid = $request->ispaid;
        $order->table_number  = $request->table_number;
        $order->floor_number = $request->floor_number;
        $order->order_type  = $request->order_type;
        $order->customer_id = $request->customer_id;
        $order->invoice_id = $request->invoice_id;
        $order->product = $request->product;
        $order->product_total = $request->product_total;
        $order->total_discount = $request->total_discount;
        $order->subtotal = $request->subtotal;
        $order->restrotaxtotal = $request->restrotaxtotal;
        $order->restro_tax = $request->restro_tax;
        $order->othertaxtotal = $request->othertaxtotal;
        $order->other_tax = $request->other_tax;
        $order->total = $request->total;
        $order->invoice_id = $request->invoice_id;
        $order->restaurant_id = $user->restaurant_id;
        $order->save();
        return response()->json(['success' => true, 'message' => 'order added successfully', 'data' => $order]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $order = order::findorFail($id);
        $name = $order->order_name;
        $order->delete();
        return response()->json(["success" => true, "message" => $name . ' order is Deleted Successfully']);
    }

    public function getOrdersBill(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            // 'table_number' => 'required',
            // 'floor_number' => 'required',
            'table_id' => 'required',
        ]);

        $table_number = $request->input('table_number');
        $floor_number = $request->input('floor_number');
        $table_id = $request->input('table_id');

        // $kot = KOT::where(['restaurant_id'=>$user->restaurant_id,'floor_number' => $floor_number, 'table_number' => $table_number,])->first();
        $kot = KOT::where(['restaurant_id' => $user->restaurant_id, 'table_id' => $table_id])->first();
        
        if ($kot) {
            $kot = new KotResource($kot);
            // $order = $kot ? $kot->kotItem->where(["table_id"=> $table_id,'status'=>'PENDING']) : null;
            return response()->json(["success" => true, "message" => "Orders List", "kot" => $kot]); //,"order" => $order]);
        } else {
            return response()->json(["success" => false, "message" => "No Orders Found"]);
        }
    }

    public function getTableId($section)
    {
        try {
            do {
                $sect_var = "";
                $sect_var = $section == "Dine-In" ? 'DI/' : ($section == "TakeAway" ? "TAK/" : ($section == "Delivery" ?  "DEL/" : ($section == "Advance" ? "ADV/" : null)));
                if (!$sect_var) {
                    return "Invalid Section Name";
                }
                $letters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                shuffle($letters);
                $randomLetters = implode('', array_slice($letters, 0, 4));
                $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $currentDate = Carbon::now();
                $currentMonth = $currentDate->format('m');
                $currentYear = $currentDate->format('y');
                $date = $currentDate->format('d');
                $table_id = $sect_var . $randomLetters . $date . $currentMonth . $currentYear . $randomDigits;
            } while (KOT::where('table_id', $table_id)->whereDate('created_at', $currentDate)->exists());

            return response()->json($table_id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function confirmOrder(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'table_id' => 'required',
            'items' => 'required',
            "orderType" => "required",
            "sub_table"=> "",
            "table"=> "",
            "section_id"=> "",
            "floor"=> "",
            "table_divided_by"=> "", // If divided
            "cover_count"=> "", // Number of People
            "customerId" => "",
        ]);

        return DB::transaction(function() use($user, $request)
        {
            $oldKot = KOT::where('restaurant_id', $user->restaurant_id)->where('table_id', $request->table_id)->first();
            if($oldKot)
            {
                return response()->json(['success'=> false, 'message' => 'Table already has an order with same table_id']);
            }

            if(($request->table_divided_by) && ($request->sub_table))
            {
                if($request->sub_table > $request->table_divided_by)
                {
                    return response()->json(['success'=> false, 'message' => "Sub-Table cannot be more than ". $request->table_divided_by]);
                }
            }

            $todaysDate = now()->toDateString(); // Get today's date in 'Y-m-d' format
            $order_number = 1;

            $table_order_number = KOT::where('restaurant_id', $user->restaurant_id)
                ->whereDate('created_at', $todaysDate)->latest()
                ->pluck('order_number')
                ->first();

            if ($table_order_number) {
                $order_number = $table_order_number + 1;
            }
    
            $kot = new KOT();
            $kot->table_id = $request->table_id;
            $kot->order_number = $order_number;        
            // $kot->isready = $request->isready;
            $kot->sub_table_number = $request->sub_table ?? null;
            $kot->section_id = $request->section_id ?? null;
            $kot->table_number = $request->table ?? null;
            $kot->floor_number = $request->floor ?? null;
            $kot->order_type = $request->orderType;
            $kot->customer_id = $request->customerId;
            $kot->restaurant_id = $user->restaurant_id;
            $kot->status = "PENDING";
            // $kot->message = $request->message;
            // $kot->is_cancelled = $request->is_cancelled;
            // $kot->total = $request->total;
            $kot->save();

            $grand_total = 0;

            foreach ($request->items as $orderItem) {

                // return $orderItem;
                $kotItem = new KotItem();
                $kotItem->kot_id = $kot->id;
                $kotItem->table_id = $request->table_id;
                $kotItem->item_id = $orderItem['Id'];
                $kotItem->quantity = $orderItem['quantity'];
                $kotItem->price = $orderItem['price'];
                $kotItem->product_total = $orderItem['quantity'] * $orderItem['price'];
                $kotItem->name = $orderItem['name'];
                $kotItem->instruction = $orderItem['instruction']?? null;
                // $kotItem->is_cancelled = $orderItem->is_cancelled;
                // $kotItem->status = $orderItem->status;
                // $kotItem->cart_id = $orderItem->cart_id;
                $kotItem->restaurant_id = $user->restaurant_id;
                // $kotItem->cancel_reason = $orderItem->cancel_reason;
                $kotItem->save();

                $grand_total += $orderItem['quantity'] * $orderItem['price'];
            }

            $kot->total = $grand_total;
            $kot->save();

            
            if($request->orderType == "Dine")
            {
                $section_name = Section::where('id', $request->section_id)->first()->name;
                
                $tableActive = new TableActive();
                $tableActive->user_id = $user->id;
                $tableActive->table_id = $request->table_id;
                $tableActive->table_number = $request->table;
                $tableActive->divided_by = $request->table_divided_by ?? null;
                $tableActive->split_table_number = $request->sub_table ?? null;
                $tableActive->section_id = $request->section_id;
                $tableActive->section_name = $section_name ?? null;
                $tableActive->floor_number = $request->floor;
                $tableActive->restaurant_id = $user->restaurant_id;
                $tableActive->cover_count = $request->cover_count ?? null;
                $tableActive->status = "Occupied";
                $tableActive->save();
            }

            return response()->json(['success' => true,'message' => 'Order confirmed successfully'], 200);
        });
    }

    public function updateItem(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'table_id' => 'required',
            'item_id' => 'required',
            'Item' => 'required',
            "orderType" => "required",
            // "instruction" =>""   
        ]);

        $kot = KOT::where('restaurant_id', $user->restaurant_id)->where('table_id', $request->table_id)->first();

        if(!$kot)
        {
            return response()->json(['success'=> false, 'message' => 'Data does not exists for this table_id']);
        }

        if($kot->is_cancelled == 1)
        {
            return response()->json(['success'=> false, 'message' => 'Data does not exists for this table_id, since it has been cancelled']);
        }

        if($kot->status == 'COMPLETED')
        {
            return response()->json(['success'=> false, 'message' => 'Cannot update item, Order has been already Completed']);
        }

        $total = $kot->total;

        $kotItem = KotItem::where("table_id",$request->table_id)->where("item_id", $request->item_id)->first();
        
        if(!$kotItem)
        {
            return response()->json(['success'=> false, 'message' => 'Data does exists for this item_id in the table']);
        }

        if($kotItem->is_cancelled == 1)
        {
            return response()->json(['success'=> false, 'message' => 'Cannnot Update, since it has been cancelled']);
        }

        $total = $total - $kotItem->product_total;
        
        $orderItem = $request->Item[0];

        $kotItem->kot_id = $kot->id;
        $kotItem->table_id = $request->table_id;
        $kotItem->item_id = $orderItem['Id'];
        $kotItem->quantity = $orderItem['quantity'];
        $kotItem->price = $orderItem['price'];
        $kotItem->product_total = $orderItem['quantity'] * $orderItem['price'];
        $kotItem->name = $orderItem['name'];
        $kotItem->instruction = $orderItem['instruction'];
        // $kotItem->is_cancelled = $orderItem->is_cancelled;
        // $kotItem->status = $orderItem->status;
        // $kotItem->cart_id = $orderItem->cart_id;
        $kotItem->restaurant_id = $user->restaurant_id;
        // $kotItem->cancel_reason = $orderItem->cancel_reason;
        $kotItem->save();

        $total = $total + $orderItem['quantity'] * $orderItem['price'];
        $kot->total = $total;
        $kot->save();

        return response()->json(["success"=>true, "message"=>"Item Updated Successfully"]);

    }

    public function addItem(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'table_id' => 'required',
            'Item' => 'required',
            "orderType" => "required",
            "item_id" => '',
        ]);

        $kot = KOT::where('restaurant_id', $user->restaurant_id)->where('table_id', $request->table_id)->first();

        if(!$kot)
        {
            return response()->json(['success'=> false, 'message' => 'Data does not exists for this table_id']);
        }

        if($kot->is_cancelled == 1)
        {
            return response()->json(['success'=> false, 'message' => 'Data does not exists for this table_id, since it has been cancelled']);
        }

        if($kot->status == 'COMPLETED')
        {
            return response()->json(['success'=> false, 'message' => 'Cannot add item, Order has been already Completed']);
        }

        $total = $kot->total;

        $orderItem = $request->Item[0];

        $kotItem = KotItem::where(['table_id' => $request->table_id, 'item_id' => $request->item_id, 'restaurant_id' => $user->restaurant_id])->first();
        
        if(($kotItem) && ($kotItem->is_cancelled == 0))
        {
            $total = $total - $kotItem->product_total;
            $kotItem->quantity = $orderItem['quantity'];
            $kotItem->price = $orderItem['price'];
            $kotItem->product_total = $orderItem['quantity'] * $orderItem['price'];
            $kotItem->name = $orderItem['name'];
            $kotItem->save();

            $total += $orderItem['quantity'] * $orderItem['price'];
    
            $kot->total = $total;
            $kot->save();

            return response()->json(['success'=> true, 'message' => 'Item upadeted to table_id : '. $request->table_id .' successfully'], 200);
        }
        else
        {
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
            return response()->json(['success'=> true, 'message' => 'Item added to table_id : '. $request->table_id .' successfully'], 200);
        }
    }

    public function cancelItem(Request $request)
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

        $item = Item::where('id',$item_id)->first();
               
        $itemName = $item->item_name;
        // $itemPrice = $item->price;
        
        $kot = KOT::where("table_id",$table_id)->first();

        if(!$kot)
        {
            return response()->json(['success' => false, 'message' => 'Table Id does not exists in the KOT table']);
        }

        $kotItem = KotItem::where(['table_id'=>$table_id,'item_id'=>$item_id, 'restaurant_id' => $user->restaurant_id])->first();

        if(!$kotItem)
        {
            return response()->json(['success' => false, 'message' => 'Item has not been found for this order']);
        }

        if($kotItem->is_cancelled == 0)
        {
            $kotItem->is_cancelled = 1;
            $kotItem->cancel_reason = $cancel_reason;
            $kotItem->save();    

            $kot->total = $kot->total - $kotItem->product_total;
            $kot->save();
            return response()->json(['success' => true,'message' => $itemName .' item has been cancelled successfully'], 200);
        }
        else{
            return response()->json(['success' => false,'message' => $itemName .' item has been already cancelled for this order'], 404);
        }
    }

    public function cancelOrder(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            "table_id" => "required",
        ]);
        $table_id = $request->table_id;
        $cancel_reason = $request->cancel_reason;

        $kot = KOT::where(['restaurant_id'=>$user->restaurant_id,'table_id'=>$table_id])->first();

        if($kot)
        {
            if($kot->is_cancelled == 0)
            {
                $kot->cancelled_reason = $cancel_reason;
                $kot->is_cancelled = 1;
                $kot->save();

                $kotItem = KotItem::where('table_id',$table_id)->get();
                foreach($kotItem as $kotItem)
                {
                    $kotItem->is_cancelled = 1;
                    $kotItem->cancel_reason = $cancel_reason;
                    $kotItem->save();  
                }

                $tableActive = TableActive::where('table_id',$table_id)->first();
                if($tableActive)
                    {$tableActive->delete();
                    }

                return response()->json(['success' => true,'message' => 'Order has been cancelled successfully'], 200);
            }else
            {
                return response()->json(['success' => false,'message' => 'Order has been already cancelled successfully']);

            }
        }else
        {
            return response()->json(['success' => false,'message' => 'Table does not exists in the KOT table']);
        }
    }

    public function completeOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'required',
            'ispaid' => 'required',
            'payment_type' => 'required',
            'orderType'=> '',
            "sub_table"=> "",
            "table"=> "",
            "section_id"=> "",
            "floor"=> "",
        ]);

        $user = Auth::user();
        $table_id = $request->table_id;

        return DB::transaction(function() use($user, $table_id, $request)
        {

            $kot = KOT::where("table_id",$table_id)->first();

            if(!$kot)
            {  
                return response()->json(['success'=> false, 'message' => 'Data does not exists for this table_id']);
            }

            if($kot->is_cancelled == 1)
            {
                return response()->json(['success'=> false, 'message' => 'Data does not exists for this table_id, since it has been cancelled']);
            }

            if($kot->status == 'COMPLETED')
            {
                return response()->json(['success'=> false, 'message' => 'Cannot add item, Order has been already Completed']);
            }
    
            $kot->status = "COMPLETED";
            $kot->save();

            $customer_id = $kot->customer_id;

            $kotItems = KotItem::where('table_id',$table_id)->get();

            $products = $this->mergedData($kotItems);

            $products = json_encode($products);

            foreach($kotItems as $kotItem)
            {
                $kotItem->status = "COMPLETED";
                $kotItem->save();
            }

            $order = new Order();
            $order->table_id = $request->table_id;
            $order->ispaid = $request->ispaid;
            $order->sub_table_number = $kot->sub_table_number ?? null;
            $order->section_id = $kot->section_id ?? null;
            $order->table_number = $kot->table_number;
            $order->floor_number = $kot->floor_number;
            $order->order_type = $kot->order_type;
            $order->customer_id = $customer_id;
            $order->invoice_id = $kot->order_number;
            $order->restaurant_id = $user->restaurant_id;
            $order->product = $products;    // This is important
            $order->product_total = $kot->total;    // Total before Tax and Discount
            $order->total_discount = 0;     // $request->total_discount; // Total Discount
            $order->subtotal = $kot->total; // Total after discount
            $order->restrotaxtotal = 0;     // $request->restrotaxtotal; // Total Tax
            $order->restro_tax = 0;         // $request->restro_tax;  // Tax Data
            $order->othertaxtotal = 0;      // $request->othertaxtotal; // Total of other tax
            $order->other_tax = 0;          // $request->other_tax; // Other tax data
            $order->total = $kot->total;    // Total after adding tax and substracting discount
            $order->save();

            $orderPayment = new OrderPayment();
            $orderPayment->user_id = $user->id;
            $orderPayment->order_id = $order->id;
            $orderPayment->customer_id = $order->customer_id;
            $orderPayment->table_id = $request->table_id;
            $orderPayment->order_number = $kot->order_number;
            $orderPayment->restaurant_id = $user->restaurant_id;
            $orderPayment->payment_type = $request->payment_type; // Cash/Online -- Compulsary
            $orderPayment->payment_method = $request->payment_method ?? null; // if ONline - UPI/Card/EMI, etc
            $orderPayment->amount = $order->total;
            $orderPayment->status = "COMPLETED";
            $orderPayment->transaction_id = $request->transaction_id ?? null;
            $orderPayment->payment_details = $request->payment_details ?? null;
            $orderPayment->save();
            
            if(($kot->order_type == "Dine" ) || ($request->orderType == "Dine"))
            {
                $tableActives = TableActive::where("table_id",$kot->table_id)->get();
                if($tableActives->count() > 0)
                {
                    foreach($tableActives as $tableActive)
                    {
                        if($tableActive->split_table_number == null)
                        {
                            $tableActive->status = "Available";
                            $tableActive->delete();
                        }
                        else
                        {
                            if($tableActive->split_table_number == $kot->sub_table_number)
                            {
                                $tableActive->status = "Available";
                                $tableActive->delete();
                            }
                        }
                    }
                }
                else
                {
                    return response()->json(["success"=>false , "message"=>"Table Not Found"]);
                }
            }

            return response()->json(["success"=>true , "message"=>"Order Completed Successfully"]);
        });

        // return response()->json(["success"=>false , "message"=>"Order Not Completed"]);
    }

    public function mergedData($products)
    {
        $formattedData = [];

        foreach ($products as $item) {
            $formattedItem = [
                'item_id' => $item['item_id'],
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => '0.00',
                'tax_percentage' => 0, 
                'subtotal' => $item['product_total'], 
                'totaltax' => 0, 
                'iscancelled' => $item['is_cancelled'],
                'totaldiscount' => 0, 
                'totalwithouttax' => $item['product_total'], 
            ];

            $formattedData[] = $formattedItem;
        }
        return $formattedData;
    }

    public function getActiveTables(Request $request)
    {
        $user = Auth::user();

        // $activeTables = TableActive::where('restaurant_id', $user->restaurant_id)->groupBy('table_number')->get();

        $activeTables = TableActive::whereIn('id', function ($query) use ($user) {
            $query->select(DB::raw('MIN(id)'))
                ->from('table_actives')
                ->where('restaurant_id', $user->restaurant_id)
                ->groupBy('table_number');
        })->latest()->get();

        $activeTables = TableActiveResource::collection($activeTables);

        // $activeTables = $activeTables->map->only("table_number");

        return response()->json(["success" => true, 'data' => $activeTables]);
    }
}
