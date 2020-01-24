<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientDumpersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_authentication')->create('patient_dumpers', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('Registrationno',100);
            $table->dateTime('RegistrationDate');
            $table->longText('Title',50);
            $table->longText('FirstName',500);
            $table->longText('MiddleName',500);
            $table->longText('LastName',500);
            $table->longText('Gender',50);
            $table->longText('MaritalStatus',100);
            $table->longText('MothersMaidenName',500);
            $table->date('DOB');
            $table->longText('HouseNo',500);
            $table->longText('Street',500);
            $table->longText('Area',500);
            $table->longText('PINCode',500);
            $table->longText('District',500);
            $table->longText('State',500);
            $table->longText('City',500);
            $table->longText('Country',500);
            $table->longText('Nationality',500);
            $table->longText('FathersName',500);
            $table->longText('Spouse',500);
            $table->longText('MobileNo',500);
            $table->longText('HomeContact',500);
            
            $table->longText('OfficeContact',500);
            $table->longText('EmailPri',500);
            $table->longText('EmailSec',500);
            $table->longText('EmerConName',500);
            $table->longText('EmerConRelationship',500)->nullable();
            $table->longText('EmerConAdd',500)->nullable();
            $table->longText('EmerConPhone',500);
            $table->longText('EmerConCellNo',500);
            $table->longText('EmerConEMail',500);
            $table->longText('Religion',500);
            
            $table->longText('Occupation',500);
            $table->longText('BloodGroup',500);
            $table->longText('PreferredModeOfCommunication',500)->nullable();
            $table->longText('CorporateCardNo',500);
            $table->longText('CorporateType',500);
            $table->longText('CorporateName',500);
            $table->longText('RefferedDocName',500);
            $table->longText('RefferedDocSpecialisation',500);
            $table->longText('RefferedDocAddress',500);
            $table->longText('RefferedDocMobile',500);
            $table->longText('RefferedDocEmail',500);
            $table->longText('WorkCompanyName',500);
            $table->longText('WorkCompanyAddress',500);
            $table->longText('WorkCompanyContactNo',500);
            $table->longText('WorkCompanyEmail',500);
            $table->longText('PassportNo',500);
            $table->date('IssueDate');
            $table->date('ExpiryDate');
            $table->longText('PassportIssuedAt',500)->nullable();
            $table->longText('OtherAllergies',500);
            $table->longText('VIP',200);
            
            $table->longText('Foreigner',500)->nullable();
            $table->longText('RegisteredBy',500);
            $table->longText('UpdatedBy',500)->nullable();
            $table->dateTime('UpdatedDate')->nullable();
            $table->integer('Age')->unsigned();
            $table->string('AgeType',10);
            $table->string('IsDOBApproximate',10)->nullable();
            $table->longText('DisplayName',500)->nullable();
            $table->longText('UnitName',500);
            
            $table->integer('RecordNo')->unsigned();
            $table->longText('RegionName',500);
            $table->longText('LOC',500);
            $table->dateTime('CR_DT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_dumpers');
    }
}
