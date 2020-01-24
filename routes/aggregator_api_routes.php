<?php

Route::group(['middleware' => 'api-auth:required'], function () {
    Route::post('patients/create', 'PatientsController@create');
    // Route::post('patients/update', 'PatientsController@update');

    Route::get('doctors/appointments', 'DoctorsController@appointments');
    Route::get('doctors', 'DoctorsController@doctorsSearch');
    Route::get('doctors/available-slots', 'DoctorsController@availableSlots');

    // Route::get('patients/appointments/{appointmentCode}', 'PatientsController@appointmentDetails');
    // Route::get('doctors', 'DoctorsController@index');
    // Route::post('doctors/update-appointment-status', 'DoctorsController@updateAppointmentStatus');
    // Route::get('doctors/{doctor_code}', 'DoctorsController@getDoctorDetails');
    // Route::get('doctor/all-doctors', 'DoctorsController@getDoctorsList');
    // Route::get('doctor/available-slots', 'DoctorsController@getAvailableSlots');
    // Route::get('doctor/booking-list', 'DoctorsController@getBookingList');
    // Route::post('patients/cancel-appointment', 'DoctorsController@updateAppointmentStatus');

    Route::post('doctors/book-appointment', 'BookingsController@bookAppointment');
    Route::get('doctors/appointment-status/{bookingId}', 'BookingsController@appointmentStatus');
    Route::post('doctors/cancel-appointment', 'BookingsController@cancelAppointment');
    Route::get('tests/appointments', 'TestBookingController@getAppointments');
    // Route::get('tests/appointments', function (){
    //     dd(1);
    // });
});
