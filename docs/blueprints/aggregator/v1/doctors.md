# Group Doctors
API endpoints related to doctors

### Get Doctors [GET /doctors{?city_name,hospital_name,speciality_name,page}]
Get list of doctors based on passed parameters.

:::note
## Note
* apitoken token is required in headers
* aggregatorType is required in headers
* In response, gender can be either ( 0: Male, 1: Female, 2: Others )
:::

+ Parameters

    + city_name: `Bengaluru` (optional, string) - Either city name or hospital name is required
    + hospital_name: `Fortis Bannerghatta Road` (optional, string) - Either city name or hospital name is required
    + speciality_name: `Cardiologist` (optional, string)
    + page: `2` (integer, optional) - Number of page in a paginated result

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
                "doctors": [
                    {
                        "id": 1,
                        "title": "Dr.",
                        "first_name": "Ram",
                        "middle_name": "Kumar",
                        "last_name": "Narayan",
                        "designation": "Senior Consultant",
                        "qualifications": "MBBS",
                        "gender" : 0,
                        "specialities": ["Cardiologists", "Orthopedics"],
                        "fees": 2000,
                        "hospital_id": "235",
                        "hospital_city_name": "Bengaluru",
                        "hospital_name": "Fortis Hospital Bannerghatta Road",
                        "hospital_address": "Fortis hospital Bannerghatta Road",
                        "hospital_primary_contact" : "234234234",
                        "hospital_secondary_contact" : "563535345",
                        "hospital_emergency_contact" : "253555523",
                        "hospital_helpline_sos" : :"2352352523"
                    },
                    {
                        "id": 2,
                        "title": "Dr.",
                        "first_name": "Ramesh",
                        "middle_name": "Kumar",
                        "last_name": "Shrivastava",
                        "designation": "Senior Consultant",
                        "qualifications": "MBBS",
                        "gender" : 0,
                        "specialities": ["Cardiologists", "Orthopedics"],
                        "fees": 2000,
                        "hospital_id": "235",
                        "hospital_city_name": "Bengaluru",
                        "hospital_name": "Fortis Hospital Bannerghatta Road",
                        "hospital_address": "Fortis hospital Bannerghatta Road",
                        "hospital_primary_contact" : "234234234",
                        "hospital_secondary_contact" : "563535345",
                        "hospital_emergency_contact" : "253555523",
                        "hospital_helpline_sos" : :"2352352523"
                    },
                    {
                        "id": 3,
                        "title": "Dr.",
                        "first_name": "Suresh",
                        "middle_name": "Kumar",
                        "last_name": "Lilha",
                        "designation": "Senior Consultant",
                        "qualifications": "MBBS",
                        "gender" : 0,
                        "specialities": ["Cardiologists", "Orthopedics"],
                        "fees": 2000,
                        "hospital_id": "235",
                        "hospital_city_name": "Bengaluru",
                        "hospital_name": "Fortis Hospital Bannerghatta Road",
                        "hospital_address": "Fortis hospital Bannerghatta Road",
                        "hospital_primary_contact" : "234234234",
                        "hospital_secondary_contact" : "563535345",
                        "hospital_emergency_contact" : "253555523",
                        "hospital_helpline_sos" : :"2352352523"
                    },
 
                ],
                "pagination": {
                    "total": 30,
                    "per_page": 10,
                    "current_page": 2,
                    "total_pages": 3,
                    "links": {
                        "prev": "http://aggregator-apis.for.myhealthcare.co/api/aggregator/v1/doctors?page=1&hospital_name=fortis&speciality_name=Cardiologist&city_name=Lake",
                        "next": "http://aggregator-apis.for.myhealthcare.co/api/aggregator/v1/doctors?page=3&hospital_name=fortis&speciality_name=Cardiologist&city_name=Lake"
                    }
                }
            }

+ Response 400

    + Headers

            Content-Type: application/json

    + Body

            {
                "errors": [
                    {
                        "message": "No doctors found with given parameters",
                        "source": ""
                    }
                ]
            }

### Get Doctor Availability Time Slots [GET /doctors/available-slots{?doctor_id,hospital_id,appointment_date,include,page}]
Get list of doctor's available time slots based on provided parameters for a particular date.

:::note
## Note
* apitoken token is required in headers
* aggregatorType is required in headers
* In response, slot time is in 24 hour format
* In response, gender can be either ( 0: Male, 1: Female, 2: Others )
:::

+ Parameters

    + doctor_id: `3` (string, required)
    + hospital_id: `237` (string, required)
    + appointment_date: `2017-09-21` (string, required) - Date in format YYYY-MM-DD
    + include: `doctor` (string, optional) - If doctor object required in response
    + page: `2` (integer, optional) - Number of page in a paginated result

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
                "doctor": {
                    "id": 3,
                    "title": "Dr.",
                    "first_name": "Suresh",
                    "middle_name": "Kumar",
                    "last_name": "Lilha",
                    "designation": "Senior Consultant",
                    "qualifications": "MBBS",
                    "gender" : 0,
                    "specialities": ["Cardiologists", "Orthopedics"],
                    "fees": 2000,
                    "hospital_id": "235",
                    "hospital_city_name": "Bengaluru",
                    "hospital_name": "Fortis Hospital Bannerghatta Road",
                    "hospital_address": "Fortis hospital Bannerghatta Road",
                    "hospital_primary_contact" : "234234234",
                    "hospital_secondary_contact" : "563535345",
                    "hospital_emergency_contact" : "253555523",
                    "hospital_helpline_sos" : :"2352352523"
                },
                "slots" : [
                    {
                        "id" : 1,
                        "start_time" : "05:00",
                        "end_time" : "05:15",
                        "fees" : 500,
                        "is_available" : 1
                    },
                    {
                        "id" : 1,
                        "start_time" : "05:15",
                        "end_time" : "05:30",
                        "fees" : 500,
                        "is_available" : 1
                    },
                    {
                        "id" : 1,
                        "start_time" : "05:30",
                        "end_time" : "05:45",
                        "fees" : 500,
                        "is_available" : 0
                    },
                    {
                        "id" : 1,
                        "start_time" : "05:45",
                        "end_time" : "06:00",
                        "fees" : 500,
                        "is_available" : 1
                    },
                    {
                        "id" : 1,
                        "start_time" : "18:00",
                        "end_time" : "18:15",
                        "fees" : 500,
                        "is_available" : 1
                    },
                    {
                        "id" : 1,
                        "start_time" : "18:15",
                        "end_time" : "18:30",
                        "fees" : 500,
                        "is_available" : 1
                    },
                    {
                        "id" : 1,
                        "start_time" : "18:30",
                        "end_time" : "18:45",
                        "fees" : 500,
                        "is_available" : 0
                    },
                    {
                        "id" : 1,
                        "start_time" : "18:45",
                        "end_time" : "19:00",
                        "fees" : 500,
                        "is_available" : 1
                    },
                    {
                        "id" : 1,
                        "start_time" : "19:00",
                        "end_time" : "19:15",
                        "fees" : 500,
                        "is_available" : 1
                    },
                    {
                        "id" : 1,
                        "start_time" : "19:15",
                        "end_time" : "19:30",
                        "fees" : 500,
                        "is_available" : 0
                    },
                    {
                        "id" : 1,
                        "start_time" : "19:30",
                        "end_time" : "19:45",
                        "fees" : 500,
                        "is_available" : 1
                    }
                ]
            }

+ Response 400

    + Headers

            Content-Type: application/json

    + Body

            {
                "errors": [
                    {
                        "message": "No doctor time slots found with given parameters",
                        "source": ""
                    }
                ]
            }