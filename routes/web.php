
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomAuth;
use App\Http\Controllers\OrderManagement\OrderController;
use App\Http\Controllers\CRMLeads\CRMLeadController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(session('username')!=null)
    {
        return redirect('dashboard');
    }
    else
    {
        return view('Authentication/admin_login');
    }
});

Route::get('/about-us', function (){
    return view('about-us');
});
Route::get('/privacy-policy', function (){
    return view('privacy-policy');
});

Route::get('/header', function ()
{
    return view('header');  
});
//-------------------------Dashoard -------------------//
Route::get('/dashboard', [App\Http\Controllers\Dashboard\DashboardController::class, 'dashboard'])->middleware(CustomAuth::class)->name('dashboard');
Route::get('/order_count/{filter}', [App\Http\Controllers\Dashboard\DashboardController::class, 'ordersCount'])->middleware(CustomAuth::class)->name('ordersCount');
// Route::get('/lead_count/{filter}', [App\Http\Controllers\Dashboard\DashboardController::class, 'leadsCount'])->middleware(CustomAuth::class)->name('leadsCount');
Route::get('/lead_count/{filter}/{start_date}/{end_date}/{lead_owner}/{state}', [App\Http\Controllers\Dashboard\DashboardController::class, 'leadsCount'])->middleware(CustomAuth::class)->name('leadsCount');
Route::get('/q5c_equipment_count/{filter}', [App\Http\Controllers\Dashboard\DashboardController::class, 'q5cEquipmentCount'])->middleware(CustomAuth::class)->name('q5cEquipmentCount');
Route::get('/vdr_equipment_count/{filter}', [App\Http\Controllers\Dashboard\DashboardController::class, 'vdrEquipmentCount'])->middleware(CustomAuth::class)->name('vdrEquipmentCount');
//Dashboard fullfillmennt
Route::get('/dashboard_fullfillment', [App\Http\Controllers\Authentication\AuthController::class, 'dashboard_fullfillment'])->middleware(CustomAuth::class)->name('dashboard_fullfillment');
//Dashboard presales
Route::get('/dashboard_presales', [App\Http\Controllers\Authentication\AuthController::class, 'dashboard_presales'])->middleware(CustomAuth::class)->name('dashboard_presales');

/*-----------------------------------Authentication Routes-------------------------------------------*/
//Login validation and authentication
Route::post('/validate_login', [App\Http\Controllers\Authentication\AuthController::class, 'validate_login'])->name('Validate Login');
//Reload Captcha
Route::get('/reload_captcha', [App\Http\Controllers\Authentication\AuthController::class, 'reloadCaptcha']);
//Logout
Route::get('/logout', [App\Http\Controllers\Authentication\AuthController::class, 'logout'])->middleware(CustomAuth::class)->name('logout');

Route::get('/notifications/{count}', [App\Http\Controllers\Authentication\AuthController::class, 'notification'])->middleware(CustomAuth::class)->name('notification');

Route::get('/getCount', [App\Http\Controllers\Authentication\AuthController::class, 'getCount'])->middleware(CustomAuth::class)->name('getCount');
//Dashboard
// Route::get('/dashboard', [App\Http\Controllers\Authentication\AuthController::class, 'dashboard'])->middleware(CustomAuth::class)->name('Dashboard');

/*-----------------------------------Vendor Management Routes-------------------------------------------*/
//Pending Vendor view
Route::get('/pending_vendors', [App\Http\Controllers\VendorManagement\VendorController::class, 'pending_vendors'])->middleware(CustomAuth::class)->name('pending_vendors');
//rejected Vendor view
Route::get('/rejected_vendors', [App\Http\Controllers\VendorManagement\VendorController::class, 'rejected_vendors'])->middleware(CustomAuth::class)->name('rejected_vendors');
//approve Vendor view
Route::get('/approved_vendors', [App\Http\Controllers\VendorManagement\VendorController::class, 'approved_vendors'])->middleware(CustomAuth::class)->name('approved_vendors');
//requested Vendor view
Route::get('/requested_vendors', [App\Http\Controllers\VendorManagement\VendorController::class, 'requested_vendors'])->middleware(CustomAuth::class)->name('requested_vendors');
//Vendor details view
Route::get('/vendor_details/{id}', [App\Http\Controllers\VendorManagement\VendorController::class, 'vendor_details'])->middleware(CustomAuth::class)->name('vendor_details');
//Crud Operation on vendor details updates...
Route::post('/share_info', [App\Http\Controllers\VendorManagement\VendorController::class, 'share_info'])->middleware(CustomAuth::class)->name('share_info');

/*-----------------------------------Product Management Routes-------------------------------------------*/
// add new product to catlg...........(view)
Route::get('/add_new_product', [App\Http\Controllers\ProductManagement\ProductController::class, 'add_new_product'])->middleware(CustomAuth::class)->name('add_new_product');
// add new product to catlg...........(Insert in database)
Route::post('/add_new_product', [App\Http\Controllers\ProductManagement\ProductController::class, 'add_new_product'])->middleware(CustomAuth::class)->name('add_new_product');
// view all pending requests of vendor products...................
Route::get('/product_request', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_request'])->middleware(CustomAuth::class)->name('product_request');
// view all pending requests of vendor products with city ...................
Route::get('/product_request/{city}', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_request_citywise'])->middleware(CustomAuth::class)->name('product_request');
// view all aproved vendor product requests ..................
Route::get('/product_approved_rent', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_approved_rent'])->middleware(CustomAuth::class)->name('product_approved_rent');
// view all aproved vendor product requests with city ..................
Route::get('/product_approved_rent/{city}', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_approved_rent_citywise'])->middleware(CustomAuth::class)->name('product_approved_rent');
//view all rejected vendor product requests...........................
Route::get('/product_rejected_rent', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_rejected_rent'])->middleware(CustomAuth::class)->name('product_rejected_rent');
//view all rejected vendor product requests with city ...........................
Route::get('/product_rejected_rent/{city}', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_rejected_rent_citywise'])->middleware(CustomAuth::class)->name('product_rejected_rent');
//view all requested vendor product requests...........................
Route::get('/product_requested_rent', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_requested_rent'])->middleware(CustomAuth::class)->name('product_requested_rent');
//view all requested vendor product requests with city...........................
Route::get('/product_requested_rent/{city}', [App\Http\Controllers\ProductManagement\ProductController::class, 'product_requested_rent_citywise'])->middleware(CustomAuth::class)->name('product_requested_rent');
//view detailed rent list of vendors...........................
Route::get('/detailed_rent_list', [App\Http\Controllers\ProductManagement\ProductController::class, 'detailed_rent_list'])->middleware(CustomAuth::class)->name('detailed_rent_list');
//update product status to approve or reject for both pending and requested products,.......
Route::post('/update_product_status', [App\Http\Controllers\ProductManagement\ProductController::class, 'update_product_status'])->middleware(CustomAuth::class)->name('update_product_status');
//Fetch all data of given vendor_id
Route::get('/fetch_all_vendor_details/{vendor_id}', [App\Http\Controllers\ProductManagement\ProductController::class, 'fetch_all_vendor_details'])->middleware(CustomAuth::class)->name('fetch_all_vendor_details');
//View Master Products...
Route::get('/view_master_products', [App\Http\Controllers\ProductManagement\ProductController::class, 'view_master_products'])->middleware(CustomAuth::class)->name('view_master_products');

/*-----------------------------------Master Product Management routes-------------------------------------------*/
// add new product to catlg...........(VIewwww)
Route::get('/add_new_product', [App\Http\Controllers\MasterProductManagement\MasterProductController::class, 'add_new_product'])->middleware(CustomAuth::class)->name('add_new_product');
// add new product to catlg...........(Insert in database)
Route::post('/add_new_product', [App\Http\Controllers\MasterProductManagement\MasterProductController::class, 'add_new_product'])->middleware(CustomAuth::class)->name('add_new_product');
//View Master Products...
Route::get('/view_master_products', [App\Http\Controllers\MasterProductManagement\MasterProductController::class, 'view_master_products'])->middleware(CustomAuth::class)->name('view_master_products');
//edit master product
Route::get('/edit_master_product/{product_id}', [App\Http\Controllers\MasterProductManagement\MasterProductController::class, 'edit_master_product'])->middleware(CustomAuth::class)->name('edit_master_product');
//update master product
Route::post('/edit_master_product/{product_id}', [App\Http\Controllers\MasterProductManagement\MasterProductController::class, 'edit_master_product'])->middleware(CustomAuth::class)->name('edit_master_product');
//view product details
Route::get('/view_product_details/{product_id}', [App\Http\Controllers\MasterProductManagement\MasterProductController::class, 'view_product_details'])->middleware(CustomAuth::class)->name('view_product_details');


/*-----------------------------------Order Management Routes-------------------------------------------*/
Route::get('/order_converted_leads', [App\Http\Controllers\OrderManagement\OrderController::class, 'viewAllLeads'])->middleware(CustomAuth::class)->name('All Converted Leads');
//View Lead Readonly
Route::get('/order_view_lead/{customer_id}/{id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'order_view_lead'])->middleware(CustomAuth::class)->name('order_view_lead');
// Assign Order
// ---- Original Route...
// Route::get('/assign_vendor', [App\Http\Controllers\OrderManagement\OrderController::class, 'assign_vendor'])->middleware(CustomAuth::class)->name('assign_vendor');
// ---- Working on new Route...
Route::get('/assign_vendor', [App\Http\Controllers\OrderManagement\OrderController::class, 'vendorAssignment'])->middleware(CustomAuth::class)->name('assign_vendor');

// Assign Order quantity wise
Route::post('/assign_vendor_exp', [App\Http\Controllers\OrderManagement\OrderController::class, 'assign_vendor_exp'])->middleware(CustomAuth::class)->name('assign_vendor_exp');

//Select vendor
//Route::get('/select_vendor/{slct_vdr_id}/{equipment}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_vendor'])->middleware(CustomAuth::class)->name('select_vendor');
Route::get('/select_vendor/{slct_vdr_id}/{equipment}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_vendor'])->middleware(CustomAuth::class)->name('select_vendor');
//get vendor
//---individual_vendor---//
Route::get('/individual_vendor/{equipment}/{sale_rental}', [App\Http\Controllers\OrderManagement\OrderController::class, 'individual_vendor'])->middleware(CustomAuth::class)->name('individual_vendor');
/////
Route::post('/get_vendor', [App\Http\Controllers\OrderManagement\OrderController::class, 'get_vendor'])->middleware(CustomAuth::class)->name('get_vendor');

//Select Vendor
// Route::get('/select_inventory/{product_id}/{warehouse_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_inventory'])->middleware(CustomAuth::class)->name('select_inventory');
Route::get('/select_inventory/{vendor_id}/{warehouse_id}/{brand_id}/{product_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_inventory'])->middleware(CustomAuth::class)->name('select_inventory');
//Select vendor All
Route::post('/select_vendor_all', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_vendor_all'])->middleware(CustomAuth::class)->name('select_vendor_all');
//assign vendor by script
Route::get('/assign_vendor_byscript/{customer_id}/{lead_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'assign_vendor_byscript'])->middleware(CustomAuth::class)->name('assign_vendor_byscript');
//generate order
Route::post('/generate_order', [App\Http\Controllers\OrderManagement\OrderController::class, 'generate_order'])->middleware(CustomAuth::class)->name('generate_order');
//order details SHOW
// Route::get('/order_details', [App\Http\Controllers\OrderManagement\OrderController::class, 'order_details'])->middleware(CustomAuth::class)->name('order_details');
Route::get('/order_details/{order_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'order_details'])->middleware(CustomAuth::class)->name('order_details');
// Filter viewAllLeads Table by Today/Yesterday/Past 3 Days/A Week/A Month.........
//Route::get('/filterLeads/{filter_by}', [App\Http\Controllers\Leads\LeadController::class, 'filterLeads'])->middleware(CustomAuth::class)->name('filterLeads');

//view all aprooved orders 
Route::get('/approved_orders', [App\Http\Controllers\OrderManagement\OrderController::class, 'viewApprovedOrders'])->middleware(CustomAuth::class)->name('approved_orders');
//view all rejected orders 
Route::get('/rejected_orders', [App\Http\Controllers\OrderManagement\OrderController::class, 'viewRejectedOrders'])->middleware(CustomAuth::class)->name('rejected_orders');
//view order info of approved orders 
Route::get('/approved_order_info/{order_id}/{order_type}', [App\Http\Controllers\OrderManagement\OrderController::class, 'viewApprovedOrderInfo'])->middleware(CustomAuth::class)->name('approved_order_info');
//view order info of rejected orders 
Route::get('/rejected_order_info/{order_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'viewRejectedOrderInfo'])->middleware(CustomAuth::class)->name('rejected_order_info');
//delete products for order
Route::get('/delete_order_product/{order_details_id}/{product_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'DeleteOrderProduct'])->middleware(CustomAuth::class)->name('delete_order_product');

//------filter order leads-----------//
Route::get('/filterOrderLeads/{filter_by}', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterOrderLeads'])->middleware(CustomAuth::class)->name('filterOrderLeads');
Route::post('/filterOrderLeads', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterOrderLeadsDWS'])->middleware(CustomAuth::class)->name('filterOrderLeadsDWS');
//order details SHOW
Route::get('/pending_for_vendor_approval', [App\Http\Controllers\OrderManagement\OrderController::class, 'pending_for_vendor_approval'])->middleware(CustomAuth::class)->name('pending_for_vendor_approval');
//------Filter Pending for vendor approval-----------//
Route::get('/filterPendingVendorApproval/{filter_by}', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterPendingVendorApproval'])->middleware(CustomAuth::class)->name('filterPendingVendorApproval');
//view pending order details
Route::get('/view_pending_order_details/{order_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'view_pending_order_details'])->middleware(CustomAuth::class)->name('view_pending_order_details');
//view for reassign vendor
Route::get('/reassign_vendor/{order_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'reassign_vendor'])->middleware(CustomAuth::class)->name('reassign_vendor');
// Post reassign vendor 
Route::POST('/reassign_vendor_post', [App\Http\Controllers\OrderManagement\OrderController::class, 'reassign_vendor_post'])->middleware(CustomAuth::class)->name('reassign_vendor_post');
// mobile App Leads Display Route
Route::get('/mobileAppLeads', [App\Http\Controllers\OrderManagement\OrderController::class, 'mobile_app_leads'])->middleware(CustomAuth::class)->name('mobile_app_leads');
//status change
//Route::get('/status_change/{order_id}/{vendor_id}/{vendor_product_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'status_change'])->middleware(CustomAuth::class)->name('status_change');
Route::post('/status_change', [App\Http\Controllers\OrderManagement\OrderController::class, 'status_change'])->middleware(CustomAuth::class)->name('status_change');
//------approve order--------//
Route::post('/approve_orders', [App\Http\Controllers\OrderManagement\OrderController::class, 'approve_orders'])->middleware(CustomAuth::class)->name('approve_orders');
// --------Pending Assignments------- //
Route::get('/pendingAssignment', [App\Http\Controllers\OrderManagement\OrderController::class, 'pendingAssignment'])->middleware(CustomAuth::class)->name('pendingAssignment');
//------filter order leads-----------//
Route::get('/filterPendingAssignment/{filter_by}', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterPendingAssignment'])->middleware(CustomAuth::class)->name('filterPendingAssignment');

Route::post('/filterPendingAssignment', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterPendingAssignmentDWS'])->middleware(CustomAuth::class)->name('filterPendingAssignmentDWS');
//Close Order
// Route::get('/close_delivery/{order_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'close_delivery'])->middleware(CustomAuth::class)->name('close_delivery');

//Close Order
Route::post('/close_delivery', [App\Http\Controllers\OrderManagement\OrderController::class, 'close_delivery'])->middleware(CustomAuth::class)->name('close_delivery');

// Route::post('/close_order_post', [App\Http\Controllers\OrderManagement\OrderController::class, 'close_order'])->middleware(CustomAuth::class)->name('close_order');

Route::post('order-close', [App\Http\Controllers\OrderManagement\OrderController::class, 'close_order'])->middleware(CustomAuth::class)->name('order-close');
Route::get('order-cancel-show-reason', [App\Http\Controllers\OrderManagement\OrderController::class, 'orderCancelReason'])->middleware(CustomAuth::class)->name('order-cancel-show-reason');



Route::post('/date_filter_order_mgmt/{type}', [App\Http\Controllers\OrderManagement\OrderController::class, 'DateFilter'])->middleware(CustomAuth::class)->name('date_filter_orders');
Route::get('/filterApprovedOrders/{filter_by}', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterApprovedOrders'])->middleware(CustomAuth::class)->name('filterApprovedOrders');

Route::post('/filterApprovedOrders', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterApprovedOrdersDWS'])->middleware(CustomAuth::class)->name('filterApprovedOrdersDWS');

Route::get('/filterRejectedOrders/{filter_by}', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterRejectedOrders'])->middleware(CustomAuth::class)->name('filterRejectedOrders');

Route::post('/filterRejectedOrders', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterRejectedOrdersDWS'])->middleware(CustomAuth::class)->name('filterRejectedOrdersDWS');

Route::get('/filterPendingVendorApproval/{filter_by}', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterPendingVendorApproval'])->middleware(CustomAuth::class)->name('filterPendingVendorApproval');

Route::post('/filterPendingVendorApproval', [App\Http\Controllers\OrderManagement\OrderController::class, 'filterPendingVendorApprovalDWS'])->middleware(CustomAuth::class)->name('filterPendingVendorApprovalDWS');
//name filter
Route::post('/order_mgmt_filter_post', [App\Http\Controllers\OrderManagement\OrderController::class, 'NameFilter'])->middleware(CustomAuth::class)->name('order_mgmt_filter_post');

//Route::get('/orders_view_all_orders', [App\Http\Controllers\OrderManagement\OrderController::class, 'ViewAllOrders'])->middleware(CustomAuth::class)->name('orders_view_all_orders');
Route::get('/viewall_order_mgmt_filter', [App\Http\Controllers\OrderManagement\OrderController::class, 'ViewAllOrdersFilter'])->middleware(CustomAuth::class)->name('viewall_order_mgmt_filter');
Route::get('/viewall_order_export', [App\Http\Controllers\OrderManagement\OrderController::class, 'ViewAllOrdersExport'])->middleware(CustomAuth::class)->name('viewall_order_export');

//order_mgmt_view_all lead
Route::get('/order_mgmt_all_leads', [App\Http\Controllers\OrderManagement\OrderController::class, 'ViewAllLeads_new'])->middleware(CustomAuth::class)->name('order_mgmt_all_leads');

//----------Notifications Pending Assignments------------//
Route::get('/pendingAssignmentsNotify', [App\Http\Controllers\OrderManagement\OrderController::class, 'pendingAssignmentsNotify'])->middleware(CustomAuth::class)->name('pendingAssignmentsNotify');

Route::get('/location_populate', [App\Http\Controllers\OrderManagement\OrderController::class, 'location_populate'])->middleware(CustomAuth::class)->name('location_populate');

/*-----------------------------------Delivery Management Routes-------------------------------------------*/
/* Add Delivery */
Route::get('/AddDelivery', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'AddDelivery'])->middleware(CustomAuth::class)->name('AddDelivery');
/* Add New Delivery insert Route */
Route::post('/AddDelivery', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'AddDelivery'])->middleware(CustomAuth::class)->name('AddDelivery');
/* Modify Delivery View All*/
Route::get('/ModifyDeliveryView', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ModifyDeliveryView'])->middleware(CustomAuth::class)->name('ModifyDeliveryView');
//Delivery Report
Route::get('/deliveryReport', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'deliveryReport'])->middleware(CustomAuth::class)->name('deliveryReport');
//Filter Delivery Report
Route::get('/deliveryReportFilter/{filter_by}/{deliverypickup}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'deliveryReportFilter'])->middleware(CustomAuth::class)->name('deliveryReportFilter');

Route::post('/searchCustomerDelReport', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'searchCustomerDelReport'])->middleware(CustomAuth::class)->name('searchCustomerDelReport');

/* Modify Delivery View */
Route::get('/ModifyDelivery/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ModifyDelivery'])->middleware(CustomAuth::class)->name('ModifyDelivery');

Route::get('/modifyDeliveryFilter/{filter_by}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'modifyDeliveryFilter'])->middleware(CustomAuth::class)->name('modifyDeliveryFilter');

Route::post('/modifyDeliveryFilter', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'modifyDeliveryFilterDWS'])->middleware(CustomAuth::class)->name('modifyDeliveryFilterDWS');

/* Modify Delivery insert Route */
Route::post('/ModifyDeliveryPost', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ModifyDeliveryPost'])->middleware(CustomAuth::class)->name('ModifyDeliveryPost');
/* viewAllDeliveries Route */
Route::get('/AllDeliveries', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'AllDeliveries'])->middleware(CustomAuth::class)->name('AllDeliveries');
/* viewCompletedDeliveries Route */
Route::get('/CompletedDeliveries', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'CompletedDeliveries'])->middleware(CustomAuth::class)->name('CompletedDeliveries');
/* viewArchivedDeliveries Route */
Route::get('/ArchivedDeliveries', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ArchivedDeliveries'])->middleware(CustomAuth::class)->name('ArchivedDeliveries');
/* Monthly Delivery Report Route */
Route::get('/MonthlyDeliveryReport', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'MonthlyDeliveryReport'])->middleware(CustomAuth::class)->name('MonthlyDeliveryReport');
/* POST Monthly Delivery Report Route */
Route::POST('/MonthlyDeliveryReport', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'MonthlyDeliveryReport'])->middleware(CustomAuth::class)->name('MonthlyDeliveryReport');
//Confirmed Deliveries Route
Route::get('/confirmed_delivery', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'confirmed_delivery'])->middleware(CustomAuth::class)->name('confirmed_delivery');
Route::get('/assign_deliveryBoy/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'assign_deliveryBoy'])->middleware(CustomAuth::class)->name('assign_deliveryBoy');
//Post assign Delivery Boy 
Route::post('/assign_deliveryBoy', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'assign_deliveryBoy_post'])->middleware(CustomAuth::class)->name('assign_deliveryBoy_post');
//-----filter delivery--------//
Route::get('/filterDeliveryOrder/{filter_by}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'filterDeliveryOrder'])->middleware(CustomAuth::class)->name('filterDeliveryOrder');

Route::post('/filterDeliveryOrder', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'filterDeliveryOrderDWS'])->middleware(CustomAuth::class)->name('filterDeliveryOrderDWS');

//-----filter pickup--------//
Route::get('/filterPickupOrder/{filter_by}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'filterPickupOrder'])->middleware(CustomAuth::class)->name('filterPickupOrder');

Route::post('/filterPickupOrder', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'filterPickupOrderDWS'])->middleware(CustomAuth::class)->name('filterPickupOrderDWS');

//-----filter pickup--------//
Route::get('/filterCollectionOrder/{filter_by}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'filterCollectionOrder'])->middleware(CustomAuth::class)->name('filterCollectionOrder');

Route::post('/filterCollectionOrder', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'filterCollectionOrderDWS'])->middleware(CustomAuth::class)->name('filterCollectionOrderDWS');

// pickup request 
Route::get('/pickup_request', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'pickup_request'])->middleware(CustomAuth::class)->name('pickup_request');
//assign del boy pickup requested order 
Route::get('/assign_pickup_delboy/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'assign_pickup_delboy'])->middleware(CustomAuth::class)->name('assign_pickup_delboy');
//post del boy assign values
Route::post('/assign_pickup_delboy_post', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'assign_pickup_delboy_post'])->middleware(CustomAuth::class)->name('assign_pickup_delboy_post');
// renew request 
Route::get('/renew_request', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'renew_request'])->middleware(CustomAuth::class)->name('renew_request');
//------derl reminder
Route::get('/send_del_reminder/{customer_id}/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'send_del_reminder'])->middleware(CustomAuth::class)->name('send_del_reminder');
//assign del boy collection requested order 
Route::get('/assign_collection_delboy/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'assign_collection_delboy'])->middleware(CustomAuth::class)->name('assign_collection_delboy');
//post del boy assign values
Route::post('/assign_collection_delboy_post/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'assign_collection_delboy_post'])->middleware(CustomAuth::class)->name('assign_collection_delboy_post');
/* Modify Delivery View */
Route::get('/ModifyCollection/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ModifyCollection'])->middleware(CustomAuth::class)->name('ModifyCollection');
/* Modify Delivery View */
Route::post('/ModifyCollection/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ModifyCollection'])->middleware(CustomAuth::class)->name('ModifyCollection');

/* Modify Delivery View */
Route::get('/ModifyPickup/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ModifyPickup'])->middleware(CustomAuth::class)->name('ModifyPickup');
/* Modify Delivery View */
Route::post('/ModifyPickup/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'ModifyPickup'])->middleware(CustomAuth::class)->name('ModifyPickup');

//order feedback form delorders table
Route::get('/order_feedback', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'order_feedback'])->middleware(CustomAuth::class)->name('order_feedback');
//perticular feedback order
Route::get('/perticular_feedback/{order_id}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'perticular_feedback'])->middleware(CustomAuth::class)->name('perticular_feedback');
//----filter order
Route::get('/filterFeedback/{filter_by}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'filterFeedback'])->middleware(CustomAuth::class)->name('filterFeedback');

//date filter for delivery mgmtg
Route::post('/date_filter_delivery_mgmt/{type}', [App\Http\Controllers\DeliveryManagement\DeliveryController::class, 'DateFilter'])->middleware(CustomAuth::class)->name('date_filter_delivery_mgmt');

Route::post('/delivery_upload_image',[App\Http\Controllers\DeliveryManagement\DeliveryController::class,'delivery_upload_image'])->middleware(CustomAuth::class)->name('delivery_upload_image');

/*--------------------------------                      End                     ---------------------------*/

/*-----------------------------------User Management Routes-------------------------------------------*/
//Add New User
Route::get('/add_user',[App\Http\Controllers\UserManagement\UserController::class, 'add_user'])->middleware(CustomAuth::class)->name('add_user');
//Submit new user
Route::post('/add_user',[App\Http\Controllers\UserManagement\UserController::class, 'add_user'])->middleware(CustomAuth::class)->name('add_user');
//Edit  User
Route::get('/edit_user/{uid}',[App\Http\Controllers\UserManagement\UserController::class, 'edit_user'])->middleware(CustomAuth::class)->name('edit_user');
//Edit user
Route::post('/edit_user/{uid}',[App\Http\Controllers\UserManagement\UserController::class, 'edit_user'])->middleware(CustomAuth::class)->name('edit_user');
//view all user
Route::get('/view_all_user',[App\Http\Controllers\UserManagement\UserController::class, 'view_all_user'])->middleware(CustomAuth::class)->name('view_all_user');
// View Customer Profile ...
Route::get('/view_profile', [App\Http\Controllers\UserManagement\UserController::class, 'view_profile'])->middleware(CustomAuth::class)->name('view_profile');

/*------------------------------- Renewal And Pickup ------------------------*/
Route::get('/renewal_pickup',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renewal_pickup'])->middleware(CustomAuth::class)->name('renewal_pickup');
//test renewal pickup for paginate
//Route::get('/renewal_pickup',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'test_renewal_pickup'])->middleware(CustomAuth::class)->name('renewal_pickup');

//Route::get('/renewal_pickup_search',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renewal_pickup_search'])->middleware(CustomAuth::class)->name('renewal_pickup_search');
//Route::post('/renewal_pickup_search',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renewal_pickup_search'])->middleware(CustomAuth::class)->name('renewal_pickup_search');
Route::get('/customer_products/{customer_id}',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'customer_products'])->middleware(CustomAuth::class)->name('customer_products');
Route::post('/order_data',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'order_data'])->middleware(CustomAuth::class)->name('order_data');
//search customer by text field
Route::get('/renew_pick_search/{filter}/{customer_val}',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'SearchCustomer'])->middleware(CustomAuth::class)->name('renew_pick_search');
//search customers by date picker from to date
Route::get('/renew_pick_date_search/{start_date}/{end_date}',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'DateSearch'])->middleware(CustomAuth::class)->name('renew_pick_date_search');

//renewal pickup search order by customer
Route::get('/renewal_pickup/{filter}/{filter_val}',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renewal_pickup_filter'])->middleware(CustomAuth::class)->name('renewal_pickup_filter');
//renewal pickup search by form
// Route::post('/renewal_pickup_search',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renewal_pickup_search'])->middleware(CustomAuth::class)->name('renewal_pickup_search');
Route::any('/renewal_pickup_search',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renewal_pickup_search'])->middleware(CustomAuth::class)->name('renewal_pickup_search_get');

//--------send reminder------//
Route::get('/send_perticular_reminder/{cust_id}/{product_name}/{pickup_date}/{product_rent}',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'AjaxReminder'])->middleware(CustomAuth::class)->name('send_perticular_reminder');
//ajax send reminder
Route::post('/ajax_send_reminder',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'AjaxReminder'])->middleware(CustomAuth::class)->name('ajax_send_reminder');
//
Route::post('/send_reminder',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'send_reminder'])->middleware(CustomAuth::class)->name('send_reminder');
//renewa pikup and sendreminder at e
Route::post('/renewal_pickup_product',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renewal_pickup_product'])->middleware(CustomAuth::class)->name('renewal_pickup_product');  
//pickup order
Route::post('/pickup_order',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'pickup_order'])->middleware(CustomAuth::class)->name('pickup_order');
//renew order
Route::post('/renew_order',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'renew_order'])->middleware(CustomAuth::class)->name('renew_order');
//----add customer comment---------//
Route::get('/add_customer_comment/{user_id}/{customer_id}/{desc}', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'add_customer_comment'])->middleware(CustomAuth::class)->name('add_customer_comment');

//------------Leads_Management-----------------------------------//

Route::get('/view_cust_lead/{customer_id}', [App\Http\Controllers\Leads\LeadController::class, 'view_cust_leads'])->middleware(CustomAuth::class)->name('view_cust_leads');
Auth::routes();
//create New Lead
Route::post('/create_lead', [App\Http\Controllers\Leads\LeadController::class, 'create_lead'])->middleware(CustomAuth::class)->name('create_lead');
// // View Customer Profile ...
Route::get('/view_profile', [App\Http\Controllers\Admin\AdminController::class, 'view_profile'])->middleware(CustomAuth::class)->name('view_profile');
// // To go to create lead file get method used because post not used
Route::get('/create_lead', [App\Http\Controllers\Leads\LeadController::class, 'create_lead'])->middleware(CustomAuth::class)->name('create_lead');

Route::any('assign-lead-user',[App\Http\Controllers\Leads\LeadController::class,'assignLeadUser'])->middleware(CustomAuth::class)->name('assign-lead-user');
//View All Leads
Route::get('/viewAllLeads', [App\Http\Controllers\Leads\LeadController::class, 'viewAllLeads'])->middleware(CustomAuth::class)->name('AllLeads');
//new code and route for view all leads
Route::get('/view_all_leads', [App\Http\Controllers\Leads\LeadController::class, 'ViewAllLeads_new'])->middleware(CustomAuth::class)->name('view_all_leads');
Route::get('/view_all_leads_test', [App\Http\Controllers\Leads\LeadController_test::class, 'ViewAllLeads_new'])->middleware(CustomAuth::class)->name('view_all_leads_test');
// view All Closed Leads 
Route::get('/viewClosedLeads', [App\Http\Controllers\Leads\LeadController::class, 'viewClosedLeads'])->middleware(CustomAuth::class)->name('ClosedLeads');
// Fetch Closed leads data based on input dates
Route::post('/viewClosedLeads', [App\Http\Controllers\Leads\LeadController::class, 'viewClosedLeads'])->middleware(CustomAuth::class)->name('ClosedLeads');
// view All InProcess Leads 
Route::get('/viewInProcessLeads', [App\Http\Controllers\Leads\LeadController::class, 'viewInProcessLeads'])->middleware(CustomAuth::class)->name('InProcessLeads');
// Fetch InProcess leads data based on input dates
Route::post('/viewInProcessLeads', [App\Http\Controllers\Leads\LeadController::class, 'viewInProcessLeads'])->middleware(CustomAuth::class)->name('InProcessLeads');
// view All Converted Leads 
Route::get('/viewConvertedLeads', [App\Http\Controllers\Leads\LeadController::class, 'viewConvertedLeads'])->middleware(CustomAuth::class)->name('ConvertedLeads');
// Fetch Converted leads data based on input dates
Route::post('/viewConvertedLeads', [App\Http\Controllers\Leads\LeadController::class, 'viewConvertedLeads'])->middleware(CustomAuth::class)->name('ConvertedLeads');
//fetch product Details..
Route::get('/fetch_product_details/{product_id}', [App\Http\Controllers\Leads\LeadController::class, 'fetch_product_details'])->middleware(CustomAuth::class)->name('fetch_product_details');
//fetch product Details sales..
Route::get('/fetch_product_details_sales/{product_id}', [App\Http\Controllers\Leads\LeadController::class, 'fetch_product_details_sales'])->middleware(CustomAuth::class)->name('fetch_product_details_sales');
// view all leads on dashboard...
Route::get('/viewAllLeadsD', [App\Http\Controllers\Leads\LeadController::class, 'viewAllLeads_d'])->middleware(CustomAuth::class)->name('AllLeads!converted&!closed');
//View Lead Readonly
Route::get('/leads_view_lead/{customer_id}/{id}', [App\Http\Controllers\Leads\LeadController::class, 'leads_view_lead'])->middleware(CustomAuth::class)->name('leads_view_lead');
//View Lead to edit
Route::get('/edit_lead/{customer_id}/{id}', [App\Http\Controllers\Leads\LeadController::class, 'edit_lead'])->middleware(CustomAuth::class)->name('edit_lead');
//Update Data post method used
Route::post('/update_lead', [App\Http\Controllers\Leads\LeadController::class, 'update_lead'])->middleware(CustomAuth::class)->name('update_lead');
//View Lead to convert
Route::get('/convert_lead/{customer_id}/{id}', [App\Http\Controllers\Leads\LeadController::class, 'convert_lead'])->middleware(CustomAuth::class)->name('convert_lead');
// update lead status / Close lead..
//Route::get('/close_lead/{customer_id}/{id}', [App\Http\Controllers\Leads\LeadController::class, 'close_lead'])->middleware(CustomAuth::class)->name('close_lead');
Route::post('/close_lead/{id}', [App\Http\Controllers\Leads\LeadController::class, 'close_lead_with_reason'])->middleware(CustomAuth::class)->name('close_lead');
//update lead status / close lead with specified reason..
//Route::get('/close_lead/{customer_id}/{id}/{reason}/{desc}', [App\Http\Controllers\Leads\LeadController::class, 'close_lead_with_reason'])->middleware(CustomAuth::class)->name('close_lead');
// Delete Lead
Route::get('/delete_lead/{id}', [App\Http\Controllers\Leads\LeadController::class, 'delete_lead'])->middleware(CustomAuth::class)->name('delete_lead');
//Display Check Customer View....
Route::get('/check_customer', [App\Http\Controllers\Leads\LeadController::class, 'check_customer'])->middleware(CustomAuth::class)->name('check_customer');
//Check Customer if already Registered.....
Route::post('/check_customer', [App\Http\Controllers\Leads\LeadController::class, 'check_customer'])->middleware(CustomAuth::class)->name('check_customer');

// FIND CUSTOMER BY PRIMARY CONTACT NUMBER.....
Route::get('/findCustomer', [App\Http\Controllers\Leads\LeadController::class, 'findCustomer'])->middleware(CustomAuth::class)->name('findCustomer');
// Filter viewAllLeads Table by Today/Yesterday/Past 3 Days/A Week/A Month.........
//Route::get('/filterLeads/{filter_by}', [App\Http\Controllers\Leads\LeadController::class, 'filterLeads'])->middleware(CustomAuth::class)->name('filterLeads');
//Route::get('/filterLeads/{filter_by}/{section}', [App\Http\Controllers\Leads\LeadController::class, 'filterLeads'])->middleware(CustomAuth::class)->name('filterLeads');
Route::get('/filterNormalLeads/{filter_by}', [App\Http\Controllers\Leads\LeadController::class, 'filterLeadsViewAll'])->middleware(CustomAuth::class)->name('filterNormalLeads');
//datewise search view all leads
Route::post('/leads_datewise_search', [App\Http\Controllers\Leads\LeadController::class, 'datewise_LeadsViewAll'])->middleware(CustomAuth::class)->name('datewise_LeadsViewAll');

//inprocess
Route::get('/filterInprocessLeads/{filter_by}', [App\Http\Controllers\Leads\LeadController::class, 'filterInprocessLeads'])->middleware(CustomAuth::class)->name('filterInprocessLeads');
//Converted Lead
Route::get('/filterConvertedLeads/{filter_by}', [App\Http\Controllers\Leads\LeadController::class, 'filterConvertedLeads'])->middleware(CustomAuth::class)->name('filterConvertedLeads');
//Closed Lead
Route::get('/filterClosedLeads/{filter_by}', [App\Http\Controllers\Leads\LeadController::class, 'filterClosedLeads'])->middleware(CustomAuth::class)->name('filterClosedLeads');

//add add_comment
Route::get('/add_lead_comment/{user_id}/{lead_id}/{desc}', [App\Http\Controllers\Leads\LeadController::class, 'add_lead_comment'])->middleware(CustomAuth::class)->name('add_lead_comment');
// sent  delivery challan 
Route::post('/sent_challan', [App\Http\Controllers\Leads\LeadController::class, 'sent_challan'])->middleware(CustomAuth::class)->name('sent_challan');
//leads date search filter
Route::post('/leads_date_search', [App\Http\Controllers\Leads\LeadController::class, 'LeadsDateSearch'])->middleware(CustomAuth::class)->name('leads_date_search');
Route::post('/leads_add_comment/{lead_id}', [App\Http\Controllers\Leads\LeadController::class, 'AddComment'])->middleware(CustomAuth::class)->name('leads_add_comment');


Route::post('/updateStatus', [App\Http\Controllers\Leads\LeadController::class, 'updateStatus'])->middleware(CustomAuth::class)->name('updateStatus');


//---------------------Close------------------------------//
Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return 'cleared';
});
//------------CRMLeads-----------------------------------//
//View CRM All Leads
Route::get('/viewCRMAllLeads', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'viewCRMAllLeads'])->middleware(CustomAuth::class)->name('viewCRMAllLeads');
Route::get('/createLeads', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'createLeads'])->middleware(CustomAuth::class)->name('createLeads');
Route::get('crm/get-products-by-type', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'getProductsByType']);
Route::post('createLeads_Save', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'createLeads_Save'])->name('createLeads_Save');
Route::post('/search-lead-by-mobile', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'searchByMobile'])->name('searchByMobile');
Route::post('/verify-status', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'verifyStatus'])->name('verifyStatus');
Route::post('/leadverify', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'verifyUpdate'])->name('leadverify');
Route::get('/viewWebsiteOrder', [App\Http\Controllers\CRMLeads\CRMLeadsController::class, 'viewWebsiteOrder'])->middleware(CustomAuth::class)->name('viewWebsiteOrder');

//---------------------Close------------------------------//

//_---------------------JD Lead Routes----------------//
//-------------view all leads -------------//
Route::get('/view_all_jd_leads', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'viewAllJDLeads'])->middleware(CustomAuth::class)->name('view_all_jd_leads');
//_---------------------JD in process leads----------------//
Route::get('/view_all_inprocess_leads', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'viewAllInProcessLeads'])->middleware(CustomAuth::class)->name('view_all_inprocess_leads');
//_---------------------JD in Converted leads----------------//
Route::get('/view_all_converted_leads', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'viewAllConvertedLeads'])->middleware(CustomAuth::class)->name('view_all_converted_leads');
//_---------------------JD in Closed leads----------------//
Route::get('/view_all_closed_leads', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'viewAllClosedLeads'])->middleware(CustomAuth::class)->name('view_all_closed_leads');
//_---------------------JD in Q5C leads----------------//
Route::get('/view_all_q5c_leads', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'viewAllQ5CLeads'])->middleware(CustomAuth::class)->name('view_all_q5c_leads');
//-------inprocesss update ------//
Route::get('/in_process/{jd_lead_id}/{user_id}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'in_process'])->middleware(CustomAuth::class)->name('in_process');
//progress
Route::post('/progress_jd_lead', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'progress_jd_lead'])->middleware(CustomAuth::class)->name('progress_jd_lead');
//-----vioew jd lead from conver---//
Route::get('/jd_view_lead/{customer_id}/{id}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'jd_view_lead'])->middleware(CustomAuth::class)->name('jd_view_lead');
//---------------Closed Lead----//
Route::get('/close_jd_lead/{user_id}/{lead_id}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'close_jd_lead'])->middleware(CustomAuth::class)->name('close_jd_lead');
//-------- close lead_with reason --------------//
Route::get('/close_jd_lead/{user_id}/{lead_id}/{reason}/{desc}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'close_jd_lead_with_reason'])->middleware(CustomAuth::class)->name('close_jd_lead');
//--------GET Conver lead to create jd_lead --------------//
Route::get('/create_jd_lead/{lead_id}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'create_jd_lead'])->middleware(CustomAuth::class)->name('create_jd_lead');
//-------- POST Convert lead to create jd_lead --------------//
Route::post('/create_jd_lead/{lead_id}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'create_jd_lead'])->middleware(CustomAuth::class)->name('create_jd_lead');
//-----------comment addd post method-----//
Route::get('/add_comment/{user_id}/{lead_id}/{desc}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'add_comment'])->middleware(CustomAuth::class)->name('add_comment');
//----------comment for converted lead-------------//
Route::get('/add_converted_comment/{user_id}/{lead_id}/{desc}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'add_converted_comment'])->middleware(CustomAuth::class)->name('add_converted_comment');
//----------jd lead filter---------//
Route::get('/filterJDLeads/{filter_by}/{section}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'filterJDLeads'])->middleware(CustomAuth::class)->name('filterJDLeads');
// view all route
Route::get('/filterJDLeadsViewAll/{filter_by}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'filterJDLeadsViewAll'])->middleware(CustomAuth::class)->name('filterJDLeadsViewAll');
// view in progress jd_leads
Route::get('/filterJDLeadsInProgress/{filter_by}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'filterJDLeadsInProgress'])->middleware(CustomAuth::class)->name('filterJDLeadsInProgress');
// view converted jd_leads
Route::get('/filterJDLeadsConverted/{filter_by}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'filterJDLeadsConverted'])->middleware(CustomAuth::class)->name('filterJDLeadsConverted');
// view closed jd_leads
Route::get('/filterJDLeadsClosed/{filter_by}', [App\Http\Controllers\JDLeadController\JDLeadController::class, 'filterJDLeadsClosed'])->middleware(CustomAuth::class)->name('filterJDLeadsConverted');


//---------------------Nurses Data -----------------------------//
//add nurse display page
Route::get('/add_nurse', [App\Http\Controllers\NurseController\NurseController::class, 'add_nurse'])->middleware(CustomAuth::class)->name('add_nurse');
//add nurse to data base
Route::post('/add_nurse', [App\Http\Controllers\NurseController\NurseController::class, 'add_nurse'])->middleware(CustomAuth::class)->name('add_nurse');
//view all nurse 
Route::get('/view_all_nurse', [App\Http\Controllers\NurseController\NurseController::class, 'view_all_nurse'])->middleware(CustomAuth::class)->name('view_all_nurses');
//------reffered nurse_id
Route::get('/view_referred_nurse', [App\Http\Controllers\NurseController\NurseController::class, 'view_referred_nurse'])->middleware(CustomAuth::class)->name('view_referred_nurse');
// iprogerss
Route::get('/view_inprogress_nurse', [App\Http\Controllers\NurseController\NurseController::class, 'view_inprogress_nurse'])->middleware(CustomAuth::class)->name('view_inprogress_nurse');
//-------closed nurse---//
Route::get('/view_closed_nurse', [App\Http\Controllers\NurseController\NurseController::class, 'view_closed_nurse'])->middleware(CustomAuth::class)->name('view_closed_nurse');
//view nurse detaile
Route::get('/view_nurse_details/{nurse_id}', [App\Http\Controllers\NurseController\NurseController::class, 'view_nurse_details'])->middleware(CustomAuth::class)->name('view_nurse_details');
//-------inprocesss update ------//
Route::get('/in_progress/{nurse_id}/{user_id}', [App\Http\Controllers\NurseController\NurseController::class, 'in_progress'])->middleware(CustomAuth::class)->name('in_progress');
//----referral it -----//
Route::post('/referral', [App\Http\Controllers\NurseController\NurseController::class, 'referral'])->middleware(CustomAuth::class)->name('referral');
//-----close status update----//
Route::get('/close/{nurse_id}/{user_id}', [App\Http\Controllers\NurseController\NurseController::class, 'close'])->middleware(CustomAuth::class)->name('close');
//---add Comment nurse data---//
Route::get('/add_nurse_comment/{user_id}/{nurse_id}/{desc}', [App\Http\Controllers\NurseController\NurseController::class, 'add_nurse_comment'])->middleware(CustomAuth::class)->name('add_nurse_comment');
//close with reason
Route::get('/close_nurse/{user_id}/{nurse_id}/{reason}/{desc}', [App\Http\Controllers\NurseController\NurseController::class, 'close_nurse'])->middleware(CustomAuth::class)->name('close_nurse');


//--------------Referral mgmt Routes-----------------//

// View Customer Profile ...
// Route::get('/view_profile', [App\Http\Controllers\ReferralController\ReferralController::class, 'view_profile'])->middleware(CustomAuth::class)->name('view_profile');
//view all expenses of delivery boy..
Route::get('/viewAllExpenses_byID/{name}', [App\Http\Controllers\ReferralController\ReferralController::class, 'viewAllExpenses_byID'])->middleware(CustomAuth::class)->name('viewAllExpenses_byID');
// view all Referrals..
Route::get('/viewAllReferrals', [App\Http\Controllers\ReferralController\ReferralController::class, 'viewAllReferrals'])->middleware(CustomAuth::class)->name('viewAllReferrals');
Route::get('/referralsCount', [App\Http\Controllers\ReferralController\ReferralController::class, 'referralsCount'])->middleware(CustomAuth::class)->name('referralsCount');

//view Expense Details
Route::get('/view_details/{id}', [App\Http\Controllers\ReferralController\ReferralController::class, 'view_details'])->middleware(CustomAuth::class)->name('view_details');
// update_status
Route::post('/searchReferrals', [App\Http\Controllers\ReferralController\ReferralController::class, 'searchReferrals'])->middleware(CustomAuth::class)->name('searchReferrals');
// update_status
Route::post('/update_status', [App\Http\Controllers\ReferralController\ReferralController::class, 'update_status'])->middleware(CustomAuth::class)->name('update_status');
//Settle Expense
Route::post('/settle_expense', [App\Http\Controllers\ReferralController\ReferralController::class, 'settle_expense'])->middleware(CustomAuth::class)->name('settle_expense');





//--------------Referral mgmt Routes Close-----------------//

Route::get('/hot_leads', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'hot_leads'])->middleware(CustomAuth::class)->name('hot_leads');
Route::get('/in_process_hot_leads/{hot_lead_id}/{user_id}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'in_process_hot_leads'])->middleware(CustomAuth::class)->name('in_process_hot_leads');
Route::post('/create_hot_lead', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'create_hot_lead'])->middleware(CustomAuth::class)->name('create_hot_lead');
Route::get('/view_hot_leads_in_process_leads', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'view_in_process_leads'])->middleware(CustomAuth::class)->name('view_hot_leads_in_process_leads');
Route::get('/view_hot_leads_in_process_leads_test', [App\Http\Controllers\HotLeads\hotLeadsControllertest::class, 'view_in_process_leads'])->middleware(CustomAuth::class)->name('view_hot_leads_in_process_leads');
Route::get('/view_closed_leads', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'view_closed_leads'])->middleware(CustomAuth::class)->name('view_closed_leads');
Route::get('/filterHotInprocessLeads/{filter_by}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'filterInprocessLeads'])->middleware(CustomAuth::class)->name('filterInprocessLeads');
Route::get('/view_lead/{customer_id}/{id}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'view_lead'])->middleware(CustomAuth::class)->name('view_lead');
//View Lead to edit
Route::get('/edit_hot_lead/{customer_id}/{id}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'edit_lead'])->middleware(CustomAuth::class)->name('edit_lead');
//Update Data post method used
Route::post('/update_hot_lead', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'create_hot_lead'])->middleware(CustomAuth::class)->name('create_hot_lead');
//View Lead to convert
Route::get('/qualify_hot_leads/{hot_lead_id}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'qualify_lead'])->middleware(CustomAuth::class)->name('qualify_lead');
// update lead status / Close lead..
Route::get('/close_hot_lead/{customer_id}/{id}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'close_lead'])->middleware(CustomAuth::class)->name('close_lead');
//update lead status / close lead with specified reason..
//Route::get('/close_hot_lead/{hot_lead_id}/{reason}/{desc}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'close_lead_with_reason'])->middleware(CustomAuth::class)->name('close_lead');
Route::post('/close_hot_lead/{hot_lead_id}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'close_lead_with_reason'])->middleware(CustomAuth::class)->name('close_lead');
//comment
//Route::get('/add_hot_lead_comment/{hot_lead_id}/{desc}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'add_hot_lead_comment'])->middleware(CustomAuth::class)->name('add_hot_lead_comment');
Route::post('/add_hot_lead_comment/{hot_lead_id}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'add_hot_lead_comment'])->middleware(CustomAuth::class)->name('add_hot_lead_comment');
// Route::get('/add_hot_lead_comment/{user_id}/{lead_id}/{desc}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'add_comment'])->middleware(CustomAuth::class)->name('add_comment');
Route::get('/filterHotLeads/{filter_by}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'filterHotLeads'])->middleware(CustomAuth::class)->name('filterHotLeads');
Route::get('/filterHotInprocessLeads/{filter_by}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'filterHotInprocessLeads'])->middleware(CustomAuth::class)->name('filterHotInprocessLeads');
Route::get('/filterHotClosedLeads/{filter_by}', [App\Http\Controllers\HotLeads\hotLeadsController::class, 'filterHotClosedLeads'])->middleware(CustomAuth::class)->name('filterHotClosedLeads');

//--------------added routes-----------------//


//-------------Reports Routes----------------------------//
Route::get('/filterReport/{day}', [App\Http\Controllers\Reports\LeadReportController::class, 'filterReport'])->middleware(CustomAuth::class)->name('filterReport');

Route::get('/monthly_report', [App\Http\Controllers\Reports\MonthlyReportController::class, 'monthly_report'])->middleware(CustomAuth::class)->name('monthly_report');


Route::post('/monthly_report', [App\Http\Controllers\Reports\MonthlyReportController::class, 'monthly_report'])->middleware(CustomAuth::class)->name('monthly_report');

// Route::get('/leads_reports', [App\Http\Controllers\Reports\LeadReportController::class, 'lead_reports'])->middleware(CustomAuth::class)->name('lead_reports');
Route::get('/leads_reports', [App\Http\Controllers\Reports\LeadReportController::class, 'leadReport'])->middleware(CustomAuth::class)->name('lead_reports');
// Route::get('/equipment_report', [App\Http\Controllers\Reports\EquipmentReportController::class, 'equipment_report'])->middleware(CustomAuth::class)->name('equipment_report');
Route::get('/equipment_report', [App\Http\Controllers\Reports\EquipmentReportController::class, 'equipmentReportNew'])->middleware(CustomAuth::class)->name('equipment-report');
Route::get('/vendor_product_report', [App\Http\Controllers\Reports\VendorReportController::class, 'vendor_product_report'])->middleware(CustomAuth::class)->name('vendor_product_report');
Route::get('/filterEquipmentReport/{day}', [App\Http\Controllers\Reports\EquipmentReportController::class, 'filterEquipmentReport'])->middleware(CustomAuth::class)->name('filterEquipmentReport');
Route::get('/mis_reports', [App\Http\Controllers\Reports\MISReportController::class, 'mis_reports'])->middleware(CustomAuth::class)->name('mis_reports');
Route::any('/daybyday_report', [App\Http\Controllers\Reports\DaybyDayReportController::class, 'daybyday_report'])->middleware(CustomAuth::class)->name('daybyday_report');

Route::any('/WebRenewals_report', [App\Http\Controllers\Reports\WebRenewalsReportController::class, 'WebRenewals_report'])->middleware(CustomAuth::class)->name('WebRenewals_report');
Route::get('/renewal_report/{filter}', [App\Http\Controllers\Reports\RenewalPickupReportController::class, 'renewalPickupReport'])->middleware(CustomAuth::class)->name('renewal_report');

Route::get('/vendor_select', [App\Http\Controllers\Reports\VendorReportController::class, 'vendor_select'])->middleware(CustomAuth::class)->name('vendor_select');
Route::get('/rented_equipment_report/{vendor_id}', [App\Http\Controllers\Reports\VendorReportController::class, 'rented_equipment_report'])->middleware(CustomAuth::class)->name('rented_equipment_report');

//customer single view
Route::get('/customer_single_view_get', [App\Http\Controllers\CustomerView\CustomerViewController::class, 'CustomerSingleViewGet'])->middleware(CustomAuth::class)->name('customer_single_view_get');
Route::post('/customer_single_view_post', [App\Http\Controllers\CustomerView\CustomerViewController::class, 'CustomerSingleViewPost'])->middleware(CustomAuth::class)->name('customer_single_view_post');
Route::get('/transaction_history',[App\Http\Controllers\CustomerView\CustomerViewController::class, 'transaction_history'])->middleware(CustomAuth::class)->name('transaction_history');
Route::post('/get_customers', [App\Http\Controllers\CustomerView\CustomerViewController::class, 'GetCustomers'])->middleware(CustomAuth::class)->name('get_customers');
Route::get('/customer_leads/{customer_id}', [App\Http\Controllers\CustomerView\CustomerViewController::class, 'GetCustomerLeads'])->middleware(CustomAuth::class)->name('customer_leads');
Route::get('/get_customer_product_data/{order_details_id}/{name}', [App\Http\Controllers\CustomerView\CustomerViewController::class, 'GetProductsData'])->middleware(CustomAuth::class)->name('get_customer_product_data');
Route::get('/get_customer_all_leads_data/{customer_id}', [App\Http\Controllers\CustomerView\CustomerViewController::class, 'GetAllLeadData'])->middleware(CustomAuth::class)->name('get_customer_all_leads_data');
//populate customers list
Route::get('/cust_single_populate_customers/{cust_keyword}', [App\Http\Controllers\CustomerView\CustomerViewController::class, 'GetCustomerListToPopulate'])->middleware(CustomAuth::class)->name('cust_single_populate_customers');
//********** Task Management **********//
Route::get('/add_project_task', [App\Http\Controllers\Task\TaskController::class, 'addProject'])->middleware(CustomAuth::class)->name('addProject');
Route::post('/add_project_task', [App\Http\Controllers\Task\TaskController::class, 'addProject'])->middleware(CustomAuth::class)->name('addProject');

Route::get('/edit_project/{project_id}', [App\Http\Controllers\Task\TaskController::class, 'editProject'])->middleware(CustomAuth::class)->name('editProject');
Route::post('/update_project', [App\Http\Controllers\Task\TaskController::class, 'updateProject'])->middleware(CustomAuth::class)->name('updateProject');
Route::get('/delete_project/{project_id}', [App\Http\Controllers\Task\TaskController::class, 'deleteProject'])->middleware(CustomAuth::class)->name('deleteProject');

Route::get('/add_new_task', [App\Http\Controllers\Task\TaskController::class, 'addTask'])->middleware(CustomAuth::class)->name('addTask');
Route::post('/add_new_task', [App\Http\Controllers\Task\TaskController::class, 'addTask'])->middleware(CustomAuth::class)->name('addTask');

Route::get('/edit_task/{task_id}', [App\Http\Controllers\Task\TaskController::class, 'editTask'])->middleware(CustomAuth::class)->name('editTask');
Route::post('/update_task', [App\Http\Controllers\Task\TaskController::class, 'updateTask'])->middleware(CustomAuth::class)->name('updateTask');
Route::get('/delete_task/{task_id}', [App\Http\Controllers\Task\TaskController::class, 'deleteTask'])->middleware(CustomAuth::class)->name('deleteTask');

Route::get('/viewAllProjects', [App\Http\Controllers\Task\TaskController::class, 'viewProjects'])->middleware(CustomAuth::class)->name('viewProjects');
Route::get('/my_task', [App\Http\Controllers\Task\TaskController::class, 'my_task'])->middleware(CustomAuth::class)->name('my_task');


//----Generate Delivery Challan ----//
Route::get('/generate_challan', [App\Http\Controllers\Leads\LeadController::class, 'generate_challan'])->middleware(CustomAuth::class)->name('generate_challan');


//----- Orders -----//
// --- -- - {[Type Individual]} - -- --- //
Route::get('/individual_vendor_batch/{equipment}/{qu_quantity}', [App\Http\Controllers\OrderManagement\OrderController::class, 'individual_vendor_batch'])->middleware(CustomAuth::class)->name('individual_vendor_batch');
Route::get('/individual_vendor_batch_sale', [App\Http\Controllers\OrderManagement\OrderController::class, 'individual_vendor_batch_sale'])->middleware(CustomAuth::class)->name('individual_vendor_batch');
Route::get('/select_vendor_warehouses/{equipment}/{vendor_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_vendor_warehouses'])->middleware(CustomAuth::class)->name('select_vendor_warehouses');
Route::get('/select_vendor_warehouses_sale/{vendor_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_vendor_warehouses_sale'])->middleware(CustomAuth::class)->name('select_vendor_warehouses');
Route::get('/select_product_brand/{equipment}/{vendor_id}/{warehouse_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_product_brand'])->middleware(CustomAuth::class)->name('select_product_brand');
Route::get('/select_product_brand_sale/{equipment}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_product_brand_sale'])->middleware(CustomAuth::class)->name('select_product_brand');
Route::get('/select_batch/{equipment}/{vendor_id}/{warehouse_id}/{brand_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_batch'])->middleware(CustomAuth::class)->name('select_batch');
Route::get('/getDetails/{product_id}', [App\Http\Controllers\OrderManagement\OrderController::class, 'getDetails'])->middleware(CustomAuth::class)->name('getDetails');

// --- -- - {[Type All]} - -- --- //
Route::post('/all_vendor_batch', [App\Http\Controllers\OrderManagement\OrderController::class, 'all_vendor_batch'])->middleware(CustomAuth::class)->name('all_vendor_batch');
Route::post('/select_vendor_warehouses_all', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_vendor_warehouses_all'])->middleware(CustomAuth::class)->name('select_vendor_warehouses_all');
Route::post('/select_product_brand_all', [App\Http\Controllers\OrderManagement\OrderController::class, 'select_product_brand_all'])->middleware(CustomAuth::class)->name('select_product_brand_all');

Route::get('/location_populate', [App\Http\Controllers\OrderManagement\OrderController::class, 'location_populate'])->middleware(CustomAuth::class)->name('location_populate');


//------Billing Payment --------//
Route::get('/pending_online_renew', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'pending_online_renew'])->middleware(CustomAuth::class)->name('pending_online_renew');

Route::get('/collection_report', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'collection_report'])->middleware(CustomAuth::class)->name('collection_report');

//---------payment recieved------------//
Route::get('/payment_recieved/{order_id}', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'PaymentRecieved'])->middleware(CustomAuth::class)->name('payment_recieved');
Route::post('/payment_recieved/{order_id}', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'PaymentRecieved'])->middleware(CustomAuth::class)->name('payment_recieved');
//online payment reminder for renewals from
Route::get('/renewal_payment_reminder/{customer_id}/{order_id}', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'renewal_online_payement_reminder'])->middleware(CustomAuth::class)->name('renewal_payment_reminder');
//pending Payments 
Route::get('/show_pending_payments', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'ShowPendingPayments'])->middleware(CustomAuth::class)->name('show_pending_payments');

//Route::get('/pending_payments', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'pendingPaymentOrder'])->middleware(CustomAuth::class)->name('pendingPaymentOrder');
// Order Transactions.
// Route::get('/order_transaction', [App\Http\Controllers\BillingAndPayment\OrderTransactionControlller::class, 'pendingPaymentOrder'])->middleware(CustomAuth::class)->name('orderTransaction');


//pending payments filter
Route::get('/pending_payments_filter/{filter_val}', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'PendigPaymentFilter'])->middleware(CustomAuth::class)->name('pending_payments_filter');

//--------complaint Management----------------//

Route::get('/raise_complaint', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'RaiseComplaint'])->middleware(CustomAuth::class)->name('raise_complaint');
//Route::post('/raise_complaint', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'RaiseComplaint'])->middleware(CustomAuth::class)->name('raise_complaint');
Route::get('/find_customers/{cust_val}', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'GetCustomers'])->middleware(CustomAuth::class)->name('find_customers');
Route::get('/get_complaint/{btn_val}/{cust_no}', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'GetComplaintData'])->middleware(CustomAuth::class)->name('get_complaint');
Route::post('/get_product_details', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'ProductDetails'])->middleware(CustomAuth::class)->name('get_product_details');
Route::get('/open_complaints', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'OpenComplaints'])->middleware(CustomAuth::class)->name('open_complaints');
Route::get('/view_open_complaint/{customer_id}/{complaint_date}', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'ViewOpenComplaint'])->middleware(CustomAuth::class)->name('view_open_complaint');
Route::post('/close_complaint', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'CloseComplaint'])->middleware(CustomAuth::class)->name('close_complaint');
Route::get('/closed_complaints', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'ShowClosedComplaints'])->middleware(CustomAuth::class)->name('closed_complaints');
Route::get('/view_closed_complaint/{customer_id}/{closed_date}', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'ViewClosedComplaint'])->middleware(CustomAuth::class)->name('view_closed_complaint');
Route::get('/complaint_details/{complaint_id}', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'GetComplaintDetails'])->middleware(CustomAuth::class)->name('complaint_details');
Route::post('/date_filter_complaints/{type}', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'DateFilter'])->middleware(CustomAuth::class)->name('date_filter_complaints');
Route::post('/searched_customers_complaints', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'SearchedCustomers'])->middleware(CustomAuth::class)->name('searched_customers_complaints');

Route::get('/get_cust_or_complaint', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'GetCustOrComplaint'])->middleware(CustomAuth::class)->name('get_cust_or_complaint');
Route::get('/complaint_customer_view/{customer_id}', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'GetCustomer'])->middleware(CustomAuth::class)->name('complaint_customer_view');
Route::get('/complaint_mgmt_filter', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'ComplaintFilter'])->middleware(CustomAuth::class)->name('complaint_mgmt_filter');
Route::get('/create_complaint', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'CreateComplaint'])->middleware(CustomAuth::class)->name('create_complaint');
Route::post('/create_complaint', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'CreateComplaint'])->middleware(CustomAuth::class)->name('create_complaint');
Route::get('/complaint_customers_populate', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'CustomerPopulate'])->middleware(CustomAuth::class)->name('complaint_customers_populate');
Route::get('/complaint_vendors_populate', [App\Http\Controllers\OrderManagement\TestController::class, 'VendorPopulate'])->middleware(CustomAuth::class)->name('complaint_vendors_populate');
Route::post('/complaint_delete', [App\Http\Controllers\ComplaintManagement\ComplaintController::class, 'DeleteComplaint'])->middleware(CustomAuth::class)->name('complaint_delete');

//-------------------------Others Leads-------------------------//
//medical lab
Route::get('/lab_register', [App\Http\Controllers\OtherServices\LabController::class, 'Registerlab'])->middleware(CustomAuth::class)->name('lab_register');
Route::post('/lab_register', [App\Http\Controllers\OtherServices\LabController::class, 'Registerlab'])->middleware(CustomAuth::class)->name('lab_register');
Route::post('/update_lab_register', [App\Http\Controllers\OtherServices\LabController::class, 'UpdateRegisterlab'])->middleware(CustomAuth::class)->name('update_lab_register');
Route::get('/create_lead_lab', [App\Http\Controllers\OtherServices\LabController::class, 'CreateLead'])->middleware(CustomAuth::class)->name('create_lead_lab');
Route::post('/create_lead_lab', [App\Http\Controllers\OtherServices\LabController::class, 'CreateLead'])->middleware(CustomAuth::class)->name('create_lead_lab');
Route::get('/view_all_lab_test_leads', [App\Http\Controllers\OtherServices\LabController::class, 'ViewAllLabTestLeads'])->middleware(CustomAuth::class)->name('view_all_lab_test_leads');
Route::get('/view_all_labs', [App\Http\Controllers\OtherServices\LabController::class, 'ViewAllLabs'])->middleware(CustomAuth::class)->name('view_all_labs');
Route::get('/view_lab/{id}', [App\Http\Controllers\OtherServices\LabController::class, 'ViewLab'])->middleware(CustomAuth::class)->name('view_lab');
Route::get('/delete_lab/{id}', [App\Http\Controllers\OtherServices\LabController::class, 'DeleteLab'])->middleware(CustomAuth::class)->name('delete_lab');
Route::post('/lab_convert_lead', [App\Http\Controllers\OtherServices\LabController::class, 'ConvertLead'])->middleware(CustomAuth::class)->name('lab_convert_lead');
Route::post('/lab_close_lead', [App\Http\Controllers\OtherServices\LabController::class, 'CloseLead'])->middleware(CustomAuth::class)->name('lab_close_lead');
Route::get('/lab_test_customers_populate', [App\Http\Controllers\OtherServices\LabController::class, 'CustomerPopulate'])->middleware(CustomAuth::class)->name('lab_test_customers_populate');
//Ambulance Leads
Route::get('/create_lead_ambulance', [App\Http\Controllers\OtherServices\AmbulanceController::class, 'CreateLead'])->middleware(CustomAuth::class)->name('create_lead_ambulance');
Route::post('/create_lead_ambulance', [App\Http\Controllers\OtherServices\AmbulanceController::class, 'CreateLead'])->middleware(CustomAuth::class)->name('create_lead_ambulance');
Route::get('/view_all_ambulance_leads', [App\Http\Controllers\OtherServices\AmbulanceController::class, 'ViewAmbulanceLeads'])->middleware(CustomAuth::class)->name('view_all_ambulance_leads');
Route::post('/amb_convert_lead', [App\Http\Controllers\OtherServices\AmbulanceController::class, 'ConvertLead'])->middleware(CustomAuth::class)->name('amb_convert_lead');
Route::post('/amb_close_lead', [App\Http\Controllers\OtherServices\AmbulanceController::class, 'CloseLead'])->middleware(CustomAuth::class)->name('amb_close_lead');
//nursing care 
Route::get('/create_lead_nursing_care', [App\Http\Controllers\OtherServices\NursingCareController::class, 'CreateLead'])->middleware(CustomAuth::class)->name('create_lead_nursing_care');
Route::post('/create_lead_nursing_care', [App\Http\Controllers\OtherServices\NursingCareController::class, 'CreateLead'])->middleware(CustomAuth::class)->name('create_lead_nursing_care');
Route::get('/view_all_nursing_care_leads', [App\Http\Controllers\OtherServices\NursingCareController::class, 'ViewNursingCareLeads'])->middleware(CustomAuth::class)->name('view_all_nursing_care_leads');
Route::post('/nur_convert_lead', [App\Http\Controllers\OtherServices\NursingCareController::class, 'ConvertLead'])->middleware(CustomAuth::class)->name('nur_convert_lead');
Route::post('/nur_close_lead', [App\Http\Controllers\OtherServices\NursingCareController::class, 'CloseLead'])->middleware(CustomAuth::class)->name('nur_close_lead');

Route::get('nursing-care',[App\Http\Controllers\OtherServices\NursingCareController::class,'index'])->middleware(CustomAuth::class)->name('nursing-care');
Route::get('nursing-care-create',[App\Http\Controllers\OtherServices\NursingCareController::class,'create'])->middleware(CustomAuth::class)->name('nursing-care-create');
Route::post('nursing-care-store',[App\Http\Controllers\OtherServices\NursingCareController::class,'store'])->middleware(CustomAuth::class)->name('nursing-care-store');
Route::get('nursing-care-view/{id}',[App\Http\Controllers\OtherServices\NursingCareController::class,'view'])->middleware(CustomAuth::class)->name('nursing-care-view');
Route::get('nursing-care-edit/{id}',[App\Http\Controllers\OtherServices\NursingCareController::class,'edit'])->middleware(CustomAuth::class)->name('nursing-care-edit');
Route::post('nursing-care-update/{id}',[App\Http\Controllers\OtherServices\NursingCareController::class,'update'])->middleware(CustomAuth::class)->name('nursing-care-update');
Route::post('nursing-care-status-update/{id}',[App\Http\Controllers\OtherServices\NursingCareController::class,'statusUpdate'])->middleware(CustomAuth::class)->name('nursing-care-status-update');
Route::post('nursing-care-cancel/{id}',[App\Http\Controllers\OtherServices\NursingCareController::class,'cancel'])->middleware(CustomAuth::class)->name('nursing-care-cancel');

//customer side links
//send link leads
    Route::any('/send_link_lead', [App\Http\Controllers\Leads\LeadController::class, 'SendLinkLead'])->middleware(CustomAuth::class)->name('send_link_lead');
    Route::any('/create_lead_link/{link_id}', [App\Http\Controllers\Leads\LeadController::class, 'CreateLeadLink'])->name('create_lead_link');
//renewal pickup auto reminder
    Route::get('/0/{id}', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'ShortUrl'])->name('short_url');
    Route::get('/cons/{id}', [App\Http\Controllers\OtherServices\OtherLeadController::class, 'ShortUrl'])->name('short_url1');
    Route::any('/get_renewal_links', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'GetRenewalLinks'])->middleware(CustomAuth::class)->name('get_renewal_links');
    Route::any('/customer_renewal_or_pickup_link/{link_id}', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'CustomerRenewalLinkData'])->name('customer_renewal_or_pickup_link');
    Route::any('/view_renewal_or_pickup_link/{link_id}', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'AdminRenewalLinkData'])->middleware(CustomAuth::class)->name('view_renewal_or_pickup_link');
//testing purpose
Route::get('/excel_exp', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'RenewalPickupExcel'])->middleware(CustomAuth::class)->name('excel_exp');

Route::any('/testing', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'RenewalAutoReminder'])->middleware(CustomAuth::class)->name('testing');

Route::get('/order_details', [App\Http\Controllers\OrderManagement\TestController::class, 'vendor_product_details'])->middleware(CustomAuth::class)->name('vendor_product_details');

Route::get('/order_detailstest', [App\Http\Controllers\OrderManagement\TestControllertest::class, 'vendor_product_detailsold'])->middleware(CustomAuth::class)->name('vendor_product_details');

Route::get('/order_details_count', [App\Http\Controllers\OrderManagement\TestController::class, 'order_details_count'])->middleware(CustomAuth::class)->name('order_details_count');

// Route::get('/inventoryUpdateScript', [App\Http\Controllers\OrderManagement\TestController::class, 'inventoryUpdateScript'])->middleware(CustomAuth::class)->name('inventoryUpdateScript');

Route::get('/editOrder/{order_id}/{order_type}', [App\Http\Controllers\OrderManagement\EditOrderController::class, 'editOrder'])->middleware(CustomAuth::class)->name('editOrder');

Route::post('/updateOrderProduct', [App\Http\Controllers\OrderManagement\EditOrderController::class, 'updateOrderProduct'])->middleware(CustomAuth::class)->name('updateOrderProduct');

Route::post('/addOrderProduct', [App\Http\Controllers\OrderManagement\EditOrderController::class, 'addOrderProduct'])->middleware(CustomAuth::class)->name('addOrderProduct');

//labour charges update
Route::post('/editOrderDelUpdate/{order_id}', [App\Http\Controllers\OrderManagement\EditOrderController::class, 'editOrderUpdate'])->middleware(CustomAuth::class)->name('editOrderDelUpdate');

Route::any('vendor-live-inventory',[App\Http\Controllers\OrderManagement\TestController::class,'vendor_live_inventory'])->middleware(CustomAuth::class)->name('vendor-live-inventory');

Route::any('vendor-inventory-auto',[App\Http\Controllers\OrderManagement\TestController::class,'vendor_inventory_auto'])->middleware(CustomAuth::class)->name('vendor-inventory-auto');

Route::any('vendor-return-inventory', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'vendorReturnInventory'])->middleware(CustomAuth::class)->name('vendor-return-inventory');
Route::any('vendor-return-inventory-create', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'vendorReturnInventoryCreate'])->middleware(CustomAuth::class)->name('vendor-return-inventory-create');
Route::any('vendor-return-inventory-get', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'vendorReturnInventoryGet'])->middleware(CustomAuth::class)->name('vendor-return-inventory-get');
Route::any('vendor-return-inventory-delete', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'vendorReturnInventoryDelete'])->middleware(CustomAuth::class)->name('vendor-return-inventory-delete');
Route::get('vendor-inventory-verify/{id}', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'vendorInventoryVerify'])->middleware(CustomAuth::class)->name('vendor-inventory-verify');
Route::get('vendor-billing', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'vendorBilling'])->middleware(CustomAuth::class)->name('vendor-billing');
Route::post('vendor-billing-crud', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'vendorBillingCrud'])->middleware(CustomAuth::class)->name('vendor-billing-crud');
// Working but not releasing now...
Route::post('get-orderid', [App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class, 'getOrderid'])->middleware(CustomAuth::class)->name('get-orderid');

//renewal pickup auto reminder
    Route::get('/0/{id}', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'ShortUrl'])->name('short_url');
    Route::any('/get_renewal_links', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'GetRenewalLinks'])->middleware(CustomAuth::class)->name('get_renewal_links');
    Route::any('/customer_renewal_or_pickup_link/{link_id}', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'CustomerRenewalLinkData'])->name('customer_renewal_or_pickup_link');
    Route::any('/view_renewal_or_pickup_link/{link_id}', [App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'AdminRenewalLinkData'])->middleware(CustomAuth::class)->name('view_renewal_or_pickup_link');

Route::post('/delivery_upload_image',[App\Http\Controllers\DeliveryManagement\DeliveryController::class,'delivery_upload_image'])->middleware(CustomAuth::class)->name('delivery_upload_image');

Route::get('rejected-orders',[App\Http\Controllers\DeliveryManagement\DeliveryController::class,'rejectedOrders'])->middleware(CustomAuth::class)->name('rejected-orders');
Route::post('rejected-orders-update',[App\Http\Controllers\DeliveryManagement\DeliveryController::class,'rejectedOrdersUpdate'])->middleware(CustomAuth::class)->name('rejected-orders-update');

Route::post('/getOrderDetails',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'getOrderDetails'])->middleware(CustomAuth::class)->name('getOrderDetails');

Route::post('/update-pickup',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'updatePickup'])->middleware(CustomAuth::class)->name('update-pickup');

Route::get('/pending_payments', [App\Http\Controllers\BillingAndPayment\BillingPaymentController::class, 'pendingPaymentOrder'])->middleware(CustomAuth::class)->name('pendingPaymentOrder');

Route::get('/pending_payments_test', [App\Http\Controllers\BillingAndPayment\BillingPaymentController_old::class, 'pendingPaymentOrder'])->middleware(CustomAuth::class)->name('pendingPaymentOrder');

Route::any('/collectionReport',[App\Http\Controllers\Reports\CollectionReportController::class,'collectionReport'])->middleware(CustomAuth::class)->name('collectionReport');

//Expenses routes
Route::any('/delivery_staff_expenses', [App\Http\Controllers\Expense\ExpenseController::class, 'AllExpenses'])->middleware(CustomAuth::class)->name('delivery_staff_expenses');
Route::post('/settle_expense', [App\Http\Controllers\Expense\ExpenseController::class, 'SettleExpense'])->middleware(CustomAuth::class)->name('settle_expense');
Route::post('/narration_updt', [App\Http\Controllers\Expense\ExpenseController::class, 'UpdateNarration'])->middleware(CustomAuth::class)->name('narration_updt');
Route::post('/add_cash', [App\Http\Controllers\Expense\ExpenseController::class, 'AddCash'])->middleware(CustomAuth::class)->name('add_cash');
Route::post('/update_cash', [App\Http\Controllers\Expense\ExpenseController::class, 'UpdateCash'])->middleware(CustomAuth::class)->name('update_cash');
Route::get('/verify_expense/{id}', [App\Http\Controllers\Expense\ExpenseController::class, 'verify_expense'])->middleware(CustomAuth::class)->name('verify_expense');
Route::post('/get_expense_data', [App\Http\Controllers\Expense\ExpenseController::class, 'GetExpenseData'])->middleware(CustomAuth::class)->name('get_expense_data');
Route::post('/update_expense', [App\Http\Controllers\Expense\ExpenseController::class, 'UpdateExpense'])->middleware(CustomAuth::class)->name('update_expense');
Route::post('/unverify_exp', [App\Http\Controllers\Expense\ExpenseController::class, 'UnverifyExpense'])->middleware(CustomAuth::class)->name('unverify_exp');
Route::post('/recalculate_exp', [App\Http\Controllers\Expense\ExpenseController::class, 'RecalculateExpense'])->middleware(CustomAuth::class)->name('recalculate_exp');
Route::post('/get-order-exp', [App\Http\Controllers\Expense\ExpenseController::class, 'getOrderExp'])->middleware(CustomAuth::class)->name('get-order-exp');

Route::post('/update-trans-mode', [App\Http\Controllers\Expense\ExpenseController::class, 'updateTransMode'])->middleware(CustomAuth::class)->name('update-trans-mode');
Route::any('/order-expenses', [App\Http\Controllers\Expense\ExpenseController::class, 'order_expenses'])->middleware(CustomAuth::class)->name('order-expenses');
Route::post('/get-order-expense', [App\Http\Controllers\Expense\ExpenseController::class, 'get_order_expense'])->middleware(CustomAuth::class)->name('get-order-expense');

//upload expenses
Route::get('/upload_expenses', [App\Http\Controllers\Expense\ExpenseController::class, 'UploadExpenses'])->middleware(CustomAuth::class)->name('upload_expenses');
Route::any('/check_prev_bal', [App\Http\Controllers\Expense\ExpenseController::class, 'checkPreviousBalance'])->middleware(CustomAuth::class)->name('check_prev_bal');
Route::post('/insert_expense', [App\Http\Controllers\Expense\ExpenseController::class, 'InsertExpense'])->middleware(CustomAuth::class)->name('insert_expense');

Route::post('check-voucher-available', [App\Http\Controllers\Expense\ExpenseController::class, 'checkVoucherAvailable'])->middleware(CustomAuth::class)->name('check-voucher-available');



Route::any('/collectionReport',[App\Http\Controllers\Reports\CollectionReportController::class,'collectionReport'])->middleware(CustomAuth::class)->name('collectionReport');

Route::get('/run-cronjob-command', function () {
    $exitCode = Artisan::call('leadreport:daily');
    $exitCode = Artisan::call('settledUnsettledTask:daily');
    $exitCode = Artisan::call('renewal:reminder');
    dd($exitCode);
    // return what you want
});

//renewal pickup excel export
Route::get('/excel_exp',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'fileExport'])->middleware(CustomAuth::class)->name('excel_exp');
//requested pickups
Route::get('/stop_requested',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'StopRequested'])->middleware(CustomAuth::class)->name('stop_requested');
Route::post('/stop_product_pickup',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class, 'StopPickupRequest'])->middleware(CustomAuth::class)->name('stop_product_pickup');

// Google Ads (Campain Pare)
Route::any('/upload-records',[App\Http\Controllers\Reports\GoogleAdsController::class, 'uploadRecords'])->middleware(CustomAuth::class)->name('uploadRecords');
Route::get('/googleCampaignReport',[App\Http\Controllers\Reports\GoogleAdsController::class, 'googleCampaignReport'])->middleware(CustomAuth::class)->name('googleCampaignReport');
Route::post('/update-details-campaign',[App\Http\Controllers\Reports\GoogleAdsController::class, 'updateDetailsCampaign'])->middleware(CustomAuth::class)->name('update-details-campaign');
// upload-google-campaign-report

Route::any('/virtual_wh_inventory',[App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class,'virtual_wh_inventory'])->middleware(CustomAuth::class)->name('virtual_wh_inventory');

Route::any('/getVendorWarehouse',[App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class,'getVendorWarehouse'])->middleware(CustomAuth::class)->name('getVendorWarehouse');

Route::post('/update-vir-state',[App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class,'update_vir_state'])->middleware(CustomAuth::class)->name('update_vir_state');

Route::get('inventoryVendorMail/{order_ids}',[App\Http\Controllers\VendorInventoryManagement\VendorInventoryController::class,'inventoryVendorMail'])->middleware(CustomAuth::class)->name('inventoryVendorMail');


Route::any('/timeline',[App\Http\Controllers\Reports\TimelineController::class,'getTimelines'])->middleware(CustomAuth::class)->name('getTimelines');
Route::any('/order_timeline/{order_id}',[App\Http\Controllers\Reports\TimelineController::class,'orderTimeLine'])->middleware(CustomAuth::class)->name('order_timeline');
Route::any('/lead_timeline/{lead_id}',[App\Http\Controllers\Reports\TimelineController::class,'leadTimeLine'])->middleware(CustomAuth::class)->name('lead_timeline');

Route::any('/fy_report',[App\Http\Controllers\Reports\TimelineController::class,'fy_report'])->middleware(CustomAuth::class)->name('fy_report');

//B2b user rates
// Route::any('b2b-user-rate',[App\Http\Controllers\B2BController\B2BController::class,'userRates'])->middleware(CustomAuth::class)->name('b2b-user-rate');
// Route::post('b2b-addproduct-rate',[App\Http\Controllers\B2BController\B2BController::class,'addProductRate'])->middleware(CustomAuth::class)->name('b2b-addproduct-rate');
// Route::post('b2b-editproduct-rate',[App\Http\Controllers\B2BController\B2BController::class,'editProductRate'])->middleware(CustomAuth::class)->name('b2b-editproduct-rate');
// Route::post('b2b-removeproduct-rate',[App\Http\Controllers\B2BController\B2BController::class,'reomveProductRate'])->middleware(CustomAuth::class)->name('b2b-removeproduct-rate');

Route::get('/monthly_records', [App\Http\Controllers\Reports\MonthlyReportController::class, 'monthly_records'])->middleware(CustomAuth::class)->name('monthly_records');
Route::any('report-addmonthly-record', [App\Http\Controllers\Reports\MonthlyReportController::class, 'addMonthlyRecord'])->middleware(CustomAuth::class)->name('report-addmonthly-record');

Route::get('report-dashboard', [App\Http\Controllers\Reports\DashboardController::class, 'dashbordView'])->middleware(CustomAuth::class)->name('report-dashboard');

Route::get('/getConvCustomers/{order_id}/{pickup_date}',[App\Http\Controllers\DeliveryManagement\DeliveryController::class,'getConvCustomers'])->middleware(CustomAuth::class)->name('getConvCustomers');
Route::any('b2b-user-add',[App\Http\Controllers\B2BController\B2BController::class,'add'])->middleware(CustomAuth::class)->name('b2b-user-add');
Route::any('b2b-user-view',[App\Http\Controllers\B2BController\B2BController::class,'view'])->middleware(CustomAuth::class)->name('b2b-user-view');
Route::any('b2b-user-view-all',[App\Http\Controllers\B2BController\B2BController::class,'viewAll'])->middleware(CustomAuth::class)->name('b2b-user-view-all');
Route::any('b2b-user-update',[App\Http\Controllers\B2BController\B2BController::class,'update'])->middleware(CustomAuth::class)->name('b2b-user-update');
Route::any('b2b-user-delete',[App\Http\Controllers\B2BController\B2BController::class,'delete'])->middleware(CustomAuth::class)->name('b2b-user-delete');
Route::post('b2b-user-active',[App\Http\Controllers\B2BController\B2BController::class,'active'])->middleware(CustomAuth::class)->name('b2b-user-active');
Route::post('b2b-user-password-change',[App\Http\Controllers\B2BController\B2BController::class,'passwordChange'])->middleware(CustomAuth::class)->name('b2b-user-password-change');

//testing or side bar route
Route::get('sidebar-test',[App\Http\Controllers\TestController\TestingController::class,'sidebar'])->middleware(CustomAuth::class)->name('sidebar-test');


Route::post('addLabourCharges',[App\Http\Controllers\OrderManagement\OrderController::class,'addLabourCharges'])->middleware(CustomAuth::class)->name('addLabourCharges');

//new renewal pickup

Route::any('renewalpickup-test',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class,'renewalPickupTest'])->middleware(CustomAuth::class)->name('renewalpickup-test');

Route::any('renewalpickup-test1',[App\Http\Controllers\RenewalPickup\RenewalPickupController1::class,'renewalPickupTest1'])->middleware(CustomAuth::class)->name('renewalpickup-test1');

Route::any('order-individual_data',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class,'getOrderData'])->middleware(CustomAuth::class)->name('order-individual_data');

Route::any('order-request',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class,'orderRequest'])->middleware(CustomAuth::class)->name('order-request');

Route::post('order-call',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class,'orderCall'])->middleware(CustomAuth::class)->name('order-call');

Route::get('fetchProductDetailsLead',[App\Http\Controllers\Leads\LeadController::class,'fetchProductDetailsLead'])->middleware(CustomAuth::class)->name('fetchProductDetailsLead');

Route::get('expense-report',[App\Http\Controllers\Reports\ExpenseReportController::class,'orderExpenseReport'])->middleware(CustomAuth::class)->name('expense-report');

Route::any('remove-pickup-prod', [App\Http\Controllers\OrderManagement\OrderController::class, 'removePickupProd'])->middleware(CustomAuth::class)->name('remove-pickup-prod');

Route::any('daily-lead-reports',[App\Http\Controllers\Leads\LeadController::class,'leadReportDetails'])->name('daily-lead-reports');

Route::post('edit-order-addr',[App\Http\Controllers\OrderManagement\EditOrderController::class,'updateOrderProduct'])->middleware(CustomAuth::class)->name('edit-order-addr');

Route::post('edit-patient-name',[App\Http\Controllers\OrderManagement\EditOrderController::class,'updateOrderProduct'])->middleware(CustomAuth::class)->name('edit-patient-name');

//closed order get data
Route::any('order-data', [App\Http\Controllers\OrderManagement\OrderController::class, 'orderData'])->name('order-data');

//pickup request order send whatsapp msg link get bank details
Route::any('cust-bank/{link_id}', [App\Http\Controllers\Customer\CustomerController::class, 'getBankDetails'])->name('cust-bank');

Route::any('order-search',[App\Http\Controllers\OrderManagement\OrderController::class,'ordersearch'])->middleware(CustomAuth::class)->name('order-search');

Route::any('generate-invoice',[App\Http\Controllers\OrderManagement\OrderController::class,'generate_delivery_invoice'])->middleware(CustomAuth::class)->name('generate-invoice');

Route::any('generate-invoice-renew',[App\Http\Controllers\OrderManagement\OrderController::class,'generate_renewal_invoice'])->middleware(CustomAuth::class)->name('generate-invoice-renew');

Route::get('renewal-invoices',[App\Http\Controllers\OrderManagement\OrderController::class, 'renewal_invoices'])->middleware(CustomAuth::class)->name('renewal-invoices');

// Route::any('generate-invoice-renew',[App\Http\Controllers\OrderManagement\OrderController::class,'generate_renewal_invoice'])->middleware(CustomAuth::class)->name('generate-invoice-renew');

// Route::get('renewal-invoices',[App\Http\Controllers\OrderManagement\OrderController::class, 'renewal_invoices'])->middleware(CustomAuth::class)->name('renewal-invoices');

Route::get('edit-renewal',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'editRenewal'])->middleware(CustomAuth::class)->name('edit-renewal');
Route::POST('update-renewal',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'updateRenewal'])->middleware(CustomAuth::class)->name('update-renewal');


Route::get('run-cron-job',[App\Http\Controllers\TestController\TestController::class, 'runCronJob'])->middleware(CustomAuth::class)->name('run-cron-job');

Route::any('order-maintenance', [App\Http\Controllers\MaintenanceOrder\OrderController::class, 'createOrder'])->name('order-maintenance');
Route::any('order-maintenance-customers', [App\Http\Controllers\MaintenanceOrder\OrderController::class, 'searchCustomer'])->name('order-maintenance-customers');
Route::any('order-customer-live-products', [App\Http\Controllers\MaintenanceOrder\OrderController::class, 'customerProducts'])->name('order-customer-live-products');
Route::any('order-maintenance-create', [App\Http\Controllers\MaintenanceOrder\OrderController::class, 'generateOrder'])->name('order-maintenance-create');
Route::any('order-maintenance-view/{order_id}', [App\Http\Controllers\MaintenanceOrder\OrderController::class, 'getOrderData'])->name('order-maintenance-view');
Route::any('order-maintenance-update/{order_id}', [App\Http\Controllers\MaintenanceOrder\OrderController::class, 'updateOrderData'])->name('order-maintenance-update');
Route::any('order-maintenance-cancel', [App\Http\Controllers\MaintenanceOrder\OrderController::class, 'cancelOrder'])->name('order-maintenance-cancel');

Route::get('run-test',[App\Http\Controllers\TestController\TestController::class, 'Test'])->middleware(CustomAuth::class)->name('run-test');


Route::any('generate-dummy-invoice',[App\Http\Controllers\DummyInvoices\DummyInvoicesController::class,'generateDummyInvoice'])->middleware(CustomAuth::class)->name('generate-dummy-invoice');
Route::get('view-all-dummy-invoices',[App\Http\Controllers\DummyInvoices\DummyInvoicesController::class,'viewAllDummyInvoices'])->middleware(CustomAuth::class)->name('view-all-dummy-invoices');
Route::get('view-dummy-invoice',[App\Http\Controllers\DummyInvoices\DummyInvoicesController::class,'viewDummyInvoice'])->middleware(CustomAuth::class)->name('view-dummy-invoice');

Route::get('unsettle-order',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'unsettleOrder'])->middleware(CustomAuth::class)->name('unsettle-order');

Route::post('add-comment',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'addComment'])->middleware(CustomAuth::class)->name('add-comment');

Route::get('new_site_daily_orders1',[App\Http\Controllers\Leads\LeadController::class,'new_site_daily_orders1'])->middleware(CustomAuth::class)->name('new_site_daily_orders1');
Route::get('get-order-images/{orderid}',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'orderImages'])->middleware(CustomAuth::class)->name('get-order-images');

Route::get('generate-expense', [App\Http\Controllers\Expense\ExpenseController::class, 'generateExpenseVoucher'])->middleware(CustomAuth::class)->name('generate-expense');

Route::any('cash-report',[App\Http\Controllers\Expense\ExpenseController::class,'cashReport'])->middleware(CustomAuth::class)->name('cash-report');
Route::any('expense-xml-export',[App\Http\Controllers\Expense\ExpenseController::class,'xmlExport'])->middleware(CustomAuth::class)->name('expense-xml-export');

//Route::get('app-privacy-policy', [App\Http\Controllers\Expense\ExpenseController::class, 'generateExpenseVoucher'])->middleware(CustomAuth::class)->name('app-privacy-policy');

Route::get('/app-privacy-policy', function () {
    return view('app-privacy-policy');
});

Route::any('order-other-expense',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'orderOtherExpense'])->middleware(CustomAuth::class)->name('order-other-expense');

Route::get('/get-order-status-count', [App\Http\Controllers\OrderManagement\OrderController::class, 'getOrderSatusCount'])->middleware(CustomAuth::class)->name('get-order-status-count');
Route::any('order-delivery-all',[App\Http\Controllers\OrderManagement\OrderController::class,'delveryOrdersAll'])->middleware(CustomAuth::class)->name('order-delivery-all');

Route::any('map-product',[App\Http\Controllers\MasterProductManagement\MasterProductController::class,'mapproduct'])->middleware(CustomAuth::class)->name('map-product');
Route::post('crdr-data',[App\Http\Controllers\OrderManagement\EditOrderController::class,'crdrdata'])->middleware(CustomAuth::class)->name('crdr-data');
Route::any('adjustment-details',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'adjustmentDetails'])->middleware(CustomAuth::class)->name('adjustment-details');
Route::get('cr-dr-report',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'cr_dr_data'])->middleware(CustomAuth::class)->name('cr-dr-report');

Route::resource('agents',App\Http\Controllers\B2BController\AgentController::class);
Route::resource('b2bcustomers',App\Http\Controllers\B2BController\B2BCustomerController::class);
Route::resource('ccad',App\Http\Controllers\BillingAndPayment\CashCollectionAgaintsDelivery::class);

Route::post('upload-crdr-note-img',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'uploadImage'])->middleware(CustomAuth::class)->name('upload-crdr-note-img');

// After collected and settled update only amount and add amount in cr_dr_table...
Route::get('edit-collection',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'editCollection'])->middleware(CustomAuth::class)->name('edit-collection');

Route::post('update-collection',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'updateCollection'])->middleware(CustomAuth::class)->name('update-collection');

Route::resource('quote',App\Http\Controllers\Quote\Quote::class);
Route::get('quote-pdf-download/{cust_id}',[App\Http\Controllers\Quote\Quote::class,'pdfDownload'])->middleware(CustomAuth::class)->name('quote-pdf-download');

Route::post('replace-order-create',[App\Http\Controllers\MaintenanceOrder\ReplacementController::class,'createOrder'])->middleware(CustomAuth::class)->name('replace-order-create');
Route::get('get-inventory/{stack}/{prodid}/{id}/{wareid?}',[App\Http\Controllers\MaintenanceOrder\ReplacementController::class,'filterInventory'])->middleware(CustomAuth::class)->name('get-inventory');
Route::post('sale-product',[App\Http\Controllers\MaintenanceOrder\ReplacementController::class,'sale_product'])->middleware(CustomAuth::class)->name('sale-product');
Route::group(['middleware' => 'customauth'], function() {
    Route::resource('replace-order',App\Http\Controllers\MaintenanceOrder\ReplacementController::class);
});
Route::any('add-warehouse-brand',[App\Http\Controllers\OrderManagement\OrderController::class,'addWarehouseBrand'])->middleware(CustomAuth::class)->name('add-warehouse-brand');

Route::post('get-overdue-period',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class,'getOverduePeriod'])->middleware(CustomAuth::class)->name('get-overdue-period');
Route::post('corporate-invoice-nos',[App\Http\Controllers\RenewalPickup\RenewalPickupController::class,'addInvoiceNos'])->middleware(CustomAuth::class)->name('corporate-invoice-nos');
Route::post('corporate-renewal',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'corporateRenewal'])->middleware(CustomAuth::class)->name('corporate-renewal');
Route::get('reverse-adjustment/{id}',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'reverseAdjustment'])->middleware(CustomAuth::class)->name('reverse-adjustment');

Route::get('get-orders-count',[OrderController::class,'getOrdersCount'])->middleware(CustomAuth::class)->name('get-orders-count');

// Delivery Staff management routes...!
Route::get('delivery-staffs',[App\Http\Controllers\UserManagement\UserController::class, 'deliveryStaffs'])->middleware(CustomAuth::class)->name('delivery-staffs');
Route::post('save-staff',[App\Http\Controllers\UserManagement\UserController::class, 'saveStaff'])->middleware(CustomAuth::class)->name('save-staff');
Route::post('update-staff/{id}',[App\Http\Controllers\UserManagement\UserController::class, 'updateStaff'])->middleware(CustomAuth::class)->name('update-staff');

Route::post('fetch-inventory-details',[App\Http\Controllers\OrderManagement\OrderController::class,'fetchInventoryDetails'])->middleware(CustomAuth::class)->name('fetch-inventory-details');
Route::post('order-generate',[App\Http\Controllers\OrderManagement\OrderController::class,'orderGenerate'])->middleware(CustomAuth::class)->name('order-generate');

Route::get('consumables-form/{contact_no}/{order_id}',[App\Http\Controllers\OtherServices\OtherLeadController::class,'consumableForm'])->name('consumables-form');
Route::post('consumables-form-submit',[App\Http\Controllers\OtherServices\OtherLeadController::class,'consumableFormSubmit'])->name('consumables-form-submit');


Route::get('get-activity-log',[App\Http\Controllers\BillingAndPayment\BillingPaymentController::class,'getActivityLog'])->middleware(CustomAuth::class)->name('get-activity-log');
Route::get('pickup-products',[App\Http\Controllers\Reports\RenewalPickupReportController::class,'pickupProducts'])->middleware(CustomAuth::class)->name('pickup-products');
Route::resource('customer-master',App\Http\Controllers\Customer\CustomerController::class);