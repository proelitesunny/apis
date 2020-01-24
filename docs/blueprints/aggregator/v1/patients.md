# Group Patients
API endpoints related to patient records.

### Create Patient [POST /patients/create]
Create a patient record in MyHealth Fortis Agent Portal.

#### Request Body Fields description

* first_name : [string, required]
* last_name : [string, optional]
* uhid : [string,  required if facility code present] (Only applicable for practo)
* facility_code : [string, required if uhid present] (Only applicable for practo)
* gender : [integer, required] (0 - male, 1 - female, 2 - other)
* dob  : [string,optional] (format : Y-m-d, before : tomorrow)
* mobile_isd_code  : [numeric,optional]
* mobile_no : [string, required] (10 digits)
* email : [string, optional]
* city_name  : [string,optional]
* state_name  : [string,optional]
* country_name  : [string,optional]
* pin_code  : [numeric,optional]
* aadhar_number  : [string,optional]
* passport_number  : [string,optional]
* pan_number  : [string,optional]
* emergency_contact_name : [string,optional]
* emergency_contact_no : [string,optional] (max:16)

:::note
## Note
* apitoken token is required in headers.
* aggregatorType is required in headers.
* city_name, country_name, state_name are accepted in standard format, abbreviated / misspelled values considered as null.
* uhid and facility_code lookup will be only applicable for practo.
:::

+ Request with body

    + Headers

            Content-Type: application/json
            aggregatorType: 3rdParty
            apitoken: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0OTg4MDUzNDgsIm5iZiI6MTQ5ODgwNTM0OCwiZXhwIjoxNTAxMzk3MzQ4LCJ1aWQiOiJNUk4wMEEwMDAxIn

    + Body

            {
                "first_name" : "Amit",
                "last_name" : "Kumar",
                "gender" : 0,
                "dob"  : "1986-10-01",
                "mobile_isd_code"  : "91",
                "mobile_no" : "9999123456",
                "email" : "amit.kumar@gmail.com",
                "city_name"  : "Bengaluru",
                "state_name"  : "Karnataka",
                "country_name"  : "India",
                "pin_code"  : 536008,
                "aadhar_number"  : "AB23532563",
                "passport_number"  : "DB3353AB",
                "pan_number"  : "AHBPA53G",
                "emergency_contact_name" : "Shyam Kumar",
                "emergency_contact_no" : "080113525353"
            }

+ Response 200

    + Headers

            Content-Type: application/json

    + Body

            {
                "message": "Patient created successfully",
                "patient_id": "235"
            }

+ Response 400

    + Headers

            Content-Type: application/json

    + Body

            {
                "errors": [
                    {
                        "message": "First name is required",
                        "source": "first_name"
                    },
                    {
                        "message": "Gender is required",
                        "source": "gender"
                    },
                    {
                        "message": "Mobile number is required",
                        "source": "mobile_no"
                    }
                ]
            }

### Get Appointments [GET /doctors/appointments{?start_date,end_date,patient_id,page}]
Get all confirmed & canceled scheduled appointments against a date range and or patient_id.

:::note
## Note
* apitoken token is required in headers
* aggregatorType is required in headers
* In response, booking_status can be either (0: pending, 1: confirmed, 2: rescheduled, 3: canceled)
:::

+ Parameters

    + start_date: `2017-09-12` (required, string) - Format YYYY-MM-DD

    + end_date: `2017-09-22` (required, string) - Format YYYY-MM-DD

    + patient_id: `2353` (optional, string) - Patient id

    + page: `1` (optional, integer) - Page number

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
                "appointments": [
                    {
                        "patient_id": 1,
                        "booking_code": "BOOKIDA00002",
                        "booking_status": 1,
                        "fees": "525.00",
                        "uhid": "qwerty",
                        "doctor_id": 1,
                        "doctor_name": "Talia",
                        "doctor_speciality": [
                            "Cardiologist 90096"
                        ],
                        "hospital_id": 1,
                        "hospital_city_name": "Lake Rooseveltmouth",
                        "hospital_name": "FortisHealthCare, 69093",
                        "hospital_address": "652 Kiel Cliffs\nNorth Camren, FL 74802-5189",
                        "hospital_primary_contact": "9936635608",
                        "appointment_date": "2017-10-01",
                        "appointment_start_time": "01:15:00",
                        "appointment_end_time": "01:30:00"
                    }
                ],
                "pagination": {
                    "total": 6,
                    "per_page": 1,
                    "current_page": 2,
                    "total_pages": 6,
                    "links": {
                        "prev": "http://aggregator-apis.for.myhealthcare.co/api/aggregator/v1/doctors/appointments?page=1&start_date=2017-09-28&end_date=2017-12-27",
                        "next": "http://aggregator-apis.for.myhealthcare.co/api/aggregator/v1/doctors/appointments?page=3&start_date=2017-09-28&end_date=2017-12-27"
                    }
                }
            }


+ Response 400 (application/json)

    + Body

            {
                "errors": [
                    {
                        "message": "No record found",
                        "source": ""
                    }
                ]
            }