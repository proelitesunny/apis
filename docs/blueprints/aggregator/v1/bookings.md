# Group Booking
API endpoints related to bookings

### Book Appointment [POST /doctors/book-appointment]
Booking of appointment with doctor for a registered patient on a given date & time.

#### Request Body Fields description

* patient_id : [string, required] - Patient id 
* doctor_id : [string, required] - Doctor id
* hospital_id : [string, required] - Hospital id
* booking_date : [string, required] - Format YYYY-MM-DD
* slot_id : [string, required] - Schedule slot id

:::note
## Note
* apitoken token is required in headers
* aggregatorType is required in headers
* In response, booking_status can be either (0: pending, 1: confirmed, 2: rescheduled, 3: canceled)
:::

+ Request with body

    + Headers

            Content-Type: application/json
            aggregatorType: 3rdParty
            apitoken: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0OTg4MDUzNDgsIm5iZiI6MTQ5ODgwNTM0OCwiZXhwIjoxNTAxMzk3MzQ4LCJ1aWQiOiJNUk4wMEEwMDAxIn

    + Body

            {
                "patient_id" : "235",
                "doctor_id" : "555",
                "hospital_id" : "3",
                "booking_date" : "2017-09-21",
                "slot_id" : "103"
            }

+ Response 200

    + Headers

            Content-Type: application/json

    + Body

            {
                "message": "Your booking has been confirmed.",
                "booking_id": 37,
                "booking_code": "B00301",
                "booking_status": 1,
                "fees": 1000
            }

+ Response 400

    + Headers

            Content-Type: application/json

    + Body

            {
                "errors": [
                    {
                        "message": "Slot not available",
                        "source": "slot_id"
                    }
                ]
            }

### Booked Appointment Status [GET /doctors/appointment-status/{bookingId}]
Once the appointment has been confirmed by doctor, use this api to get status confirmed booking

:::note
## Note
* apitoken token is required in headers
* aggregatorType is required in headers
* In response, booking_status can be either (0: pending, 1: confirmed, 2: rescheduled, 3: canceled)
:::

+ Parameters

    + bookingId: `155800` (required, number) - Booking id generated from appointment confirmation

+ Request with headers

    + Headers

            Content-Type: application/json
            aggregatorType: 3rdParty
            apitoken: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0OTg4MDUzNDgsIm5iZiI6MTQ5ODgwNTM0OCwiZXhwIjoxNTAxMzk3MzQ4LCJ1aWQiOiJNUk4wMEEwMDAxIn

+ Response 200

    + Headers

            Content-Type: application/json

    + Body

            {
                "patient_id": "103",
                "booking_code": "B02353",
                "booking_status": 1,
                "booking_code": "B02353",
                "fees": 1000,
                "uhid": "23432",
                "doctor_id": "2353",
                "doctor_name": "Ram Narayan",
                "doctor_speciality_name": "Cardiologists",
                "hospital_id": "235",
                "hospital_city_name": "Bengaluru",
                "hospital_name": "Fortis Hospital Bannerghatta Road",
                "hospital_address": "Fortis hospital Bannerghatta Road",
                "hospital_primary_contact" : "234234234",
                "appointment_date": "2017-09-25",
                "appointment_start_time": "11:00",
                "appointment_end_time": "11:15"
            }

+ Response 400 (application/json)

    + Body

            {
                "errors": [
                    {
                        "message": "Booking id is not valid",
                        "source": ""
                    }
                ]
            }

### Cancel Appointment [POST /doctors/cancel-appointment]
Cancellation of booked appointment based on booking code.

#### Request Body Fields description

* booking_id : [string, required]
* cancellation_reason : [number, required] From below mentioned reasons

:::note
## Note
* apitoken token is required in headers
* aggregatorType is required in headers
* Below are the concellation reasons accepted,

        0 - "I will not be able to come on time for appointment"
        1 - "I want to visit a different doctor at a different time"
        2 - "I have already booked an appointment with a doctor, in some other hospital"
        3 - "I need guidance from the staff, as to which speciality/doctor I should meet"
        9 - "Other Reason"
:::

+ Request with body

    + Headers

            Content-Type: application/json
            aggregatorType: 3rdParty
            apitoken: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0OTg4MDUzNDgsIm5iZiI6MTQ5ODgwNTM0OCwiZXhwIjoxNTAxMzk3MzQ4LCJ1aWQiOiJNUk4wMEEwMDAxIn

    + Body

            {
                "booking_id" : "22330",
                "cancellation_reason" : 1
            }

+ Response 200

    + Headers

            Content-Type: application/json

    + Body

            {
                "message": "Your booking is canceled."
            }

+ Response 400 (application/json)

    + Body

            {
                "errors": [
                    {
                        "message": "Booking ID is not valid.",
                        "source": "booking_id"
                    }
                ]
            }