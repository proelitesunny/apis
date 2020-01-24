<?php

namespace App\Http\Requests\Api\Aggregator\Patients;

use Illuminate\Validation\Rule;
use App\MyHealthcare\Repositories\Country\CountryInterface;
use App\MyHealthcare\Repositories\State\StateInterface;
use App\MyHealthcare\Repositories\City\CityInterface;

class UpdateRequest extends \App\Http\Requests\Api\BaseRequest
{
    protected $countryInterface;
    protected $stateInterface;
    protected $cityInterface;

    public function __construct(
        array $query = array(),
        array $request = array(),
        array $attributes = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        CountryInterface $country,
        StateInterface $state,
        CityInterface $city)
    {

        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->countryInterface = $country;
        $this->stateInterface = $state;
        $this->cityInterface = $city;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['patient_id', 'first_name', 'last_name', 'gender', 'dob', 'email', 'city_name', 'state_name', 'country_name', 'pin_code', 'aadhar_number', 'passport_number', 'pan_number', 'emergency_contact_name', 'emergency_contact_no']));

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'patient_id' => 'required|exists:patients,id',
            'patient_id' => [
                'required',
                Rule::exists('patients','id')->where(function ($query) {
                    $query->where('create_source', config('constants.CREATION_SOURCE_INTERNAL')['aggregator_'.strtolower($this->header('aggregatorType'))]);
                })
            ],
            'first_name' => 'required|min:3|alpha',
            'last_name' => 'nullable|min:1',
            'gender' => 'required|in:'.implode(',', array_keys(config('constants.GENDER_ENUM'))),
            'dob' => 'required|date_format:'.config('api.aggregator_api.date_format.input').'|before:tomorrow|after:1900-01-01',
            'email' => 'nullable|email',
            'city_name' =>  'nullable|required_with:country,state|exists:cities,name',
            'state_name' =>  'nullable|required_with:country,city|exists:states,name',
            'country_name' =>  'nullable|required_with:state,city|exists:countries,name',
            'pin_code' => 'nullable|min:1|max:14|regex:/^([a-zA-Z0-9]+[-\s]?)+[a-zA-Z0-9]+$/',  //allow number with dash(-)
            'aadhar_number' => 'nullable|regex:/^([a-zA-Z0-9]+[-\s]?)+[a-zA-Z0-9]+$/',
            'passport_number' => 'nullable|regex:/^([a-zA-Z0-9]+[-\s]?)+[a-zA-Z0-9]+$/',
            'pan_number' => 'nullable|regex:/^([a-zA-Z0-9]+[-\s]?)+[a-zA-Z0-9]+$/',
            'emergency_contact_name' => 'nullable|min:3|string',
            'emergency_contact_no' => 'required|regex:/^[0-9]{1,4}?(\d)(?!\1+$)\d{1,12}$/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'patient_id.required' => trans('errors.PATIENT_147'),
            'patient_id.exists' => trans('errors.PATIENT_122'),

            'first_name.required' => trans('errors.PATIENT_106'),
            'first_name.min' => trans('errors.PATIENT_134'),
            'first_name.alpha' => trans('errors.PATIENT_135'),

            // 'last_name.required' => trans('errors.PATIENT_107'),
            'last_name.min' => trans('errors.PATIENT_136'),
            'last_name.alpha' => trans('errors.PATIENT_137'),

            'gender.required' => trans('errors.PATIENT_109'),
            'gender.in' => trans('errors.PATIENT_130'),

            'dob.required' => trans('errors.PATIENT_138'),
            'dob.date_format' => trans('errors.PATIENT_127'),
            'dob.before' => trans('errors.PATIENT_139'),
 
            'email.email' => trans('errors.PATIENT_142'),

            'country_name:exists' => trans('errors.PATIENT_143'),
            'state_name:exists' => trans('errors.PATIENT_144'),
            'city_name:exists' => trans('errors.PATIENT_145'),
            'pin_code:regex' => trans('errors.PATIENT_146'),

            'emergency_contact_name.min' => trans('errors.PATIENT_129'),
            'emergency_contact_name.string' => trans('errors.PATIENT_129'),

            'emergency_contact_no.required' => trans('errors.PATIENT_153'),
            'emergency_contact_no.regex' => trans('errors.PATIENT_152'),
        ];
    }

    public function validate()
    {
        parent::validate();

        if (!empty($this->country_name)) {
            $country = $this->countryInterface->getCountryByName($this->country_name);
            $this->request->set('country_id', $country->id);
        }

        if (!empty($this->state_name)) {
            $state = $this->stateInterface->getStateByName($this->state_name);
            $this->request->set('state_id', $state->id);

            if (strtolower($this->country_name) != strtolower($state->country->name)) {
                abort(400, trans('errors.PATIENT_148'), es('country_name'));
            }

            $this->request->set('country_id', $state->country->id);
        }

        if (!empty($this->city_name)) {

            $city = $this->cityInterface->getCityByName($this->city_name);
            $this->request->set('city_id', $city->id);

            if (strtolower($this->state_name) != strtolower($city->state->name)) {
                abort(400, trans('errors.PATIENT_149'), es('state_name'));
            }

            if (strtolower($this->country_name) != strtolower($city->state->country->name)) {
                abort(400, trans('errors.PATIENT_148'), es('country_name'));
            }

            $this->request->set('state_id', $city->state->id);
            $this->request->set('country_id', $city->state->country->id);
        }

        $this->setInternalDateFormat('dob');

        $this->offsetUnset('country_name');
        $this->offsetUnset('city_name');
        $this->offsetUnset('state_name');

        // Check For Aadhar, PAN, Passport
        $this->getIdTypeValue();

        $this->request->set('emergency_contact', $this->emergency_contact_no);
        $this->offsetUnset('emergency_contact_no');

        $this->request->set('guardian_name', $this->emergency_contact_name);
        $this->offsetUnset('emergency_contact_name');
    }
}
