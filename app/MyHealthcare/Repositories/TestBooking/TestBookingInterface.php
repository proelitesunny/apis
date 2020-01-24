<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\MyHealthcare\Repositories\TestBooking;

/**
 *
 * @author admin-pc
 */
interface TestBookingInterface {

    public function getTestAppointments($params);
    public function create($params);

}
