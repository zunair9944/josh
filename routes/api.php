<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\InsuranceProviderController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\AppointmentTypesController;
use App\Http\Controllers\PatientNotesController;
use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\ConnectedCalendarController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\OutlookCalendarController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\NotificationsController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/me',function(Request $request){
        $user = User::with('store')->with('store')->findOrFail($request->user()->id);
        return $user;
    });

    Route::get('/is_calendar_connected',[UserController::class,'is_calendar_connected']);
    Route::get('/get_connected_calendars',[ConnectedCalendarController::class,'get_connected_calendars']);


    Route::resource('users', UserController::class);

    Route::post('/logout', [UserController::class, 'logout']);

    // Route::middleware(['role:store_owner|staff'])->group(function(){

        /** Bookings */
        Route::prefix('bookings')->group(function () {
            Route::get('/get_store_bookings', [BookingsController::class, 'get_store_bookings']);
            Route::get('/get_booking_by_date/{date}',[BookingsController::class,'get_booking_by_date']);
            Route::get('/count', [BookingsController::class, 'total_store_bookings_count']);
            Route::get('/get_bookings_by_user', [BookingsController::class, 'get_bookings_by_user']);
        }); 


        /** Patients */
        Route::prefix('patients')->group(function(){
            Route::get('/get_upcoming_bookings/{id}',[PatientsController::class,'get_upcoming_bookings']);
            Route::get('/get_patient_notes/{id}',[PatientNotesController::class,'get_patient_notes']);
            Route::post('/patient_notes/{id}',[PatientNotesController::class,'store']);
        });
        
        /** Appointment Types */
        Route::prefix('appointment_types')->group(function(){
            Route::get('/count', [AppointmentTypesController::class,'total_store_appointment_types']);
            Route::get('/get_appointment_types_by_user/{id}', [AppointmentTypesController::class,'get_appointment_types_by_user']);
        });
        
        
        Route::post('/create_staff_user', [UserController::class,'create_staff_user']);
        Route::get('/get_store_users',[UserController::class,'get_store_users']);
        Route::post('/verify_shopify_keys',[StoreController::class,'verify_shopify_keys']);
        
        Route::resource('bookings', BookingsController::class)->except(['edit','create']);
        Route::resource('patients',PatientsController::class)->except(['edit','create']);
        Route::resource('patient_notes',PatientNotesController::class)->except(['edit','create']);
        Route::resource('questionnaires',QuestionnaireController::class)->except(['edit','create']);
        Route::resource('appointment_types',AppointmentTypesController::class)->except(['edit','create']);
        Route::resource('attachments',AttachmentsController::class);

        Route::get('/insurance_providers',[InsuranceProviderController::class,'index']);
    // });

    Route::prefix('availability')->group(function(){
        Route::get('/{user_id}', [AvailabilityController::class,'availability_by_user_id']);
        Route::post('/{user_id}', [AvailabilityController::class,'set_user_availability']);
        Route::get('/get_available_days/{user_id}',[AvailabilityController::class,'get_available_days']);
        Route::get('/get_available_times/{day_index}/{user_id}/{appointment_type_id}/{date}', [AvailabilityController::class, 'get_available_times']);
    });


    Route::prefix('calendars')->group(function(){
        /** Google Calendar */
        Route::get('/google/list_all_calendars',[GoogleCalendarController::class,'list_calendars']);
        Route::get('/google/get_all_events/{calendarId}',[GoogleCalendarController::class,'get_all_events']);
        Route::get('/google/get_booked_slots/{user_id}',[GoogleCalendarController::class,'get_booked_slots']);
        Route::post('/google/create_event',[GoogleCalendarController::class,'create_event']);
        Route::post('/google/remove_conflicts_check',[GoogleCalendarController::class,'remove_conflicts_check']);
        Route::post('/google/add_conflicts_check',[GoogleCalendarController::class,'add_conflicts_check']);
        
        /** Outlook Calendars */
        Route::get('/outlook/list_all_calendars',[OutlookCalendarController::class,'list_calendars']);
        Route::post('/outlook/remove_conflicts_check',[OutlookCalendarController::class,'remove_conflicts_check']);
        Route::post('/outlook/add_conflicts_check',[OutlookCalendarController::class,'add_conflicts_check']);
        Route::post('/outlook/set_primary_sub_calendar',[OutlookCalendarController::class,'set_primary_sub_calendar']);
    });


    Route::prefix('notifications')->group(function(){
        Route::get('/', [NotificationsController::class,'get_user_notifications']);
    });
    


});

Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);
// Route::post('/testing', [UserController::class, 'store']);
// Route::get('/count', [BookingsController::class, 'total_store_bookings_count']);
