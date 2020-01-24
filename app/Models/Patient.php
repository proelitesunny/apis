<?php

namespace App\Models;

use App\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\MyHealthcare\Helpers\Asset;
use Illuminate\Support\Facades\Crypt;

class Patient extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function patientHealthDetails() {
        return $this->hasOne(PatientHealthDetail::class);
    }

    public function patientHasManyHisMapping() {
        return $this->hasMany('App\Models\PatientHisMapping', 'patient_id');
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class,'patient_id');
    }

    /* public function parent()
      {
      return $this->belongsTo(self::class, 'parent_id');
      }

      public function children()
      {
      return $this->hasMany(self::class,'parent_id');
      }

      public function user()
      {
      return $this->belongsTo(User::class);
      }

      public function patientPreferences()
      {
      return $this->hasMany(PatientPreference::class);
      }

      public function country()
      {
      return $this->belongsTo(Country::class);
      }

      public function state()
      {
      return $this->belongsTo(State::class);
      }

      public function city()
      {
      return $this->belongsTo(City::class);
      }

      public function getGenderAttribute($value)
      {
      return config('constants.GENDER_ENUM')[$value];
      }

      public function getBloodGroupAttribute($value)
      {
      return config('constants.BLOOD_GROUP')[$value];
      }

      public function getIdTypeAttribute($value)
      {
      return config('constants.ID_TYPE')[$value];
      }

      public function getProfilePictureAttribute($value) {

      $profile_pic = config('constants.upload_path').$value;

      if(file_exists($profile_pic) && is_file($profile_pic)){

      return Asset::getImagePath($value);

      }else{
      return url('/').'/assets/images/user.png';
      }

      //return empty($value)?'':env('APP_URL') . '/storage/' . $value;
      }

      public function getIdNoAttribute()
      {
      try {
      //            return Crypt::decrypt($this->id_number);
      return base64_decode($this->id_number);
      } catch (DecryptException $e) {
      logger($e->getMessage());
      return null;
      }
      }

      public function age() {
      return \Carbon\Carbon::parse($this->dob)->diffInYears(\Carbon\Carbon::now());
      }

      public function dob() {
      return \Carbon\Carbon::parse($this->dob)->format('d/m/Y');
      }

      public function getFullNameAttribute()
      {
      return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
      }

      public function getIsActiveAttribute($value)
      {
      return config('constants.USER_STATUS_ENUM')[$value];
      }

      public function getIsVerifiedAttribute($value)
      {
      return config('constants.DOCTOR_VERIFY_ENUM')[$value];
      }

      public function userDevice()
      {
      return $this->morphOne('App\Models\UserDevice', 'notifiable');
      }

      public function bookings()
      {
      return $this->hasMany(Booking::class);
      }

      public function getStatusBookingAttribute()
      {
      try {
      return config('constants.APPOINTMENT_BOOKING')[$this->booking_status];
      } catch (\Exception $e) {
      return null;
      }
      }
      public function masterPatientIndex()
      {
      return $this->belongsTo(MasterPatientIndex::class,'master_indices_id');
      }

     */

    public function masterPatientIndex() {
        return $this->belongsTo(MasterPatientIndex::class, 'master_indices_id');
    }

}
