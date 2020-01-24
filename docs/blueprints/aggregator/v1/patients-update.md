
### Update Patient [POST /patients/update]
Update a patient record in MyHealth Fortis Agent Portal.

#### Request Body Fields description

* patient_id: [string, required]
* first_name : [string, required]
* last_name : [string, optional]
* gender : [integer, required] (0 - male, 1 - female, 2 - other)
* dob  : [string,required] (format : Y-m-d, before : tomorrow)
* email : [string, optional]
* city_name  : [string,optional]
* state_name  : [string,optional]
* country_name  : [string,optional]
* pin_code  : [numeric,optional]
* aadhar_number  : [string,optional]
* passport_number  : [string,optional]
* pan_number  : [string,optional]
* emergency_contact_name : [string,optional]
* emergency_contact_no : [string,required] (max:16)

:::note
## Note
* apitoken token is required in headers.
* aggregatorType is required in headers.
* Update will be perform against "patient_id" considering it as  unique for individual patient.
* city_name, country_name, state_name are accepted in standard format, abbreviated / misspelled values considered as null.
* Mobile number plays a vital role while syncing of data. so it's not in update parameters.
* Field which contains values will only be updated.
:::

+ Request with headers

    + Headers

            Content-Type: application/json
            aggregatorType: 3rdParty
            apitoken: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0OTg4MDUzNDgsIm5iZiI6MTQ5ODgwNTM0OCwiZXhwIjoxNTAxMzk3MzQ4LCJ1aWQiOiJNUk4wMEEwMDAxIn

    + Body

            {
               "patient_id": "235",
               "emergency_contact_name": "Ramesh",
               "emergency_contact_no": "9078563411",
            }

+ Response 200

    + Headers

            Content-Type: application/json

    + Body

            {
                "message": "Patient record updated successfully"
            }

+ Response 400

    + Headers

            Content-Type: application/json

    + Body

            {
                "errors": [
                    {
                        "message": "Patient id is required.",
                        "source": "patient_id"
                    }
                ]
            }
