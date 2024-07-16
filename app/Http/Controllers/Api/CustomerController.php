<?php

namespace App\Http\Controllers\Api;

use App\CentralLogics\Helpers as CentralLogicsHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\CustomerAddress;
use App\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    public function address_list(Request $request)
    {
        return response()->json(CustomerAddress::where('user_id', $request->user()->id)->latest()->get(), 200);
    }

    public function info(Request $request)
    {
        $data = $request->user();

        //$data['order_count'] =0;//(integer)$request->user()->orders->count();
        //$data['member_since_days'] =(integer)$request->user()->created_at->diffInDays();
        //unset($data['orders']);
        return response()->json($data, 200);
    }
    public function add_new_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => "Error with the address"], 403);
        }


        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->insert($address);
        return response()->json(['message' => trans('messages.successfully_added')], 200);
    }
    public function update_address(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => CentralLogicsHelpers::error_processor($validator)], 403);
        }
        /*$point = new Point($request->latitude,$request->latitude);
        $zone = Zone::contains('coordinates', $point)->first();
        if(!$zone)
        {
            $errors = [];
            array_push($errors, ['code' => 'coordinates', 'message' => trans('messages.out_of_coverage')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }*/
        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'zone_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->where('user_id', $request->user()->id)->update($address);
        return response()->json(['message' => trans('messages.updated_successfully'), 'zone_id' => 1], 200);
    }

    public function updateInformation(Request $request)
    {

        $user = $request->user();

        $customer = Customer::where('id', $user->id)->firstOrFail();
        $fields = $request->validate([
            'f_name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:customers,email,' . $customer->id,
            'phone' => 'sometimes|required|string|unique:customers,phone,' . $customer->id,
            'password' => 'sometimes|required|string',
        ]);

        // Update customer information if the fields are provided
        if (isset($fields['f_name'])) {
            $customer->f_name = $fields['f_name'];
        }
        if (isset($fields['email'])) {
            $customer->email = $fields['email'];
        }
        if (isset($fields['phone'])) {
            $customer->phone = $fields['phone'];
        }
        if (isset($fields['password'])) {
            $customer->password = bcrypt($fields['password']);
        }

        // Save the updated customer information
        $customer->save();

        // Return a success response
        return response([
            'message' => 'Customer information updated successfully',
            'results' => $customer,
        ], 200);
    }
}
