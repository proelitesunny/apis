<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorDumpersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_authentication')->create('doctor_dumpers', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('GID',50);
            $table->longText('TITLE',100);
            $table->longText('FIRST_NAME',500);
            $table->longText('MIDDLE_NAMES',500);
            $table->longText('LAST_NAME',500);
            $table->longText('DEPARTMENT_NAME',500);
            $table->longText('SPECIALITY',500);
            $table->longText('GRADE_NAME',500);
            $table->longText('EMPLOYEE_DESGINATION',500)->nullable();
            $table->longText('EMP_PAY_LOC',500);
            $table->longText('HOSPITAL_NAME',500);
            $table->longText('EMAIL_ADDRESS',500);
            $table->longText('HOSPITAL_CODE',100);
            $table->longText('EMPLOYEE_STATUS',100);
            $table->longText('EMPLOYEE_TYPE',500);
            $table->longText('CATEGORY',100);
            $table->date('DATE_OF_JOINING');
            $table->date('DATE_OF_BIRTH');
            $table->double('AGE');
            $table->string('GENDER');
            $table->longText('QUALIFICATION_DIPLOMA',500);
            $table->string('DIPLOMA_PASSING_YEAR');
            $table->longText('QUALIFICATION_GRADUATION',500);
            $table->string('GRADUATION_PASSING_YEAR');
            $table->longText('QUALIFICATION_POST_GRADUATE',500);
            $table->string('POST_GRADUATE_YEAR');
            $table->longText('QUALIFICATION_DOCTERATE',500)->nullable();
            $table->string('DOCTERATE_YEAR')->nullable();
            $table->longText('QUALIFICATION_OTHERS',500)->nullable();
            $table->string('OTHERS_YEAR')->nullable();
            $table->longText('PREVIOUS_EMPLOYER',500)->nullable();
            $table->double('PREV_EXP');
            $table->double('CURRENT_FORTIS_EXP');
            $table->double('TOTAL_EXP');
            $table->longText('REG_COUN_NAME',500);
            $table->string('REG_COUN_NO');
            $table->date('REG_DATE');
            $table->longText('OTHER_NAME',500)->nullable();
            $table->longText('OTHER_REG_NO',500)->nullable();
            $table->date('OTHER_REG_DT');
            $table->string('EMP_INFO_UPDATION_DATE');
            $table->string('EMP_ASSIGN_UPDATION_DATE');
            $table->string('RECORD_STATUS_UPDATION_DATE');
            $table->integer('REC_NO')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor_dumpers');
    }
}
