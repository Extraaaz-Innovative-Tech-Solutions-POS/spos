<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KOT;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        $order = Order::where('restaurant_id',$user->restaurant_id)->get();
        
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
        $order->id   = $request->id   ;
        $order->ispaid = $request->ispaid;
        $order->table_number  = $request->table_number ;
        $order->floor_number = $request->floor_number;
        $order->order_type  = $request->order_type ;
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
        $order->restaurant_id = $restaurant_id;
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
        $kot = KOT::where(['restaurant_id'=>$user->restaurant_id,'table_id' => $table_id])->first();

        if ($kot) {
            $order = $kot ? $kot->kotItems->where('status', 'PENDING') : null;
            return response()->json(["success" => true, "message" => "Orders List", "data" => $order]);
        } else {
            return response()->json(["success" => false, "message" => "No Orders Found"]);
        }

    }

    public function getTableId($section)
    {
        try {
            do {
                $sect_var = "";
                $sect_var = $section == "Dine-In" ? 'DI/' : ($section == "Takeaway" ? "TAK/" : null);
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
                // Check for uniqueness for the current day across all restaurants and locations
            } while (KOT::where('table_id', $table_id)->whereDate('created_at', $currentDate)->exists());
            
            return response()->json($table_id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
