<?php

use Illuminate\Support\Facades\Route;

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

Route::get('localization/{locale}','LocalizationController@index');

Route::get('/', 'Web\LoginController@index')->name('index');

Route::post('Authenticate', 'Web\LoginController@loginProcess')->name('loginProcess');

Route::get('LogoutProcess', 'Web\LoginController@logoutProcess')->name('logoutprocess');


Route::group(['middleware' => ['UserAuth']], function () {
    Route::post('shopname/edit', 'Web\AdminController@shopnameEdit')->name('shopnameEdit');

    Route::get('ChangePassword-UI', 'Web\LoginController@getChangePasswordPage')->name('change_password_ui');
    Route::put('UpdatePassword', 'Web\LoginController@updatePassword')->name('update_pw');

    //Dashboard List
    Route::get('Inventory-Dashboard', 'Web\InventoryController@getInventoryDashboard')->name('inven_dashboard');
    Route::get('Stock-Dashboard', 'Web\StockController@getStockPanel')->name('stock_dashboard');
    Route::get('Sale-Dashboard', 'Web\SaleController@getSalePanel')->name('sale_panel');
    Route::get('Order-Dashboard', 'Web\OrderController@getOrderPanel')->name('order_panel');
    Route::get('Admin-Dashboard','Web\AdminController@getAdminDashboard')->name('admin_dashboard');

    //Ajax List
    Route::post('AjaxGetItem', 'Web\InventoryController@AjaxGetItem')->name('AjaxGetItem');
    Route::post('AjaxGetCountingUnit', 'Web\InventoryController@AjaxGetCountingUnit')->name('AjaxGetCountingUnit');
    Route::post('getunitprice', 'Web\InventoryController@getunitprice')->name('getunitprice');
    Route::post('getCountingUnitsByItemId', 'Web\SaleController@getCountingUnitsByItemId');
    Route::post('getCountingUnitsByItemCode', 'Web\SaleController@getCountingUnitsByItemCode');
    Route::post('getCustomerInfo', 'Web\AdminController@getCustomerInfo');
    Route::post('ajaxConvertResult', 'Web\InventoryController@ajaxConvertResult');
    Route::post('storeCustomerOrder', 'Web\OrderController@storeCustomerOrder');
    Route::post('getTotalSaleReport', 'Web\AdminController@getTotalSaleReport');
    Route::post('showSubCategory', 'Web\InventoryController@showSubCategory');
    Route::post('AjaxGetCustomerList','Web\AdminController@getSalesCustomerList')->name('AjaxGetCustomerList');
    Route::post('AjaxGetCustomerwID','Web\AdminController@getSalesCustomerWithID')->name('AjaxGetCustomerwID');
    Route::post('AjaxStoreCustomer','Web\AdminController@storeSalesCustomer')->name('AjaxStoreCustomer');
    Route::post('changeCustomerPassword', 'Web\AdminController@changeCustomerPassword');

    Route::post('saleCustomerDelete','SaleCustomerController@delete')->name('saleCustomerDelete');
    Route::get('list','Web\AdminController@show_sale_customer_credit_list')->name('list');

    //Route::get('Sale/shistory','Web\AdminController@history')->name('shistory');
    Route::get('credit/{id}','Web\AdminController@credit')->name('credit');
    Route::post('store_each_paid','Web\AdminController@store_eachPaid')->name('store_each_paid');
    Route::post('store_all_credit/{id}','Web\AdminController@store_allPaid')->name('store_all_credit');


    //Category

	Route::post('category/store', 'Web\InventoryController@storeCategory')->name('category_store');
	Route::post('category/update/{id}', 'Web\InventoryController@updateCategory')->name('category_update');
    Route::post('category/delete', 'Web\InventoryController@deleteCategory');
    Route::get('category', 'Web\InventoryController@categoryList')->name('category_list');

	//SubCategory
	// Route::get('subcategory', 'Web\InventoryController@subcategoryList')->name('subcategory_list');
	// Route::post('subcategory/store', 'Web\InventoryController@storeSubCategory')->name('sub_category_store');
	// Route::post('subcategory/update/{id}', 'Web\InventoryController@updateSubCategory')->name('sub_category_update');


    //Item
	Route::get('item', 'Web\InventoryController@itemList')->name('item_list');
	Route::post('item/store', 'Web\InventoryController@storeItem')->name('item_store');
	Route::post('item/update/{id}', 'Web\InventoryController@updateItem')->name('item_update');
	Route::post('item/delete', 'Web\InventoryController@deleteItem');
    Route::get('expired','Web\InventoryController@getExpiredItem')->name('expired_item_list');
    Route::post('AjaxGetCountingUnit_by_ExpireStatus', 'Web\InventoryController@AjaxGetCountingUnit_by_ExpireStatus')->name('AjaxGetCountingUnit_by_ExpireStatus');

    //Counting Unit
	Route::get('Count-Unit/{item_id}', 'Web\InventoryController@getUnitList')->name('count_unit_list');
    Route::post('Count-Unit/store', 'Web\InventoryController@storeUnit')->name('count_unit_store');
    Route::post('Count-Unit/update/{id}', 'Web\InventoryController@updateUnit')->name('count_unit_update');
    Route::post('Count-Unit/code_update/{id}', 'Web\InventoryController@updateUnitCode')->name('count_unit_code_update');
    Route::post('Count-Unit/original_code_update/{id}', 'Web\InventoryController@updateOriginalCode')->name('original_code_update');
    Route::post('Count-Unit/delete', 'Web\InventoryController@deleteUnit');

    //Counting Unit Relation
    Route::get('Unit-Relation/{item_id}', 'Web\InventoryController@unitRelationList')->name('unit_relation_list');
    Route::post('Unit-Relation/store', 'Web\InventoryController@storeUnitRelation')->name('unit_relation_store');
    Route::post('Unit-Relation/update/{id}', 'Web\InventoryController@updateUnitRelation')->name('unit_relation_update');

    //Counting Unit Conversion
    Route::get('Unit-Convert/{unit_id}', 'Web\InventoryController@convertUnit')->name('convert_unit');
    //Route::post('Unit-Convert/store', 'Web\InventoryController@convertCountUnit')->name('convert_count_unit');

    //item adjust
    Route::get('item-adjust', 'Web\StockController@itemadjust')->name('itemadjust');

    //StockCount
    Route::get('Stock-Count/Count', 'Web\StockController@getStockCountPage')->name('stock_count');
    Route::get('Stock-Count/CountExport', 'Web\StockController@stockCountExport')->name('stockcountexport');
    Route::get('stocklists', 'Web\StockController@getstocklists')->name('stock_lists');
    Route::get('Stock-Count/Price', 'Web\StockController@getStockPricePage')->name('stock_price_page');
    Route::get('Stock-Count/Reorder', 'Web\StockController@getStockReorderPage')->name('stock_reorder_page');
    Route::post('Stock-Count/UpdateCount', 'Web\StockController@updateStockCount')->name('update_stock_count');
    Route::post('Stock-Count/UpdatePrice', 'Web\StockController@updateStockPrice')->name('update_stock_price');

    //Employee
    Route::get('Employee', 'Web\AdminController@getEmployeeList')->name('employee_list');
    Route::post('Employee/store', 'Web\AdminController@storeEmployee')->name('employee_store');
    Route::get('Employee/details/{id}', 'Web\AdminController@getEmployeeDetails')->name('employee_details');
    Route::post('employee-update', 'Web\AdminController@employeeupdate')->name('employee.update');

    //Customer
    Route::get('Customer', 'Web\AdminController@getCustomerList')->name('customer_list');
    Route::post('Customer/store', 'Web\AdminController@storeCustomer')->name('store_customer');
    Route::get('Customer/details/{id}', 'Web\AdminController@getCustomerDetails')->name('customer_details');
    Route::post('Customer/update/{id}', 'Web\AdminController@updateCustomer')->name('customer_update');
    Route::post('Customer/Change-Level', 'Web\AdminController@changeCustomerLevel')->name('change_customer_level');

    //Sale
    Route::get('Sale', 'Web\SaleController@getSalePage')->name('sale_page');
    Route::post('Sale/Voucher', 'Web\SaleController@storeVoucher');
    Route::post('Sale/Get-Voucher', 'Web\SaleController@getVucherPage')->name('get_voucher');
    Route::get('Sale/History', 'Web\SaleController@getSaleHistoryPage')->name('sale_history');
    Route::get('Sale/SummaryMain','Web\SaleController@getVoucherSummaryMain')->name('voucher_summary_main');
    Route::post('Sale/SummaryDetail','Web\SaleController@searchItemSalesByDate')->name('search_item_sales_by_date');
    Route::post('Sale/Search-History', 'Web\SaleController@searchSaleHistory')->name('search_sale_history');
    Route::get('export-salehistory/{from}/{to}/{id}/{sales}/{data_type}/{type}', 'Web\SaleController@saleHistoryExport')->name('salehistoryexport');
    Route::post('serarch-item-adjusts', 'Web\SaleController@searchItemAdjusts')->name('search_item_adjusts');
    Route::post('voucher-delete', 'Web\SaleController@voucherDelete')->name('voucher_delete');
    Route::get('serarch-item-adjusts', function () {
        return redirect()->route('itemadjust-lists');
    });
    Route::post('Sale/search_sale_discount_record', 'Web\SaleController@search_sale_discount_record')->name('search_sale_discount_record');
    Route::get('Sale/Voucher-Details/{id}', 'Web\SaleController@getVoucherDetails')->name('getVoucherDetails');
    Route::get('discount_record_list','Web\SaleController@show_discount_list')->name('discount_record_list');
    Route::post('getSelectionDiscount','Web\SaleController@show_discount_type')->name('getSelectionDiscount');
    Route::post('getDateDiscount','Web\SaleController@show_discount_date')->name('getDateDiscount');
    Route::post('get_discount_main_type','Web\SaleController@ajax_get_discount_main')->name('get_discount_main_type');
    Route::post('get_foc','Web\SaleController@ajax_get_foc')->name('get_foc');
    Route::post('get_item','Web\SaleController@ajax_get_item')->name('get_item');
    Route::post('get_vou','Web\SaleController@ajax_get_vou')->name('get_vou');
    Route::post('get_date','Web\SaleController@ajax_get_date')->name('get_date');
    Route::post('getSalesItems','Web\SaleController@getSalesItems')->name('getSalesItems');
    //Order
    Route::get('Order/{type}', 'Web\OrderController@getOrderPage')->name('order_page');
    Route::get('Order-Details/{id}', 'Web\OrderController@getOrderDetailsPage')->name('order_details');
    Route::post('Order/Change', 'Web\OrderController@changeOrderStatus')->name('update_order_status');
    Route::get('Order/Voucher/History', 'Web\OrderController@getOrderHistoryPage')->name('order_history');
    Route::post('Order/Voucher/Search-History', 'Web\OrderController@searchOrderVoucherHistory')->name('search_order_history');
    Route::get('Sale/Search-History', 'Web\SaleController@searchSaleHistoryget');
    Route::get('Order/Voucher-Details/{id}', 'Web\OrderController@getVoucherDetails')->name('voucher_order_details');

    Route::post('mobile-print','Web\AdminController@mobileprint');
    //Purchase
    Route::get('Purchase', 'Web\AdminController@getPurchaseHistory')->name('purchase_list');
    Route::get('Purchase/Details/{id}', 'Web\AdminController@getPurchaseHistoryDetails')->name('purchase_details');
    Route::get('Purchase/Create', 'Web\AdminController@createPurchaseHistory')->name('create_purchase');
    Route::post('Purchase/Store', 'Web\AdminController@storePurchaseHistory')->name('store_purchase');
    Route::post('store_supplier', 'Web\AdminController@store_supplier')->name('store_supplier');
    Route::get('add_supplier', 'Web\AdminController@add_supplier')->name('add_supplier');
    Route::get('suppliercreditlist','Web\AdminController@show_supplier_credit_lists')->name('supplier_credit_list');
    Route::get('supcredit/{id}','Web\AdminController@supplier_credit')->name('supcredit');
    Route::post('store_each_paid_supplier','Web\AdminController@store_eachPaidSupplier')->name('store_each_paid_supplier');
    Route::post('store_all_suppliercredit/{id}','Web\AdminController@store_allSupplierPaid')->name('store_all_suppliercredit');
    Route::post('getPurchaseData','Web\AdminController@getPurchase_Info')->name('getPurchaseData');
    Route::post('getsell_end','Web\AdminController@getsell_end_info')->name('getsell_end');


    //financial
    Route::get('export-welldoneHistory/{from}/{to}/{id}','Web\AdminController@welldoneHistoryExport')->name('welldonehistroyexport');
    Route::get('export-payhistory/{from}/{to}/{id}','Web\AdminController@payableHistoryExport')->name('payablehistoryexport');
    Route::get('export-expensehistory/{from}/{to}', 'Web\AdminController@expenseHistoryExport')->name('expensehistoryexport');
    Route::get('searchIncomeHistory','Web\AdminController@incomeHistoryExport')->name('incomehistoryexport');

    Route::get('fixasset', 'Web\AdminController@showFixasset')->name('fixasset');
    Route::get('show_capital', 'Web\AdminController@show_capitalPanel')->name('show_capital');

    Route::post('store_capital', 'Web\AdminController@store_capitalInfo')->name('store_capital');
    Route::get('addasset', 'Web\AdminController@addasset')->name('addasset');
    Route::get('Financial', 'Web\AdminController@getTotalSalenAndProfit')->name('financial');

    Route::get('Expenses', 'Web\AdminController@expenseList')->name('expenses');
    Route::post('storeExpense', 'Web\AdminController@storeExpense')->name('store_expense');
    Route::post('updateExpense/{id}', 'Web\AdminController@updateExpense')->name('update_expense');
    Route::post('deleteExpense', 'Web\AdminController@deleteExpense')->name('delete_expense');
    Route::post('searchExpenseHistory', 'Web\AdminController@searchExpenseHistory')->name('search_expense_history');

    Route::post('store_asset', 'Web\AdminController@storeAsset')->name('store_asset');
    Route::post('store_sell_end', 'Web\AdminController@storeSellEnd')->name('store_sell_end');
    Route::post('store_reinvest','Web\AdminController@store_reinvest_info')->name('store_reinvest');
    Route::post('store_withdraw','Web\AdminController@store_withdraw_info')->name('store_withdraw');


    Route::get('Incomes', 'Web\AdminController@incomeList')->name('incomes');
Route::post('storeIncome', 'Web\AdminController@storeIncome')->name('store_income');
Route::post('searchIncomeHistory', 'Web\AdminController@searchIncomeHistory')->name('search_income_history');
Route::post('updateIncome/{id}', 'Web\AdminController@updateIncome')->name('update_income');
Route::post('deleteIncome', 'Web\AdminController@deleteIncome')->name('delete_income');

    Route::get('receivable_payable', 'Web\AdminController@getReceivablePayable')->name('receivable_payable_lists');
    Route::post('welldone/search','Web\AdminController@search_welldone')->name('serach_welldone_history');
    Route::post('payable/newsearch', 'Web\AdminController@search_payable_bydate')->name('search_payable_bydate');

    Route::post('getTotalSaleReport', 'Web\AdminController@getTotalSaleReport');
    Route::post('getTotalIncome', 'Web\AdminController@getTotalIncome');

    //delete for sale customer from vouncher blade
    route::get('delete_saleuser/{id}','Web\SaleController@delete_saleuser')->name('delete_saleuser');

    Route::get('new-asset', 'Web\AdminController@getnewAsset')->name('get_new_asset');


    Route::get('wayPlanning', 'Web\DeliveryController@wayPlaningForm')->name('way_planing_form');

    Route::get('wayPlanningLists', 'Web\DeliveryController@wayPlaningLists')->name('way_planing_lists');

    Route::post('wayplanning/store', 'Web\DeliveryController@wayplanningstore')->name('wayplanning.store');

    Route::post('deliveryorder/receive/store', 'Web\DeliveryController@deliveryOrderReceiveStore')->name('deliveryorderreceive.store');



    Route::get('shop-lists', 'Web\DeliveryController@getshopList');

    Route::get('Admin/Shop/{id}', 'Web\DeliveryController@SalePage')->name('admin_sale_page');
    Route::post('testVoucher', 'Web\DeliveryController@storetestVoucher');
    Route::post('getItemForA5', 'Web\DeliveryController@getItemA5')->name('getItemForA5');


	Route::get('item-assign', 'Web\InventoryController@itemAssign')->name('item_assign');
	Route::post('assign-item-ajax', 'Web\InventoryController@itemAssignajax')->name('item_assign_ajax');
	Route::post('assign-itemshop', 'Web\InventoryController@itemAssignShop');

	Route::post('stockupdate-ajax', 'Web\StockController@stockUpdateAjax')->name('stockupdate-ajax');
	Route::post('pharmpriceupdate-ajax', 'Web\StockController@pharmpriceUpdateAjax')->name('pharmpriceupdate-ajax');
	Route::post('patientpriceupdate-ajax', 'Web\StockController@patientpriceUpdateAjax')->name('patientpriceupdate-ajax');

	Route::post('purchseupdate-ajax', 'Web\StockController@purchaseUpdateAjax')->name('purchaseupdate-ajax');
	Route::post('itemadjust-ajax', 'Web\StockController@itemadjustAjax')->name('itemadjust-ajax');
	Route::get('itemadjust-lists', 'Web\StockController@itemadjustLists')->name('itemadjust-lists');

	Route::get('fixedasset-lists', 'Web\AdminController@getFixedAssets')->name('fixedasset-lists');

    Route::get('itemrequestlists', 'Web\AdminController@itemrequestlists')->name('itemrequestlists');
    Route::post('store_itemrequest', 'Web\AdminController@store_itemrequest')->name('store_itemrequest');

    Route::get('itemrequest/details/{id}', 'Web\AdminController@getRequestHistoryDetails')->name('request_details');
    Route::get('create/itemrequest', 'Web\AdminController@create_itemrequest')->name('create_itemrequest');
    Route::post('requestitems/send', 'Web\AdminController@requestitemssend')->name('requestitemssend');
    Route::post('purchaseprice/update', 'Web\AdminController@purchasepriceUpdate')->name('purchasepriceupdate');
    Route::post('delete_units', 'Web\AdminController@delete_units')->name('delete_units');


    Route::post('purchase_delete', 'Web\AdminController@purchaseDelete')->name('purchase_delete');

    //Clinic
    Route::get('patient/register', 'Web\AdminController@patientregister')->name('patientregister');
    Route::post('appointment/store', 'Web\ClinicController@appointmentStore')->name('appointmentstore');
    Route::post('searchpatient', 'Web\ClinicController@searchpatient');
    Route::post('searchpatient/todayappointments', 'Web\ClinicController@searchpatientToday');
    Route::post('oldpatient/appointment', 'Web\ClinicController@oldpatientAppointment')->name('appointment.oldpatient');
    Route::get('appointments/{patient_id}', 'Web\ClinicController@appointments')->name('appointments');
    Route::get('patient-profile/{patient_id}', 'Web\ClinicController@patientProfile')->name('patient_profile');
      Route::get('patient-delete/{patient_id}', 'Web\ClinicController@patientDelete')->name('patient_delete');
    Route::post('patient-profile-update', 'Web\ClinicController@patientProfileUpdate')->name('patient_profile_update');
    Route::get('patient/history/{appointment_id}', 'Web\ClinicController@patientHistory')->name('patienthist');
    Route::get('records/{appointment_id}', 'Web\ClinicController@appointmentRecord')->name('appointmentRecord');
    Route::get('appointments', 'Web\ClinicController@todayAppointments')->name('today.appointments');
      Route::get('delete_patient', 'Web\ClinicController@todAppointments')->name('todappointments');
    Route::post('searchAppointments/filter', 'Web\ClinicController@searchAppointments')->name('filter');
    Route::post('store/record', 'Web\ClinicController@storeRecord')->name('storeRecord');
    Route::post('store/recordinfo', 'Web\ClinicController@storeRecordInfo')->name('storeRecordInfo');
    Route::post('attachments/store', 'Web\ClinicController@attachmentsStore')->name('attachments.store');
    Route::post('attachments/delete', 'Web\ClinicController@attachmentsDelete')->name('attachments.delete');
    Route::get('Diagnosis', 'Web\ClinicController@getDiagnosis')->name('getDiagnosis');
	Route::post('Diagnosis/store', 'Web\ClinicController@diagnosisStore')->name('diagnosis_store');
    Route::post('Diagnosis/storeOntime', 'Web\ClinicController@diagnosisStoreOntime')->name('diagnosis_store_ontime');
    Route::get('DepartmentList', 'Web\ClinicController@DepartmentList')->name('department_list');

	Route::get('CreateDepartment', 'Web\ClinicController@CreateDepartment')->name('create_department');

	Route::post('StoreDepartment', 'Web\ClinicController@StoreDepartment')->name('store_department');

	Route::get('EditDepartment/{department}', 'Web\ClinicController@EditDepartment')->name('edit_department');

	Route::put('UpdateDepartment/{department}', 'Web\ClinicController@UpdateDepartment')->name('update_department');

    //Service
    Route::get('services','Web\ServiceController@serviceList')->name('services.lists');
    Route::post('services/update/{id}','Web\ServiceController@serviceUpdate')->name('services.update');
    Route::post('services/store','Web\ServiceController@serviceStore')->name('services.store');
    Route::post('services/delete','Web\ServiceController@serviceDelete')->name('services.delete');
    Route::post('servicesAjax','Web\ServiceController@serviceAjax')->name('service_tab');

    //Package
    Route::get('packages','Web\ServiceController@packageList')->name('packages.lists');
    Route::post('packages/update/{id}','Web\ServiceController@packageUpdate')->name('packages.update');
    Route::post('packages/store','Web\ServiceController@packageStore')->name('packages.store');
    Route::post('packages/delete','Web\ServiceController@packageDelete')->name('packages.delete');

    //Procedure Group
    Route::get('procuduregroup', 'Web\ClinicController@getprocuduregroup')->name('getprocuduregroup');
	Route::post('procuduregroup/store', 'Web\ClinicController@procuduregroupStore')->name('procuduregroup_store');

    //Procedure Item
    Route::get('procudureitem', 'Web\ClinicController@getprocedureItem')->name('getprocedureitem');
	Route::post('procudureitem/store', 'Web\ClinicController@procedureItemStore')->name('procedureitem_store');
    Route::get('procudureitem/delete/{id}', 'Web\ClinicController@procedureItemDelete')->name('procedureitem_delete');
    Route::post('procudureitem/update/{id}', 'Web\ClinicController@procedureItemUpdate')->name('procedureitem_update');

    //Room Usage
    Route::get('roomusage', 'Web\ClinicController@getroomusage')->name('getroomusage');
	Route::post('roomusage/store', 'Web\ClinicController@roomusageStore')->name('roomusage_store');

    //Doctor
    Route::get('DoctorList', 'DoctorController@DoctorList')->name('doctor_list');
    Route::get('CreateDoctor', 'DoctorController@CreateDoctor')->name('create_doctor');
    Route::post('StoreDoctor', 'DoctorController@StoreDoctor')->name('store_doctor');
    Route::get('doctor/profile', 'DoctorController@doctorProfile')->name('doc.profile');
    Route::get('DoctorScheduleList', 'DoctorController@DoctorScheduleList')->name('doctor.schedulelist');

    //Doctor Schedule
    Route::get('ScheduleList', 'DoctorController@ScheduleList')->name('schedule_list');
    Route::get('CreateScheduleDay', 'DoctorController@CreateScheduleDay')->name('create_schedule_day');
    Route::post('AjaxDepartment', 'DoctorController@AjaxDepartment')->name('AjaxDepartment');
    Route::post('StoreScheduleDay', 'DoctorController@StoreScheduleDay')->name('store_schedule_day');
    Route::get('CheckDoctorProfile/{doctor}', 'DoctorController@CheckDoctorProfile')->name('check_doctor_profile');
    Route::get('editDoctor/{id}', 'DoctorController@editDoctor')->name('edit_doctor');
    Route::post('edit/StoreDoctor', 'DoctorController@editStoreDoctor')->name('edit_store_doctor');
    Route::get('CheckScheduleTime/{day}/{doctor}', 'DoctorController@CheckScheduleTime')->name('check_schedule_time');

    // Clinic Sale Page
    Route::get('ClinicSale', 'Web\ClinicController@getClinicSalePage')->name('clinic_sale_page');
    Route::post('searchItem', 'Web\ClinicController@searchItem');
    Route::get('ClinicSale/History', 'Web\ClinicController@getClinicSaleHistoryPage')->name('clinic_sale_history');
    Route::post('ClinicSale/Search-History', 'Web\ClinicController@searchClinicSaleHistory')->name('search_clinic_sale_history');
    Route::get('Sale/ClinicVoucher-Details/{id}', 'Web\ClinicController@getClinicVoucherDetails')->name('getClinicVoucherDetails');
    Route::post('Sale/Get-ClinicVoucher', 'Web\ClinicController@getClinicVucherPage')->name('get_clinic_voucher');

    //Clinic Patient History
    Route::get('clinichistory', 'Web\ClinicController@history')->name('history');
    Route::get('patientlistexport/{from}/{to}','Web\ClinicController@patientlistexport')->name('patientlistexport');

    //Medical Record
    Route::post('medicalrecord', 'Web\ClinicController@medicalrecord')->name('medicalrecord');
    Route::post('attachmentimage', 'Web\ClinicController@attachimg')->name('attachimg');
    //Canvas Test
    Route::get('/canvas','Web\ClinicController@canvas');

    //Voucher
    Route::post('clinic/storevoucher', 'Web\ClinicController@storeVoucher')->name('clinic.storevoucher');
});

Route::get('/excel', function () {
    return view('Admin.execel');
});
Route::post('execelImport', 'Web\ImportController@execelImport')->name('execelImport');
