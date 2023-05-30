<?php

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

Route::get('/', 'UserController@index')->name('login');

Route::post('/authorization/login', 'Auth\LoginController@authorization')->name('authorization.login');
// FIXME: FORGOT PASSWORD. NEEDED.
/*Route::get('/forgot-password', 'Auth\ForgotPassword2Controller@forgotPassword')->name('forgot-password');
Route::post('/forgot-password-email', 'Auth\ForgotPassword2Controller@forgotPasswordEmail')->name('forgot-password-email');
Route::get('/reset-password/{pin}', 'Auth\ForgotPassword2Controller@resetPassword')->name('reset-password');
Route::post('/reset-password/{pin}', 'Auth\ForgotPassword2Controller@resetPasswordPost')->name('reset-password-post');*/
// FIXME: FORGOT PASSWORD. NEEDED END

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', 'has\DashboardController@index')->name('dashboard');

    Route::get('/user/change-password', function () {
        return view('resetPassword');
    });
    Route::post('/user/change-password', 'Auth\ResetPasswordController@resetPassword')->name('user.reset-password');

    Route::post('/report/render/{title}', 'Report\OraclePublisherController@render')->name('report');
    Route::get('/report/render/{title?}', 'Report\OraclePublisherController@render')->name('report-get');

    Route::get('/load-building-list/{id}', 'has\AjaxController@loadBuildingList')->name('load-building-list');

    Route::post('/authorization/logout', 'Auth\LoginController@logout')->name('logout');
    // Example of authorization middleware ->middleware('app.authorize:CAN_SEE_ALL_DEPARTMENT,CAN_SEE_ANY_DEPARTMENT') // App Authorization for CPA
    Route::group(['prefix' => 'colony', 'name' => 'colony'], function () {
        Route::group(['name' => 'register.','as' => 'colony.'], function () {
            Route::get('/', 'has\colony\ColonyController@index')->name('index');
            Route::get('/index', 'has\colony\ColonyController@index')->name('index');
            Route::get('/colony-register', 'has\colony\ColonyController@colony_register')->name('colony_register'); //->middleware('app.authorize:CAN_ADD_COLONY');
            Route::get('/report-colony-register', 'has\colony\ColonyController@report_colony_register')->name('report_colony_register');
            Route::post('/colony-register', 'has\colony\ColonyController@store')->name('store');
            Route::get('/colony-register/load/{id}', 'has\colony\ColonyController@loadColonyRegister')->name('load_colony');

            Route::get('/load-division-to-district/{id}', 'has\colony\ColonyController@load_division_to_district')->name('load_division_to_district');
            Route::get('/load-district-to-thana/{id}', 'has\colony\ColonyController@load_district_to_thana')->name('load_district_to_thana');

            Route::get('/colony-datatable-list', 'has\colony\ColonyController@datatableList')->name('datatable-list');
            Route::get('/colony-register/{id}', 'has\colony\ColonyController@edit')->name('edit');  //->middleware('app.authorize:CAN_EDIT_COLONY');
            Route::put('/colony-register/update/{id}', 'has\colony\ColonyController@update')->name('update');
        });
    });

    Route::group(['name' => 'flat-name-entry', 'as' => 'flat-name-entry.'], function () {
        Route::get('/flat-name-entry', 'has\FlatNameEntryController@index')->name('flat-name-entry-index');
        Route::get('/flat-name-entry/{id}', 'has\FlatNameEntryController@edit')->name('flat-name-entry-edit');
        Route::post('/flat-name-entry', 'has\FlatNameEntryController@post')->name('flat-name-entry-post');
        Route::put('/flat-name-entry/{id}', 'has\FlatNameEntryController@update')->name('flat-name-entry-update');
//        Route::get('/flat-name-entry/delete/{id}', 'has\FlatNameEntryController@delete')->name('flat-name-entry-delete');
        Route::post('/flat-name-entry-datatable-list', 'has\FlatNameEntryController@dataTableList')->name('flat-name-entry-datatable-list');
    });

    Route::group(['name' => 'house.', 'as' => 'house.'], function () {
        // Basic routes
        Route::get('/houses', 'has\HouseController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_HOUSE');
        Route::get('/houses-datatable-list', 'has\HouseController@datatableList')->name('datatable-list');
        Route::get('/temp-datatable-list', 'has\HouseController@tempDatatableList')->name('temp-datatable-list');
        Route::get('/houses/{id}', 'has\HouseController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_HOUSE');
        Route::post('/houses', 'has\HouseController@store')->name('store');
        Route::get('/houses-delete', 'has\HouseController@delete')->name('delete');
        Route::post('/houses-permanent', 'has\HouseController@permanent')->name('permanent');
        Route::put('/houses/{id}', 'has\HouseController@update')->name('update');

        // Supporting routes
        Route::get('/houses/load/{id}', 'has\HouseController@load')->name('load');
        Route::get('/houses/load-data/{buildingId}', 'has\HouseController@loadData')->name('load-data');
    });

    Route::group(['name' => 'building.', 'as' => 'building.'], function () {
            Route::get('/buildings', 'has\building\BuildingController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_BUILDING');
            Route::get('/buildings-datatable-list', 'has\building\BuildingController@datatableList')->name('datatable-list');
            Route::get('/buildings/{id}', 'has\building\BuildingController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_BUILDING');
            Route::post('/buildings', 'has\building\BuildingController@store')->name('store');
            Route::put('/buildings/update/{id}', 'has\building\BuildingController@update')->name('update');
    });
//
    Route::group(['name' => 'ha-application.', 'as' => 'ha-application.'], function () {
        // Basic routes
        Route::get('/ha-applications', 'has\HaApplicationController@index')->name('index'); //->middleware('app.authorize:CAN_APPLY_FOR_HOUSE_ALLOTMENT');
        Route::get('/ha-applications-datatable-list', 'has\HaApplicationController@datatableList')->name('datatable-list');
        Route::get('/ha-applications/{id}', 'has\HaApplicationController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_HOUSE_ALLOTMENT_APPLICATION');
        Route::post('/ha-applications', 'has\HaApplicationController@store')->name('store');
        Route::put('/ha-applications/{id}', 'has\HaApplicationController@update')->name('update');
        Route::get('/get-advertise-flat-data/{adv_id}/{house_type_id}', 'has\HaApplicationController@ajaxAdvertisementFlatData')->name('ajaxHouseData');

    });

    Route::group(['name' => 'advertisement.', 'as' => 'advertisement.'], function () {
        Route::get('/advertisements', 'has\AdvertisementController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_ADVERTISEMENT');
        Route::get('/advertisement-datatable-list', 'has\AdvertisementController@datatableList')->name('datatable-list');
//        Route::get('/advertisement-datatable-house/{ack_id}', 'has\AdvertisementController@datatableHouse')->name('datatable-house');
        Route::get('/advertisement-datatable-house/{dpt_id}/{house_type}', 'has\AdvertisementController@datatableHouse')->name('datatable-house');
        Route::get('/advertisements/{id}', 'has\AdvertisementController@loadHouseListForAdvertisementToedit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_ADVERTISEMENT');
        Route::get('/advertisements-ack-validity/{ack_id}', 'has\AdvertisementController@adAckValidity')->name('advertisements-ack-validity');
        Route::get('/advertisements-list/{id}', 'has\AdvertisementController@loadHouseListForAdvertisement')->name('loadHouseListForAdvertisement');
        Route::post('/advertisements', 'has\AdvertisementController@store')->name('store');
        Route::put('/advertisements/{id}', 'has\AdvertisementController@update')->name('update');
        Route::get('/advertisement/status/{id}', 'has\AdvertisementController@changeStatus')->name('changeStatus');// New route for advertisement In-active
    });

    Route::group(['name' => 'search_advertisements.', 'as' => 'search_advertisement.'], function () {
        Route::get('/search_advertisements', 'has\AdvertisementController@searchAdvertisements')->name('searchAdvertisements');
        Route::get('/advertisement-list', 'has\AdvertisementController@searchDatatableList')->name('search-datatable-list');
        Route::get('/advertisements-list-edit/{id}', 'has\AdvertisementController@loadHouseListForAdvertisementToedit')->name('edit');
//        Route::get('/advertisements-list/{id}', 'has\AdvertisementController@loadHouseListForAdvertisement')->name('loadHouseListForAdvertisement');
//        Route::post('/advertisements', 'has\AdvertisementController@store')->name('store');
        Route::put('/search_advertisements/{id}', 'has\AdvertisementController@update')->name('update');
    });

    Route::group(['prefix' => 'ajax', 'name' => 'ajax.', 'as' => 'ajax.'], function() {
        Route::get('/divisions', 'has\AjaxController@divisions')->name('divisions');
        Route::get('/districts/{divisionId}', 'has\AjaxController@districts')->name('districts');
        Route::get('/thanas/{thanaId}', 'has\AjaxController@thanas')->name('thanas');
        Route::get('/employees', 'has\AjaxController@employees')->name('employees');
        Route::get('/employeesWithDept/{empDept?}', 'has\AjaxController@employeesWithDept')->name('employeesWithDept');
        Route::get('/employees-with-allotted-houses', 'has\AjaxController@employeesWithAllottedHouses')->name('employees-with-allotted-houses');
        Route::get('/second-employees-interchange-request-houses', 'has\AjaxController@secondEmpForInterchangeReq')->name('second-employees-interchange-request-houses');
        Route::get('/employees-interchange-request-houses', 'has\AjaxController@firstEmpForInterchangeReq')->name('employees-interchange-request-houses');
        Route::get('/alloted-employee', 'has\AjaxController@allotedEmployee')->name('alloted-employee');
        Route::get('/request-interchange-first-emp/{empId}', 'has\AjaxController@firstRequestEmp')->name('request-interchange-first-emp');
        Route::get('/request-interchange-second-emp/{empId}', 'has\AjaxController@secondRequestEmp')->name('request-interchange-second-emp');
        Route::get('/employee/{employeeCode}', 'has\AjaxController@employee')->name('employee');
        Route::get('/employee-with-allotted-house/{employeeCode}', 'has\AjaxController@employeeWithAllottedHouse')->name('employee-with-allotted-house');
        Route::get('/employee-husband/{employeeCode}', 'has\AjaxController@employeeHusband')->name('employee-husband');
        Route::get('/house-type/{advertisementId}', 'has\AjaxController@advertiseHouseType')->name('advertise-house-type');
        Route::get('/house/{houseId}', 'has\AjaxController@house')->name('house');
        Route::get('/allocatedHouseEmployeeList', 'has\AjaxController@allocatedHouseEmployeeList')->name('allocatedHouseEmployeeList');
        Route::get('/employee-house-allocated-info/{employeeCode}', 'has\AjaxController@employeeBasicInfoWithAllocatedHouse')->name('employeeBasicInfoWithAllocatedHouse');
        Route::get('/employee-house-allocated-replaced-info/{employeeCode}', 'has\AjaxController@employeeBasicInfoWithReplacedAllocatedHouse')->name('employeeBasicInfoWithReplacedAllocatedHouse');
        // takeOver
        Route::get('/allotted-letter-wise-employee-details/{allotmentNoOrEmpCode}', 'has\AjaxController@allottedLetterWiseAndEmployeeDetails')->name('allottedLetterAndEmployeeDetails');
        Route::get('/emp-code-wise-employee-details/{allotmentNoOrEmpCode}', 'has\AjaxController@empCodeWiseAndEmployeeDetails')->name('empCodeWiseAndEmployeeDetails');
        Route::get('/interchange-information/{allotmentNo}', 'has\AjaxController@interchangeInformation')->name('interchange-information');
        Route::get('/allotted-letter-wise-replaced-employee-details/{allotmentNoOrEmpCode}', 'has\AjaxController@allottedLetterWiseReplacedEmployeeDetails')->name('allottedLetterWiseReplacedEmployeeDetails');
        Route::get('/emp-code-wise-replace-employee-details/{allotmentNoOrEmpCode}', 'has\AjaxController@empCodeWiseReplacedEmployeeDetails')->name('empCodeWiseReplacedEmployeeDetails');

        //Route::get('/buildings-by-colony/{colonyId}/{dormitoryYN?}', 'has\AjaxController@buildingsByColony')->name('buildings-by-colony');
        Route::get('/buildings-by-colony-and-house-types', 'has\AjaxController@buildingsByColonyHouseType')->name('buildings-by-colony-and-house-types');
        Route::get('/buildings-by-colony/{colonyId}', 'has\AjaxController@buildingsByColony')->name('buildings-by-colony');
        Route::get('/buildings-by-colony/{colonyId}/{dormitoryYN}/{houseTypeId}', 'has\AjaxController@buildingsByColonyDormitory')->name('buildings-by-colony');
        Route::get('/house-types-by-building/{buildingId}', 'has\AjaxController@houseTypesByBuilding')->name('house-types-by-building');
        Route::get('/house-types-wise-by-building/{typeId}', 'has\AjaxController@typeWiseHouse')->name('house-types-wise-by-building');
        Route::get('/house-types-by-colony/{colonyId}', 'has\AjaxController@houseTypesByColony')->name('house-types-by-colony');

        //New created according to cr no -----
        Route::get('/house-types-by-building-dormitory/{buildingId}/{dormitoryYN?}', 'has\AjaxController@houseTypesByBuildingDormitory')->name('house-types-by-building-dormitory');

        Route::get('/houselist-by-building/{buildingId}/{dormitoryYN?}/{houseTypeId}', 'has\AjaxController@houseListByBuilding')->name('houselist-by-building');

        Route::get('/houselist-by-building-report/{buildingId}/{dormitoryYN?}', 'has\AjaxController@houseListByBuildingForReport')->name('houselist-by-building-report');
        Route::get('/all-houselist-by-building-report/{buildingId}/{dormitoryYN?}', 'has\AjaxController@AllHouseListByBuildingForReport')->name('all-houselist-by-building-report');
        Route::get('/housedetails-by-house/{houseId}', 'has\AjaxController@housedetailsByHouse')->name('housedetails-by-house');
        Route::get('/colony-wise-assign-type/{colonyId}', 'has\AjaxController@colonywiseassigntype')->name('colony-wise-assign-type');
        Route::get('/house-type-by-advertisement/{advId}', 'has\AjaxController@housetypebyadvertisement')->name('house-type-by-advertisement');

        Route::get('/houses', 'has\AjaxController@houses')->name('houses');

        Route::get('/emp', 'has\AjaxController@empRepApproved')->name('emp');

        Route::get('/allotted-houses', 'has\AjaxController@allottedHouses')->name('allotted-houses');
        Route::get('/advertisements', 'has\AjaxController@advertisements')->name('advertisements');
        Route::get('/advertisements-by-dept/{dept}', 'has\AjaxController@advertisementsByDept')->name('advertisements-by-dept');
        Route::get('/dpt-ack-no-by-dept/{dept}', 'has\AjaxController@dptAckNoByDept')->name('dpt-ack-no-by-dept');
        Route::get('/housetype-by-adv/{adv_id}', 'has\AjaxController@advertiseHouseType')->name('advertiseHouseType');
        Route::get('/employees-by-dept/{dept}', 'has\AjaxController@employeesByDept')->name('employees-by-dept');
        Route::get('/new-general-allot-letters', 'has\AjaxController@newGeneralAllotLetters')->name('new-general-allot-letters');
        Route::get('/take-over-letter-civil', 'has\AjaxController@takeoverlettercivil')->name('take-over-letter-civil');
        Route::get('/take-over-letter-elec', 'has\AjaxController@takeoverletterelec')->name('take-over-letter-elec');
        Route::get('/new-interchange-allot-letters', 'has\AjaxController@newInterchangeAllotLetters')->name('new-interchange-allot-letters');
        Route::get('/new-replace-allot-letters', 'has\AjaxController@newReplaceAllotLetters')->name('new-replace-allot-letters');
        Route::get('/new-replace-allot-elec-letters', 'has\AjaxController@newReplaceAllotElecLetters')->name('new-replace-allot-elec-letters');

        Route::get('/electrical-engineers', 'has\AjaxController@electricalEngineers')->name('electrical-engineers');
        Route::get('/civil-engineers', 'has\AjaxController@civilEngineers')->name('civil-engineers');

        Route::get('/load_employee_family_details/{employee_code}', 'has\AjaxController@loadEmployeeFamilyDetails')->name('load_employee_family_details');
        Route::get('/emp-code-wise-employee-details-for-allottee/{employee_code}', 'has\AjaxController@employeeDetailsForAllottee')->name('employeeDetailsForAllottee');

        //House Department Change
        Route::get('/housedetails-by-dpt/{dptId}', 'has\AjaxController@houseDetailsByDpt')->name('houseDetailsByDpt');
        Route::get('/ackdetails-by-dpt/{dptId}', 'has\AjaxController@ackDetailsByDpt')->name('ackDetailsByDpt');

        Route::get('/advertisements-departments/{house_type}', 'has\AjaxController@adDept')->name('adDept');
        Route::get('/load_employee_code_details/{emp_code}', 'has\AjaxController@empDetailsByCode')->name('empDetailsByCode');
    });

    Route::group(['name' => 'point-assessment.', 'as' => 'point-assessment.'], function() {
        Route::get('/point-assessments', 'has\PointAssessmentController@index')->name('index'); //->middleware('app.authorize:CAN_DECIDE_HOUSE_ALLOTMENT_APPLICATION');
        Route::get('/point-assessments-applicant-datatable-list', 'has\PointAssessmentController@datatableList')->name('datatable-list');
        Route::post('/point-assessments', 'has\PointAssessmentController@store')->name('store');
        Route::get('/get-advertisement-dropdownlist/', 'has\PointAssessmentController@pointAssessmentsDropdown')->name('pointAssessmentsDropdown');
    });

    Route::group(['name' => 'ha-application-approval.', 'as' => 'ha-application-approval.'], function () {
        // Basic routes
        Route::get('/ha-applications-approval/{id}', 'has\HaApplicationApprovalController@edit')->name('edit');
        Route::post('/ha-applications-approval/{applicationId}', 'has\HaApplicationApprovalController@store')->name('store');
        Route::post('/ha-applications-approval-un-assign/{applicationId}', 'has\HaApplicationApprovalController@unAssign')->name('un-assign');
        Route::post('/ha-applications-approval-deny/{applicationId}', 'has\HaApplicationApprovalController@deny')->name('deny');
        Route::get('/ha-applications-approval/download/{id}', 'has\HaApplicationApprovalController@eligibleAttachmentDownload')->name('eligible-attachment-download');
    });

    Route::group(['name' => 'allotmentLetter.', 'as' => 'allotmentLetter.'], function () {
          Route::get('/allotment-letter', 'has\AllotmentLetterController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_GENERAL_ALLOTMENT_LETTER');
          Route::get('/allotment-letter-list', 'has\AllotmentLetterController@datatableList')->name('datatableList');
          Route::get('/ha-applications-datatable-list-to-allotment/{id}', 'has\AllotmentLetterController@AdvLoadDatatableList')->name('datatable-list');
          Route::post('/allotment-letter', 'has\AllotmentLetterController@store')->name('store');
          Route::get('/allotment-letter/{id}', 'has\AllotmentLetterController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_GENERAL_ALLOTMENT_LETTER');
          Route::put('/allotment-letter/{id}', 'has\AllotmentLetterController@update')->name('update');
          Route::get('/show-allotment-letter', 'has\AllotmentLetterController@showletter')->name('showletter');
          Route::get('/user-wise-allot-letter', 'has\AllotmentLetterController@userWiseallotLetter')->name('userWiseallotLetter');


     });

    Route::group(['name' => 'house-allotment.', 'as' => 'house-allotment.'], function() {
//        Route::get('/house-allotments', 'has\HouseAllotmentController@index')->name('index'); //->middleware('app.authorize:CAN_CANCEL_HOUSE_ALLOTMENT');

        Route::get('/house-allotment', 'has\HouseAllotmentController@depAllotment')->name('depAllotment');

        Route::post('/house-allotment', 'has\HouseAllotmentController@store')->name('store');
        Route::get('/house-allotment/edit/{id}', 'has\HouseAllotmentController@edit')->name('depAllotmentEdit');
        Route::put('/house-allotment/update/{id}', 'has\HouseAllotmentController@update')->name('update');

        Route::get('/house-allotments-datatable-list', 'has\HouseAllotmentController@datatableList')->name('datatable-list');
    });

    Route::group(['name' => 'takeOver.', 'as' => 'takeOver.'], function () {
        Route::get('/take-over', 'has\TakeOverController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_TAKEOVER');
        Route::get('/take-over-elec', 'has\TakeOverController@takeoverElec')->name('takeoverElec'); //->middleware('app.authorize:CAN_ADD_TAKEOVER');
        Route::get('/take-over-civil', 'has\TakeOverController@takeoverCivil')->name('takeoverCivil'); //->middleware('app.authorize:CAN_ADD_TAKEOVER');
        Route::get('/take-over-list', 'has\TakeOverController@datatableList')->name('datatableList');
        Route::get('/take-over-list-elec', 'has\TakeOverController@datatableListElec')->name('datatableList-elec');
        Route::get('/take-over-list-civil', 'has\TakeOverController@datatableListCivil')->name('datatableList-civil');
        Route::post('/take-over', 'has\TakeOverController@store')->name('store');
        Route::post('/take-over-elec', 'has\TakeOverController@elecStore')->name('elecStore');

        Route::get('/take-over-application', 'has\TakeOverRequestController@index')->name('index'); //->middleware('app.authorize:CAN_APPLY_FOR_HOUSE_ALLOTMENT');
        Route::post('/take-over-request', 'has\TakeOverRequestController@takeOverRequest')->name('takeOverRequest'); //->middleware('app.authorize:CAN_ADD_TAKEOVER');

    });

    Route::group(['name' => 'civilHandOver.', 'as' => 'civilHandOver.'], function () {
        Route::get('/civil-allottee_informationhand-over', 'has\HandOverController@civilIndex')->name('index'); //->middleware('app.authorize:CAN_ADD_HANDOVER');
        //Route::get('/civil-hand-over', 'has\HandOverController@civilIndex')->name('civilIndex'); //->middleware('app.authorize:CAN_ADD_HANDOVER');
        Route::get('/civil-hand-over-list', 'has\HandOverController@civilDatatableList')->name('civilDatatableList');
        Route::post('/civil-hand-over', 'has\HandOverController@civilStore')->name('civilStore');
    });

    Route::group(['name' => 'electricHandOver.', 'as' => 'electricHandOver.'], function () {
        Route::get('/electric-hand-over', 'has\HandOverController@electricIndex')->name('index'); //->middleware('app.authorize:CAN_ADD_HANDOVER');
        Route::get('/electric-hand-over-list', 'has\HandOverController@electricDatatableList')->name('electricDatatableList');
        Route::post('/electric-hand-over', 'has\HandOverController@electricStore')->name('electricStore');
    });

    Route::group(['name' => 'house-interchange-application.', 'as' => 'house-interchange-application.'], function() {
        Route::get('/house-interchange-applications', 'has\HouseInterchangeApplicationController@index')->name('index'); //->middleware('app.authorize:CAN_APPLY_FOR_HOUSE_INTERCHANGE');
        Route::get('/house-interchange-applications-datatable-list', 'has\HouseInterchangeApplicationController@datatableList')->name('datatable-list');
        Route::post('/house-interchange-applications', 'has\HouseInterchangeApplicationController@store')->name('store');
        Route::get('/house-interchange-applications/{id}', 'has\HouseInterchangeApplicationController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_HOUSE_INTERCHANGE_APPLICATION');
        Route::put('/house-interchange-applications/{id}', 'has\HouseInterchangeApplicationController@update')->name('update');
    });

    Route::group(['name' => 'house-interchange-approval.', 'as' => 'house-interchange-approval.'], function() {
        Route::get('/house-interchange-approvals', 'has\HouseInterchangeApprovalController@index')->name('index'); //->middleware('app.authorize:CAN_APPROVE_HOUSE_INTERCHANGE_APPLICATION');
        Route::get('/house-interchange-approvals-datatable-list', 'has\HouseInterchangeApprovalController@datatableList')->name('datatable-list');
        Route::get('/house-interchange-approvals/{id}', 'has\HouseInterchangeApprovalController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_APPROVED_HOUSE_INTERCHANGE_APPLICATION');
        Route::put('/house-interchange-approvals/{id}', 'has\HouseInterchangeApprovalController@update')->name('update');
    });

    Route::group(['name' => 'house-replacement-application.', 'as' => 'house-replacement-application.'], function() {
        Route::get('/house-replacement-applications', 'has\HouseReplacementApplicationController@index')->name('index');//->middleware('app.authorize:CAN_APPLY_FOR_HOUSE_REPLACEMENT');
        Route::get('/house-replacement-applications-datatable-list', 'has\HouseReplacementApplicationController@datatableList')->name('datatable-list');
        Route::post('/house-replacement-applications', 'has\HouseReplacementApplicationController@store')->name('store');
        Route::get('/house-replacement-applications/{id}', 'has\HouseReplacementApplicationController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_HOUSE_REPLACEMENT_APPLICATION');
        Route::put('/house-replacement-applications/{id}', 'has\HouseReplacementApplicationController@update')->name('update');
    });

    Route::group(['name' => 'house-replacement-approval.', 'as' => 'house-replacement-approval.'], function() {
        Route::get('/house-replacement-approvals', 'has\HouseReplacementApprovalController@index')->name('index'); //->middleware('app.authorize:CAN_APPROVE_HOUSE_REPLACEMENT_APPLICATION');
        Route::get('/house-replacement-approvals-datatable-list', 'has\HouseReplacementApprovalController@datatableList')->name('datatable-list');
        Route::get('/house-replacement-approvals/{id}', 'has\HouseReplacementApprovalController@edit')->name('edit');
        Route::put('/house-replacement-approvals/{id}', 'has\HouseReplacementApprovalController@update')->name('update'); //->middleware('app.authorize:CAN_EDIT_APPROVED_HOUSE_REPLACEMENT_APPLICATION');
        Route::put('/house-replacement-approvals-un-assign/{id}', 'has\HouseReplacementApprovalController@unAssign')->name('un-assign');
    });

    Route::group(['name' => 'report-generator.', 'as' => 'report-generator.'], function() {
        Route::get('/report-generators', 'has\ReportGeneratorController@index')->name('index');
        Route::get('/report-generator-params/{id}', 'has\ReportGeneratorController@reportParams')->name('report-params');
    });

    Route::group(['name' => 'allotmentLetterInterchange.', 'as' => 'allotmentLetterInterchange.'], function () {
        Route::get('/interchange-allotment-letter', 'has\AllotmentLetterInterchangeController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_INTERCHANGE_ALLOTMENT_LETTER');
        Route::get('/interchange-approvals-datatable-list', 'has\AllotmentLetterInterchangeController@datatableListToShowInterchange')->name('datatable-list');
        Route::get('/interchange-allotment-letter-list', 'has\AllotmentLetterInterchangeController@datatableList')->name('datatableList');
        Route::get('/interchange-ha-applications-datatable-list-to-allotment/{id}', 'has\AllotmentLetterInterchangeController@AdvLoadDatatableList')->name('datatable-list');
        Route::post('/interchange-allotment-letter', 'has\AllotmentLetterInterchangeController@store')->name('store');
        Route::get('/interchange-allotment-letter/{id}', 'has\AllotmentLetterInterchangeController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_INTERCHANGE_ALLOTMENT_LETTER');
        Route::get('/interchange-allotment-letter-info/{id}', 'has\AllotmentLetterInterchangeController@showInterchangeApplicationInfo')->name('showInterchangeApplicationInfo');
        Route::put('/interchange-allotment-letter/{id}', 'has\AllotmentLetterInterchangeController@update')->name('update');
    });

    Route::group(['name' => 'house-allotment-cancellation.', 'as' => 'house-allotment-cancellation.'], function() {
        Route::get('/house-allotment-list', 'has\HouseAllotmentCancellationController@index')->name('index');
        Route::get('/house-allotment-cancellations/{allotmentId}', 'has\HouseAllotmentCancellationController@edit')->name('edit');
        Route::put('/house-allotment-cancellations/{allotmentId}', 'has\HouseAllotmentCancellationController@update')->name('update');
        Route::put('/house-allotment-cancellation-request/{allotmentId}', 'has\HouseAllotmentCancellationController@cancelRequest')->name('cancelRequest');
        Route::get('/house-allotment-cancellation-datatable', 'has\HouseAllotmentCancellationController@datatableList')->name('datatable-list');
        Route::get('/house-allotment-cancellation-list', 'has\HouseAllotmentCancellationController@cancelList')->name('cancel-list');
    });

    Route::group(['name' => 'interchange-takeover.', 'as' => 'interchange-takeover.'], function () {
        Route::get('/interchange-takeovers', 'has\InterchangeTakeoverController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_INTERCHANGE_TAKEOVER');
        Route::get('/interchange-takeovers-list', 'has\InterchangeTakeoverController@datatableList')->name('datatableList');
        Route::post('/interchange-takeovers', 'has\InterchangeTakeoverController@store')->name('store');
        Route::get('/interchange-takeovers-civil', 'has\InterchangeTakeoverController@civilIndex')->name('interchange-takeovers-civil');
        Route::post('/interchange-takeovers-civil', 'has\InterchangeTakeoverController@civilStore')->name('civil-Store');
        Route::get('/interchange-takeovers-elec', 'has\InterchangeTakeoverController@elecIndex')->name('interchange-takeovers-elec');
        Route::post('/interchange-takeovers-elec', 'has\InterchangeTakeoverController@elecStore')->name('elec-Store');

    });

    Route::group(['name' => 'replaceAllotmentLetter.', 'as' => 'replaceAllotmentLetter.'], function () {
        Route::get('/replace-allotment-letter', 'has\ReplaceAllotmentLetterController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_REPLACE_ALLOTMENT_LETTER');
        Route::get('/replace-allotment-letter-list', 'has\ReplaceAllotmentLetterController@datatableList')->name('datatableList');
        Route::get('/ha-replace-approved-datatable-list', 'has\ReplaceAllotmentLetterController@AdvLoadDatatableList')->name('datatable-list');
        Route::post('/replace-allotment-letter', 'has\ReplaceAllotmentLetterController@store')->name('store');
        Route::get('/replace-allotment-letter/{id}', 'has\ReplaceAllotmentLetterController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_REPLACE_ALLOTMENT_LETTER');
        Route::put('/replace-allotment-letter/{id}', 'has\ReplaceAllotmentLetterController@update')->name('update');
    });

    Route::group(['name' => 'replaceTakeOver.', 'as' => 'replaceTakeOver.'], function () {
        Route::get('/replace-take-over', 'has\ReplaceTakeOverController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_REPLACE_TAKEOVER');
        Route::get('/replace-take-over-list', 'has\ReplaceTakeOverController@datatableList')->name('datatableList');
        Route::get('/replace-take-over-elec-list', 'has\ReplaceTakeOverController@elecdatatableList')->name('elecdatatableList');
        Route::post('/replace-take-over', 'has\ReplaceTakeOverController@store')->name('store');
        Route::get('/replace-take-over-civil', 'has\ReplaceTakeOverController@civilIndex')->name('replace-take-over-civil');
        Route::post('/replace-take-over-civil', 'has\ReplaceTakeOverController@civilStore')->name('civilStore');
        Route::get('/replace-take-over-elec', 'has\ReplaceTakeOverController@elecIndex')->name('replace-take-over-elec');
        Route::post('/replace-take-over-elec', 'has\ReplaceTakeOverController@elecStore')->name('elecStore');
    });

    Route::group(['name' => 'bulk-house.', 'as' => 'bulk-house.'], function () {
        // Basic routes
        Route::get('/bulk-houses', 'has\BulkHouseController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_BULK_HOUSES');
        Route::post('/bulk-houses', 'has\BulkHouseController@store')->name('store');
    });


    Route::group(['name' => 'allottee_informations.', 'as' => 'allottee_informations.'], function () {
        // Basic routes
        Route::get('/allottee_information', 'has\AllotteeInformationController@index')->name('index'); //->middleware('app.authorize:CAN_APPLY_FOR_HOUSE_ALLOTMENT');
        Route::get('/allottee-information-datatable-list', 'has\AllotteeInformationController@datatableList')->name('datatable-list');
       // Route::get('/ha-applications/{id}', 'has\HaApplicationController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_HOUSE_ALLOTMENT_APPLICATION');
        Route::post('/allottee_information', 'has\AllotteeInformationController@store')->name('store');
        Route::get('/allottee-remove', 'has\AllotteeInformationController@removeAllottee')->name('allottee-remove');
        Route::get('/allottee-show', 'has\AllotteeInformationController@showAllottee')->name('allottee-show');
       // Route::put('/ha-applications/{id}', 'has\HaApplicationController@update')->name('update');
    });

    // For News
    Route::get('/get-top-news', 'NewsController@getNews')->name('get-top-news');
    Route::get('/news-download/{id}', 'NewsController@downloadAttachment')->name('news-download');

    //Allocate Flat

    Route::group(['name' => 'allocate-flat.', 'as' => 'allocate-flat.'], function () {
        Route::get('/allocate-flat', 'has\AllocateFlatController@index')->name('index'); //->middleware('app.authorize:CAN_ADD_ADVERTISEMENT');
        Route::get('/allocate-flat-datatable-list', 'has\AllocateFlatController@datatableList')->name('datatable-list');
        Route::post('/allocate-flat', 'has\AllocateFlatController@store')->name('store');
        Route::get('/allocate-flat/{id}', 'has\AllocateFlatController@edit')->name('edit');
        Route::put('/allocate-flat/{id}', 'has\AllocateFlatController@update')->name('update');
        Route::get('/get-dep-data/{dept_ack_id}', 'has\AllocateFlatController@depData')->name('depData');
        Route::get('/get-building-data/{house_type_id}/{colonyID}', 'has\AllocateFlatController@ajaxBulidingData')->name('ajaxBulidingData');
        Route::get('/get-house-data/{building_id}/{house_type_id}/{colonyId}', 'has\AllocateFlatController@ajaxHouseData')->name('ajaxHouseData');
        Route::get('/house-data-remove', 'has\AllocateFlatController@ajaxHouseDataDlt')->name('ajaxHouseDataDlt ');
        Route::get('/get-residential-house-type-data/{colonyId}', 'has\AllocateFlatController@ajaxHouseTypeData')->name('ajaxHouseTypeData');
        Route::get('/get-house-road-data/{buildingId}', 'has\AllocateFlatController@buildingRoad')->name('buildingRoad');
        Route::get('/add-to-temp/', 'has\AllocateFlatController@addToTemp')->name('addToTemp');
        Route::get('/delete-from-temp/', 'has\AllocateFlatController@deleteFromTemp')->name('deleteFromTemp');
    });


    Route::group(['name' => 'search_allottement.', 'as' => 'search_allottement.'], function () {
        Route::get('/search_allottement', 'has\AllocateFlatController@searchAdvertisements')->name('searchAdvertisements');
        Route::get('/allocate-list', 'has\AllocateFlatController@searchDatatableList')->name('search-datatable-list');
        Route::get('/allocate-list-edit/{id}', 'has\AllocateFlatController@loadHouseListForAdvertisementToedit')->name('edit');
//        Route::get('/advertisements-list/{id}', 'has\AdvertisementController@loadHouseListForAdvertisement')->name('loadHouseListForAdvertisement');
//        Route::post('/advertisements', 'has\AdvertisementController@store')->name('store');
        Route::put('/search_allottement/{id}', 'has\AdvertisementController@update')->name('update');
    });

    Route::get('/get-workflow-id', 'has\WorkflowController@get_workflow')->name('get-workflow-id');
    Route::get('/get-approval-list', 'has\WorkflowController@load_workflow')->name('get-approval-list');
    Route::post('/get-approval-post', 'has\WorkflowController@assign_workflow')->name('get-approval-post');
    Route::post('/get-multiple-assign-post', 'has\WorkflowController@multi_assign_workflow')->name('get-multiple-assign-post');

    Route::get('/approval', 'has\WorkflowController@status')->name('approval');
    Route::post('/approval-post', 'has\WorkflowController@store')->name('approval-post');

    // Multi point approval
    Route::post('/multi-approval-post', 'has\WorkflowController@multiApprovalStore')->name('multi-approval-post');


    Route::group(['name' => 'hand-over-application.', 'as' => 'hand-over-application.'], function () {
        // Basic routes
        Route::get('/hand-over-application', 'has\HandOverApplicationController@index')->name('index'); //->middleware('app.authorize:CAN_APPLY_FOR_HOUSE_ALLOTMENT');
        Route::post('/hand-over-request', 'has\HandOverApplicationController@handOverRequest')->name('handOverRequest');


        Route::get('/hand-over-application-datatable-list', 'has\HandOverApplicationController@datatableList')->name('datatable-list');
        Route::get('/hand-over-application/{id}', 'has\HandOverApplicationController@edit')->name('edit'); //->middleware('app.authorize:CAN_EDIT_HOUSE_ALLOTMENT_APPLICATION');
        Route::post('/hand-over-application', 'has\HandOverApplicationController@store')->name('store');
        Route::put('/hand-over-application/{id}', 'has\HandOverApplicationController@update')->name('update');
        Route::get('/get-advertise-flat-data/{adv_id}/{house_type_id}', 'has\HandOverApplicationController@ajaxAdvertisementFlatData')->name('ajaxHouseData');

        Route::get('/cpa-hand-over-application', 'has\HandOverApplicationController@cpaIndex')->name('cpa-hand-over-application');
        Route::get('/cpa-hand-over-application-list', 'has\HandOverApplicationController@cpadatalist')->name('cpa-hand-over-application-list');
        Route::post('/cpa-hand-over-request', 'has\HandOverApplicationController@cpaHandOverRequest')->name('cpa-hand-over-request');
        Route::get('/cpa-hand-over-request-document/{id}', 'has\HandOverApplicationController@download')->name('cpa-hand-over-request-document');
    });

    Route::group(['name' => 'house-dept-change.', 'as' => 'house-dept-change.'], function () {
        Route::get('/house-change', 'has\HouseDeptChangeController@index')->name('index');
        Route::get('/house-change-list', 'has\HouseDeptChangeController@datatable')->name('datatable');
        Route::post('/house-change', 'has\HouseDeptChangeController@store')->name('store');
        Route::get('/house-by-emp/{empID}', 'has\HouseDeptChangeController@gethousebyemp')->name('gethousebyemp');

//        Route::post('/hand-over-request', 'has\HandOverApplicationController@handOverRequest')->name('handOverRequest');
    });

    Route::group(['name' => 'user-wise-area.', 'as' => 'user-wise-area.'], function () {
        Route::get('/user-wise-area', 'has\colony\userColonyController@index')->name('index');
        Route::post('/user-wise-area', 'has\colony\userColonyController@store')->name('store');
        Route::get('/user-wise-area-edit/{id}', 'has\colony\userColonyController@edit')->name('edit');
        Route::put('/user-wise-area-Update/{id}', 'has\colony\userColonyController@update')->name('update');
        Route::get('/load-role-to-user/{id}', 'has\colony\userColonyController@getRoletoUser')->name('load-role-to-user');
        Route::get('/load-role-to-approve-user/{id}', 'has\colony\userColonyController@getRoletoApproveUser')->name('load-role-to-approve-user');
        Route::get('/load-role-to-user-search/{role}/{employee}', 'has\colony\userColonyController@getRoletoUserSearch')->name('load-role-to-user-search');
//        Route::post('/house-change', 'has\HouseDeptChangeController@store')->name('store');
////        Route::post('/hand-over-request', 'has\HandOverApplicationController@handOverRequest')->name('handOverRequest');
    });
});

