<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Models\Customer;
use App\Models\CustomerSync;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dropDown()
    {
        return Customer::where("company_id", request("company_id"))
            ->when(request()->filled("branch_id"), fn ($q) => $q->where("branch_id", request("branch_id")))
            ->get();
    }

    public function index()
    {
        return Customer::where("company_id", request("company_id"))->with("recent_log")->paginate(request('per_page') ?? 10);
    }

    public function show(Customer $customer)
    {
        return $customer;
    }

    public function tempUpload(Request $request)
    {
        return CustomerSync::create([
            "payload" => $request->all(),
            "company_id" => $request->company_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->filled("profile_picture")) {
                $data['profile_picture'] = $this->processImage("customer/profile_picture");
            }
            $data["date"] = date("Y-m-d");
            Customer::create($data);
            return $this->response("Customer has been registered", null, true);
        } catch (\Throwable $th) {
            return $this->response("Customer cannot be registered", null, false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Customer $customer)
    {
        try {
            $data = $request->validated();

            if ($request->filled("profile_picture")) {
                $data['profile_picture'] = $this->processImage("customer/profile_picture");
            }
            $customer->update($data);
            return $this->response("Customer has been updated", null, true);
        } catch (\Throwable $th) {
            return $this->response("Customer cannot be update", null, false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return $this->response("Customer has been deleted", null, true);
        } catch (\Throwable $th) {
            return $this->response("Customer cannot be delete", null, false);
        }
    }
}
