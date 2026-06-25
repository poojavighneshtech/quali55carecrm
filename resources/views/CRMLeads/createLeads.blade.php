@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    
        <title>CRM Leads</title>
        
    </head>

<body id="page-top">	
	<!-- Page Wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-person-plus"></i> Create Lead</h5>
        </div>

        <div class="card-body p-4">

            {{-- Step Tabs --}}
            <ul class="nav nav-pills mb-4 justify-content-between" id="leadTabs" role="tablist">
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link active w-100" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab">
                        <i class="bi bi-info-circle"></i> Basic Information
                    </button>
                </li>
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link w-100" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab">
                        <i class="bi bi-person"></i> Customer Info
                    </button>
                </li>
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link w-100" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab">
                        <i class="bi bi-geo-alt"></i> Address
                    </button>
                </li>
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link w-100" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab">
                        <i class="bi bi-hdd-network"></i> Equipments
                    </button>
                </li>
            </ul>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="<?php echo url('/');?>/createLeads_Save">
                @csrf

                <div class="tab-content" id="leadTabsContent">
                    {{-- Step 1 --}}
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        <div class="row g-3 p-3 border rounded shadow-sm bg-light">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Order Status<span class="text-danger">*</span></label>
                                <select class="form-select shadow-sm form-control" name="order_status" >
                                    <option value="new">New</option>
                                    <option value="Work In Process">In Process</option>
                                    <option value="hotLead">Hot Lead</option>
                                    <option value="Converted">Convert</option>
                                </select>
                            </div>
                            @error('order_status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Source<span class="text-danger">*</span></label>
                                <!-- <input type="text" name="source" class="form-control shadow-sm" placeholder="Enter source" > -->
                                <select class="form-select shadow-sm form-control" name="source" >
                                    <option value="">Select Source</option>
                                    <option value="Google_Ads">Google Ads</option>
                                    <option value="Web_Chat">Web Chat</option>
                                    <option value="Web_Popup">Web Popup</option>
                                    <option value="Web_Order">Web Order</option>
                                    <option value="Web_Call">Web - Call</option>
                                    <option value="Web_WhatsApp">Web - WhatsApp</option>
                                    <option value="Reference">Reference</option>
                                    <option value="Just_Dial">Just Dial</option>
                                    <option value="Agent">Agent</option>
                                    <option value="Corporate_Booking">Corporate Booking</option>
                                    <option value="Returning_Cust">Returning Cust</option>
                                    <option value="Wellness_Forever">Wellness Forever</option>
                                    <option value="IndiaMart">IndiaMart</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3" id="agentNameDiv" style="display:none;">
                                <label class="form-label fw-semibold">Agent Name<span class="text-danger">*</span></label>
                                <input type="text" name="agentName" class="form-control" placeholder="Enter Agent Name" >
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Payment Mode</label>
                                <select class="form-select form-control shadow-sm" name="payment_mode">
                                    <option>Cash</option>
                                    <option>Online</option>
                                </select>
                            </div>

                            {{-- <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">Order City</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="city" id="city1" value="Mumbai" checked>
                                    <label class="btn btn-outline-primary" for="city1">Mumbai</label>

                                    <input type="radio" class="btn-check" name="city" id="city2" value="Pune">
                                    <label class="btn btn-outline-primary" for="city2">Pune</label>

                                    <input type="radio" class="btn-check" name="city" id="city3" value="Delhi">
                                    <label class="btn btn-outline-primary" for="city3">Delhi</label>

                                    <div class="form-check ms-3 align-self-center">
                                        <input type="checkbox" class="form-check-input" id="outskirts" name="outskirts">
                                        <label class="form-check-label" for="outskirts">Outskirts</label>
                                    </div>
                                </div>
                            </div> --}}
                            <style>
                                .city-option {
                                    padding: 6px 18px;
                                    border: 1px solid #0d6efd;
                                    border-radius: 25px;
                                    cursor: pointer;
                                    font-weight: 500;
                                    transition: 0.2s;
                                    display: inline-block;
                                    margin-right: 12px; /* spacing */
                                }
                                .city-input:checked + .city-option {
                                    background: #0d6efd;
                                    color: white;
                                }
                                .city-option:hover {
                                    background: #0d6efd;
                                    color: white;
                                }
                            </style>

                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">Order City</label>

                                <!-- CENTER ALIGN -->
                                <div class="d-flex align-items-center flex-wrap">

                                    <div>
                                        <input type="radio" class="city-input d-none" name="city" id="city1" value="Mumbai" checked>
                                        <label for="city1" class="city-option">Mumbai</label>
                                    </div>

                                    <div>
                                        <input type="radio" class="city-input d-none" name="city" id="city2" value="Pune">
                                        <label for="city2" class="city-option">Pune</label>
                                    </div>

                                    <div>
                                        <input type="radio" class="city-input d-none" name="city" id="city3" value="Delhi">
                                        <label for="city3" class="city-option">Delhi</label>
                                    </div>

                                    <div>
                                        <input type="radio" class="city-input d-none" name="city" id="city4" value="New Mumbai">
                                        <label for="city4" class="city-option">New Mumbai</label>
                                    </div>

                                    <div>
                                        <input type="radio" class="city-input d-none" name="city" id="city5" value="Thane">
                                        <label for="city5" class="city-option">Thane</label>
                                    </div>

                                    <div class="form-check ms-3 mt-1">
                                        <input type="checkbox" class="form-check-input" id="outskirts" name="outskirts" value="1">
                                        <label class="form-check-label fw-semibold" for="outskirts">Outskirts</label>
                                    </div>

                                </div>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Follow Up Date</label>
                                <input type="date" name="follow_up" class="form-control shadow-sm">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Remark</label>
                                <textarea class="form-control shadow-sm" name="remark" rows="2" placeholder="Enter remark..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        <div class="p-3 border rounded shadow-sm bg-light">

                            {{-- Customer Details --}}
                            <div class="border rounded p-3 mb-4 bg-white">
                                <h6 class="text-primary fw-bold mb-3">Customer Details</h6>

                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Search Contact No.</label>
                                        <input type="number" name="search_contact" id="search_contact" class="form-control" placeholder="Mobile No" oninput="this.value = this.value.slice(0,10)">
                                    </div>

                                    <div class="text-end mt-4">
                                        <button type="button" id="searchBtn" class="btn btn-success px-4" disabled>
                                            Search
                                        </button>
                                    </div>
                                </div>



                                <hr>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Name<span class="text-danger">*</span></label>
                                        <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Enter Name" >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Mobile Number<span class="text-danger">*</span></label>
                                        <input type="text" name="contact_no" id="contact_no" class="form-control" placeholder="Enter Mobile Number" >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" id="customer_email" class="form-control" placeholder="Enter Email">
                                    </div>
                                </div>
                            </div>

                            {{-- Patient Details --}}
                            <div class="border rounded p-3 bg-white">
                                <h6 class="text-primary fw-bold mb-3">Patient Details</h6>

                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">Patient Name</label>
                                        <input type="text" name="patient_name" id="patient_name" class="form-control" placeholder="Enter Patient Name">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold d-block">Gender</label>
                                        <div class="btn-group" role="group" aria-label="Gender">
                                            <input type="radio" class="btn-check" name="gender" id="male" value="Male" checked>
                                            <label class="btn btn-outline-primary" for="male">Male</label>

                                            <input type="radio" class="btn-check" name="gender" id="female" value="Female">
                                            <label class="btn btn-outline-primary" for="female">Female</label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Age</label>
                                        <input type="number" name="age" id="age" class="form-control" placeholder="Age">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Step 3 --}}
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <div class="row p-3 border rounded shadow-sm bg-light">
                            {{-- Shipping Address --}}
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-white shadow-sm h-100">
                                    <h6 class="text-primary fw-bold mb-3">Shipping Address</h6>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="pickup_store" name="pickup_store" value="1">
                                        <label class="form-check-label fw-semibold" for="pickup_store">
                                            Pickup from our store..
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Search Address</label>
                                        <input type="text" name="ship_search_address" id="ship_search_address" class="form-control shadow-sm"
                                            placeholder="Delivery location/landmark">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Flat/Apt No, Building No <span class="text-danger">*</span></label>
                                        <input type="text" name="ship_flat" id="ship_flat" class="form-control shadow-sm" >
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Landmark/Area <span class="text-danger">*</span></label>
                                            <input type="text" name="ship_landmark" id="ship_landmark" class="form-control shadow-sm" >
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                                            <input type="text" name="ship_pincode" id="ship_pincode" class="form-control shadow-sm" >
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">State</label>
                                            <select name="ship_state" id="ship_state" class="form-select shadow-sm form-control">
                                                <option value="Maharashtra">Maharashtra</option>
                                                <option value="Gujarat">Gujarat</option>
                                                <option value="Delhi">Delhi</option>
                                                <option value="Karnataka">Karnataka</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">City</label>
                                            <select name="ship_city" id="ship_city" class="form-select shadow-sm form-control">
                                                <option>Select City</option>
                                                <option value="Mumbai">Mumbai</option>
                                                <option value="Pune">Pune</option>
                                                <option value="Nagpur">Nagpur</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Contact Person Name</label>
                                            <input type="text" name="ship_contact_person" id="ship_contact_person" class="form-control shadow-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Contact No</label>
                                            <input type="text" name="ship_contact_no" id="ship_contact_no" class="form-control shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Billing Address --}}
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-white shadow-sm h-100">
                                    <h6 class="text-primary fw-bold mb-3">Billing Address</h6>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="same_as_shipping" name="same_as_shipping" value="1">
                                        <label class="form-check-label fw-semibold" for="same_as_shipping">
                                            Same as Shipping..
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Search Address</label>
                                        <input type="text" name="bill_search_address" id="bill_search_address" class="form-control shadow-sm"
                                            placeholder="Delivery location/landmark">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Flat/Apt No, Building No <span class="text-danger">*</span></label>
                                        <input type="text" name="bill_flat" id="bill_flat" class="form-control shadow-sm" >
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Landmark/Area <span class="text-danger">*</span></label>
                                            <input type="text" name="bill_landmark" id="bill_landmark" class="form-control shadow-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                                            <input type="number" name="bill_pincode" id="bill_pincode" class="form-control shadow-sm" >
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">State</label>
                                            <select name="bill_state" id="bill_state" class="form-select shadow-sm form-control">
                                                <option value="Maharashtra">Maharashtra</option>
                                                <option value="Gujarat">Gujarat</option>
                                                <option value="Delhi">Delhi</option>
                                                <option value="Karnataka">Karnataka</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">City</label>
                                            <select name="bill_city" id="bill_city" class="form-select shadow-sm form-control">
                                                <option>Select City</option>
                                                <option value="Mumbai">Mumbai</option>
                                                <option value="Pune">Pune</option>
                                                <option value="Nagpur">Nagpur</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Contact Person Name</label>
                                            <input type="text" name="bill_contact_person" id="bill_contact_person" class="form-control shadow-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Contact No</label>
                                            <input type="text" name="bill_contact_no" id="bill_contact_no" class="form-control shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Step 4 --}}
                    <div class="tab-pane fade" id="step4" role="tabpanel">
                        <div class="row g-3 p-3 border rounded shadow-sm bg-light">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Equipments</label>

                                <div class="card shadow-sm p-3">
                                    <div class="row g-3 align-items-end">
                                        <!-- Billing Type -->
                                        <div class="col-md-3">
                                            <label class="form-label">Billing Type</label>
                                            <select class="form-select form-control" name="billing_type" id="billing_type">
                                                <option value="">Select</option>
                                                <option value="Rental">RENT</option>
                                                <option value="Sale">SALE</option>
                                            </select>
                                        </div>

                                        <!-- Period -->
                                        <div class="col-md-3 period-section">
                                            <label class="form-label">Period</label>
                                            <select class="form-select form-control" name="period">
                                                <!-- <option value="1 week">1 week</option> -->
                                                <option value="1 month">1 month</option>
                                                <option value="3 month">3 month</option>
                                                <option value="6 month">6 month</option>
                                                <option value="1 year">1 year</option>
                                            </select>
                                        </div>

                                        <!-- Quantity -->
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity</label>
                                            <div class="input-group">
                                                <button class="btn btn-outline-danger" type="button" id="minusBtn">-</button>
                                                <input type="number" class="form-control text-center" name="quantity" value="1" min="1" id="quantityInput">
                                                <button class="btn btn-outline-success" type="button" id="plusBtn">+</button>
                                            </div>
                                        </div>

                                        <!-- Product -->
                                        <!-- <div class="col-md-3">
                                            <label class="form-label">Product</label>
                                            <select class="selectpicker form-control form-select" data-live-search="true" id="product" name="product" title="Search Product">
                                                @foreach ($products as $product)
                                                    <option value="{{ $product['id'] }}">{{ $product['product_name'] }}</option>
                                                @endforeach
                                             </select>
                                        </div> -->

                                        <div class="col-md-3">
                                            <label class="form-label">Product</label>
                                            <select class="selectpicker form-control form-select" 
                                                    data-live-search="true" 
                                                    id="product" 
                                                    name="product" 
                                                    title="Search Product">
                                            </select>
                                        </div>

                                        <!-- Add Button -->
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-success mt-3 w-100" id="addItemBtn">
                                                Add
                                            </button>
                                        </div>
                                    </div>

                                    <table class="table table-bordered mt-3" id="equipmentTable">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Billing Type</th>
                                                <th>Period</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Deposit</th>
                                                <th>Transport</th>
                                                <th>Discount %</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>


                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="text-end mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-danger px-4">Cancel</a>
                    <button type="submit" class="btn btn-success px-4">Save</button>
                </div>
            </form>
        </div>


        <div class="modal fade" id="editPriceModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Price</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
                    </div>

                    <div class="modal-body">
                        <label>Price Amount</label>
                        <input type="number" class="form-control" id="modalPriceValue">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="savePriceBtn">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDepositModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Deposit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
                    </div>

                    <div class="modal-body">
                        <label>Deposit Amount</label>
                        <input type="number" class="form-control" id="modalDepositValue">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="saveDepositBtn">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editTransportModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Transport Cost</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
                    </div>

                    <div class="modal-body">
                        <label>Transport Amount</label>
                        <input type="number"
                            class="form-control"
                            id="modalTransportValue">
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button"
                                class="btn btn-success"
                                id="saveTransportBtn">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDiscountModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Discount</h5>
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">x</button>
                    </div>

                    <div class="modal-body">
                        <label>Discount Amount</label>
                        <input type="number"
                            class="form-control"
                            id="modalDiscountValue"
                            min="0">
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button"
                                class="btn btn-success"
                                id="saveDiscountBtn">
                            Save
                        </button>
                    </div>

                </div>
            </div>
        </div>





    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
document.getElementById('same_as_shipping').addEventListener('change', function() {
    const checked = this.checked;
    const fields = ['flat', 'landmark', 'pincode', 'state', 'city', 'contact_person', 'contact_no'];

    fields.forEach(field => {
        const shipField = document.querySelector(`[name="ship_${field}"]`);
        const billField = document.querySelector(`[name="bill_${field}"]`);
        if (shipField && billField) {
            if (checked) billField.value = shipField.value;
            else billField.value = '';
        }
    });
});
</script>
<script>
    document.getElementById("plusBtn").onclick = function() {
        let qty = document.getElementById("quantityInput");
        qty.value = parseInt(qty.value) + 1;
    };
    document.getElementById("minusBtn").onclick = function() {
        let qty = document.getElementById("quantityInput");
        if (qty.value > 1) qty.value = parseInt(qty.value) - 1;
    };
</script>
<script>
    $('#billing_type').on('change', function () {

        var billingType = $(this).val();

        if (billingType === "Sale") {
            $(".period-section").hide();   // Hide Period
        } else {
            $(".period-section").show();   // Show Period
        }

        $("#product").html("").selectpicker('refresh');

        if (billingType === "") return;

        $.ajax({
            url: "<?php echo url('/'); ?>/crm/get-products-by-type",
            method: "GET",
            data: { type: billingType },
            success: function (response) {

                $("#product").empty();

                if (response.length > 0) {
                    $.each(response, function (index, item) {
                        // $("#product").append(
                        //     '<option value="' + item.id + '">' + item.product_name + '</option>'
                        // );

                        if(billingType == 'Rental'){
                            $("#product").append(
                                '<option value="' + item.id + '"' +
                                ' data-product_rent="' + item.product_rent + '"' +
                                ' data-product_deposite="' + item.product_deposite + '"' +
                                ' data-product_transport_cost="' + item.product_transport_cost + '"' +
                                ' data-min_rent_percentage="' + item.min_rent_percentage + '"' +
                                '>' + item.product_name + '</option>'
                            );
                        }else{
                            $("#product").append(
                                '<option value="' + item.id + '"' +
                                ' data-product_sale_rate="' + item.product_sale_rate + '"' +
                                ' data-product_deposite="' + item.product_deposite + '"' +
                                ' data-product_transport_cost="' + item.product_transport_cost + '"' +
                                ' data-min_rent_percentage="' + item.min_rent_percentage + '"' +
                                '>' + item.product_name + '</option>'
                            );
                        }
                    });
                } else {
                    $("#product").append('<option value="">No Product Found</option>');
                }

                $("#product").selectpicker('refresh');
            }
        });
    });
</script>
<script>
    $(document).ready(function () {

        // ADD button click
        $("#addItemBtn").on("click", function () {

            let billingType = $("#billing_type").val();
            let qty = $("#quantityInput").val();

            let product = $("#product option:selected");

            if (!billingType || product.val() === "") {
                // Swal.fire({
                //     title: "Missing Fields!",
                //     text: "Please select Billing Type and Product",
                //     icon: "warning",
                //     confirmButtonText: "OK"
                // });
                return;
            }


            let productName = product.text();

            // 🔥 Main fix: Rental vs Sale
            let price = 0;

            let periodText = $("select[name='period']").val();

            let period = 0;

            if (periodText) {
                if (periodText.includes('month')) {
                    period = parseInt(periodText);   // "1 month" → 1, "3 month" → 3
                } 
                else if (periodText.includes('year')) {
                    period = parseInt(periodText) * 12; // "1 year" → 12
                }
            }
            
            if (billingType === "Rental") {
                price = parseFloat(product.data("product_rent")) || 0;
            } else {
                price = parseFloat(product.data("product_sale_rate")) || 0;
                periodText = "-";   // <-- Sale me periodText nahi hoga
            }

            let deposit = parseFloat(product.data("product_deposite")) || 0;
            let transport = parseFloat(product.data("product_transport_cost")) || 0;
            let discount = parseFloat(product.data("min_rent_percentage")) || 0;

            // 🔥 Price × Qty x period
            let totalPrice = price * qty * period;

            // 🔥 Discount amount (percentage)
            let discountAmount = (totalPrice * discount) / 100;

            let total = (price * qty * period) + deposit + transport - discountAmount;

            // let row = `
            //     <tr>
            //         <td>${productName}</td>
            //         <td>${billingType}</td>
            //         <td>${period}</td>
            //         <td>
            //             <button class="btn btn-danger btn-sm qty-minus">-</button>
            //             <span class="mx-2 qty-val">${qty}</span>
            //             <button class="btn btn-success btn-sm qty-plus">+</button>
            //         </td>
            //         <td>₹${totalPrice.toLocaleString()}</td>
            //         <td>₹${deposit.toLocaleString()}</td>
            //         <td>₹${transport.toLocaleString()}</td>
            //         <td>₹${discount.toLocaleString()}</td>
            //         <td class="total-val">₹${total.toLocaleString()}</td>
            //         <td>
            //             <button class="btn btn-danger btn-sm delete-row">
            //                 <i class="bi bi-trash"></i>
            //             </button>
            //         </td>
            //     </tr>
            // `;

            let row = `
                <tr>
                    <td>${productName}
                        <input type="hidden" name="items[product_id][]" value="${product.val()}">
                    </td>

                    <td>${billingType}
                        <input type="hidden" name="items[billing_type][]" value="${billingType}">
                    </td>

                    <td>${periodText}
                        <input type="hidden" name="items[period][]" value="${periodText}">
                    </td>

                    <td>
                        <span class="mx-2 qty-val">${qty}</span>

                        <input type="hidden" class="qty-input" name="items[qty][]" value="${qty}">
                    </td>

                    <td>
                        ₹<span class="price-text">${totalPrice.toLocaleString()}</span>
                        <input type="hidden" class="price-input" name="items[price][]" value="${price}">
                        <button type="button" class="btn btn-success btn-sm rounded-0 edit-price">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>

                    <td>
                        ₹<span class="deposit-text">${deposit.toLocaleString()}</span>
                        <input type="hidden" class="deposit-input" name="items[deposit][]" value="${deposit}">
                        <button type="button" class="btn btn-success btn-sm rounded-0 edit-deposit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>

                    <td>
                        ₹<span class="transport-text">${transport.toLocaleString()}</span>
                        <input type="hidden" class="transport-input" name="items[transport][]" value="${transport}">
                        <button type="button" class="btn btn-success btn-sm rounded-0 edit-transport">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>


                    <td>
                        <span class="discount-text">${discount.toLocaleString()}</span>%
                        <input type="hidden" class="discount-input" name="items[discount][]" value="${discount}">
                        <button type="button" class="btn btn-success btn-sm rounded-0 edit-discount">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>


                    <td class="total-val">₹${total.toLocaleString()}
                        <input type="hidden" class="total-input" name="items[total][]" value="${total}">
                    </td>

                    <td>
                        <button class="btn btn-danger btn-sm delete-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;


            $("#equipmentTable tbody").append(row);
        });


        // Change price start
        let currentPriceRow = null;

        // Edit Price click
        $(document).on('click', '.edit-price', function () {

            currentPriceRow = $(this).closest('td');

            let currentPrice = currentPriceRow
                .find('.price-input')
                .val();

            $('#modalPriceValue').val(currentPrice);
            $('#editPriceModal').modal('show');
        });


        // Save Price
        $(document).on('click', '#savePriceBtn', function () {

            let newPrice = parseFloat($('#modalPriceValue').val());

            if (isNaN(newPrice) || newPrice < 0) {
                alert('Please enter valid price');
                return;
            }

            // hidden input
            currentPriceRow.find('.price-input').val(newPrice);

            // UI text
            currentPriceRow.find('.price-text')
                .text(newPrice.toLocaleString('en-IN'));

            // 🔥 recalc total
            recalculateRow(currentPriceRow.closest('tr'));

            $('#editPriceModal').modal('hide');
            this.blur();
        });

        // Change price end


        

        // Change deposit start
        let currentDepositRow = null;

        // Edit
        $(document).on('click', '.edit-deposit', function () {
            currentDepositRow = $(this).closest('td');

            let currentDeposit = currentDepositRow.find('.deposit-input').val();
            $('#modalDepositValue').val(currentDeposit);
            $('#editDepositModal').modal('show');
        });

        // Save
        $(document).on('click', '#saveDepositBtn', function () {

            let newDeposit = $('#modalDepositValue').val();

            if (newDeposit === '' || isNaN(newDeposit) || newDeposit < 0) {
                alert('Please enter valid deposit amount');
                return;
            }

            currentDepositRow.find('.deposit-input').val(newDeposit);
            currentDepositRow.find('.deposit-text')
                .text(parseInt(newDeposit).toLocaleString('en-IN'));

            recalculateRow(currentDepositRow.closest('tr'));

            this.blur();
            $('#editDepositModal').modal('hide');
        });
        // Change deposit end

        // Change Transport start
        let currentTransportRow = null;

        // Edit Transport
        $(document).on('click', '.edit-transport', function () {

            currentTransportRow = $(this).closest('td');

            let currentTransport = currentTransportRow
                .find('.transport-input')
                .val();

            $('#modalTransportValue').val(currentTransport);
            $('#editTransportModal').modal('show');
        });


        // Save Transport
        $(document).on('click', '#saveTransportBtn', function () {

            let newTransport = $('#modalTransportValue').val();

            if (newTransport === '' || isNaN(newTransport) || newTransport < 0) {
                alert('Please enter valid transport amount');
                return;
            }

            // update hidden input
            currentTransportRow.find('.transport-input').val(newTransport);

            // update UI
            currentTransportRow.find('.transport-text')
                .text(parseInt(newTransport).toLocaleString('en-IN'));

            // 🔥 recalc total
            recalculateRow(currentTransportRow.closest('tr'));

            this.blur();
            $('#editTransportModal').modal('hide');
        });
        // Change Transport end

        let currentDiscountCell = null;

        // Open discount modal
        $(document).on('click', '.edit-discount', function () {

            currentDiscountCell = $(this).closest('td');

            let discount = currentDiscountCell
                .find('.discount-input')
                .val();

            $('#modalDiscountValue').val(discount);
            $('#editDiscountModal').modal('show');
        });

        // Save discount
        $(document).on('click', '#saveDiscountBtn', function () {

            let newDiscount = parseFloat($('#modalDiscountValue').val());

            if (isNaN(newDiscount) || newDiscount < 0) {
                alert('Invalid discount');
                return;
            }

            currentDiscountCell.find('.discount-input').val(newDiscount);
            currentDiscountCell.find('.discount-text').text(newDiscount);

            // 🔥 RECALC REQUIRED
            recalculateRow(currentDiscountCell.closest('tr'));

            $('#editDiscountModal').modal('hide');
        });










        // Delete row
        $(document).on("click", ".delete-row", function () {
            $(this).closest("tr").remove();
        });


        // Quantity + update
        $(document).on("click", ".qty-plus", function () {
            let row = $(this).closest("tr");
            let qtySpan = row.find(".qty-val");
            let qty = parseInt(qtySpan.text()) + 1;

            qtySpan.text(qty);

            updateTotal(row, qty);
        });

        // Quantity - update
        $(document).on("click", ".qty-minus", function () {
            let row = $(this).closest("tr");
            let qtySpan = row.find(".qty-val");
            let qty = Math.max(1, parseInt(qtySpan.text()) - 1);

            qtySpan.text(qty);

            updateTotal(row, qty);
        });

        // function updateTotal(row, qty) {
        //     let price = parseFloat(row.find("td:eq(4)").text().replace(/₹|,/g, ""));
        //     let deposit = parseFloat(row.find("td:eq(5)").text().replace(/₹|,/g, ""));
        //     let transport = parseFloat(row.find("td:eq(6)").text().replace(/₹|,/g, ""));

        //     let total = (price * qty) + deposit + transport;

        //     row.find(".total-val").text("₹" + total.toLocaleString());
        // }

    });

</script>

<script>

    function recalculateRow(row) {

        let price = parseFloat(row.find('.price-input').val()) || 0;
        let qty = parseInt(row.find('.qty-input').val()) || 1;

        let billingType = row.find('input[name="items[billing_type][]"]').val();
        let periodText = row.find('input[name="items[period][]"]').val();

        let period = 1;

        if (billingType === "Rental" && periodText !== "-") {
            if (periodText.includes('month')) {
                period = parseInt(periodText);
            } else if (periodText.includes('year')) {
                period = parseInt(periodText) * 12;
            }
        }

        let deposit = parseFloat(row.find('.deposit-input').val()) || 0;
        let transport = parseFloat(row.find('.transport-input').val()) || 0;
        let discountPercent = parseFloat(row.find('.discount-input').val()) || 0;

        // 🔥 Base price
        let basePrice = price * qty * period;

        // 🔥 Discount amount
        let discountAmount = (basePrice * discountPercent) / 100;

        let total = basePrice - discountAmount + deposit + transport;

        // UI update
        row.find('.total-val').html(
            `₹${total.toLocaleString('en-IN')}
            <input type="hidden" class="total-input" name="items[total][]" value="${total.toFixed(2)}">`
        );
    }


</script>

<script>
    document.querySelector('select[name="source"]').addEventListener('change', function () {
        const agentDiv = document.getElementById('agentNameDiv');

        if (this.value === 'Agent') {
            agentDiv.style.display = 'block';
        } else {
            agentDiv.style.display = 'none';
        }
    });
</script>

<script>
    const contactInput = document.getElementById('search_contact');
    const searchBtn = document.getElementById('searchBtn');

    contactInput.addEventListener('input', function () {
        const value = this.value.toString();

        if (value.length === 10) {
            searchBtn.disabled = false;
        } else {
            searchBtn.disabled = true;
        }
    });
</script>
<script>
$(document).ready(function () {

    $('#searchBtn').on('click', function () {

        let mobile = $('#search_contact').val();
        
        $.ajax({
            url: "{{url('/')}}/search-lead-by-mobile",
            type: "POST",
            data: {
                mobile: mobile,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {

                if (!res.success) {
                    alert(res.message);
                    $('#customer_name').val('');
                    $('#contact_no').val('');
                    $('#customer_email').val('');
                    $('#patient_name').val('');
                    $('#gender').val('');
                    $('#age').val('');
                    $('#pickup_store').val('');
                    
                    $('#ship_search_address').val('');
                    $('#ship_flat').val('');
                    $('#ship_landmark').val('');
                    $('#ship_pincode').val('');
                    $('#ship_contact_person').val('');
                    $('#ship_contact_no').val('');
                    
                    $('#same_as_shipping').val('');
                    $('#bill_search_address').val('');
                    $('#bill_flat').val('');
                    $('#bill_landmark').val('');
                    $('#bill_pincode').val('');
                    $('#bill_contact_person').val('');
                    $('#bill_contact_no').val('');
                    return;
                }

                $('#customer_name').val(res.data.customer_name);
                $('#contact_no').val(res.data.contact_no);
                $('#customer_email').val(res.data.email);
                $('#patient_name').val(res.data.patient_name);

                // res.data.gender = "Male" or "Female"
                if (res.data.gender === 'Female') {
                    $('#female').prop('checked', true);
                } else if (res.data.gender === 'Male') {
                    $('#male').prop('checked', true);
                }

                $('#age').val(res.data.age);
                
                // res.data.pickup_store = 1 or 0
                if (res.data.pickup_store == 1) {
                    $('#pickup_store').prop('checked', true);
                } else {
                    $('#pickup_store').prop('checked', false);
                }
                
                // =========================
                // Shipping Address Fetch
                // =========================
                $('#ship_search_address').val(res.data.ship_search_address);
                $('#ship_flat').val(res.data.ship_flat);
                $('#ship_landmark').val(res.data.ship_landmark);
                $('#ship_pincode').val(res.data.ship_pincode);
                $('#ship_state').val(res.data.ship_state).trigger('change');
                $('#ship_city').val(res.data.ship_city).trigger('change');
                $('#ship_contact_person').val(res.data.ship_contact_person);
                $('#ship_contact_no').val(res.data.ship_contact_no);

                // res.data.same_as_shipping = 1 or 0
                if (res.data.same_as_shipping == 1) {
                    $('#same_as_shipping').prop('checked', true);
                } else {
                    $('#same_as_shipping').prop('checked', false);
                }

                // =========================
                // Billing Address Fetch
                // =========================
                $('#bill_search_address').val(res.data.bill_search_address);
                $('#bill_flat').val(res.data.bill_flat);
                $('#bill_landmark').val(res.data.bill_landmark);
                $('#bill_pincode').val(res.data.bill_pincode);
                $('#bill_state').val(res.data.bill_state).trigger('change');
                $('#bill_city').val(res.data.bill_city).trigger('change');
                $('#bill_contact_person').val(res.data.bill_contact_person);
                $('#bill_contact_no').val(res.data.bill_contact_no);
            },
            error: function (xhr) {
                console.error(xhr);
                alert('Something went wrong');
            }
        });

    });

});
</script>




{{-- Optional: Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endsection
    
    
    
</body>
</html>