<?php

namespace App\Providers;

use App\MyHealthcare\Repositories\AdminUser\AdminUserInterface;
use App\MyHealthcare\Repositories\AdminUser\AdminUserRepository;
use App\MyHealthcare\Repositories\AuditLogger\AuditInterface;
use App\MyHealthcare\Repositories\AuditLogger\AuditRepository;
use App\MyHealthcare\Repositories\Booking\BookingInterface;
use App\MyHealthcare\Repositories\Booking\BookingRepository;
use App\MyHealthcare\Repositories\City\CityInterface;
use App\MyHealthcare\Repositories\City\CityRepository;
use App\MyHealthcare\Repositories\Country\CountryInterface;
use App\MyHealthcare\Repositories\Country\CountryRepository;
use App\MyHealthcare\Repositories\CustomerCare\CustomerCareInterface;
use App\MyHealthcare\Repositories\CustomerCare\CustomerCareRepository;
use App\MyHealthcare\Repositories\Doctor\DoctorInterface;
use App\MyHealthcare\Repositories\Doctor\DoctorRepository;
use App\MyHealthcare\Repositories\DoctorHoliday\DoctorHolidayInterface;
use App\MyHealthcare\Repositories\DoctorHoliday\DoctorHolidayRepository;
use App\MyHealthcare\Repositories\DoctorTimeSchedule\DoctorTimeScheduleInterface;
use App\MyHealthcare\Repositories\DoctorTimeSchedule\DoctorTimeScheduleRepository;
use App\MyHealthcare\Repositories\FrontEndDesk\FrontEndDeskInterface;
use App\MyHealthcare\Repositories\FrontEndDesk\FrontEndDeskRepository;
use App\MyHealthcare\Repositories\HealthOffer\HealthOfferRepository;
use App\MyHealthcare\Repositories\Hospital\HospitalInterface;
use App\MyHealthcare\Repositories\Hospital\HospitalRepository;
use App\MyHealthcare\Repositories\Medicine\MedicineInterface;
use App\MyHealthcare\Repositories\Medicine\MedicineRepository;
use App\MyHealthcare\Repositories\MedicineUsage\MedicineUsageInterface;
use App\MyHealthcare\Repositories\MedicineUsage\MedicineUsageRepository;
use App\MyHealthcare\Repositories\Patient\PatientInterface;
use App\MyHealthcare\Repositories\Patient\PatientRepository;
use App\MyHealthcare\Repositories\PatientHealthDetail\PatientHealthDetailInterface;
use App\MyHealthcare\Repositories\PatientHealthDetail\PatientHealthDetailRepository;
use App\MyHealthcare\Repositories\PatientPreference\PatientPreferenceInterface;
use App\MyHealthcare\Repositories\PatientPreference\PatientPreferenceRepository;
use App\MyHealthcare\Repositories\Permission\PermissionInterface;
use App\MyHealthcare\Repositories\Permission\PermissionRepository;
use App\MyHealthcare\Repositories\Prescription\PrescriptionInterface;
use App\MyHealthcare\Repositories\Prescription\PrescriptionRepository;
use App\MyHealthcare\Repositories\Role\RoleInterface;
use App\MyHealthcare\Repositories\Role\RoleRepository;
use App\MyHealthcare\Repositories\Speciality\SpecialityInterface;
use App\MyHealthcare\Repositories\Speciality\SpecialityRepository;
use App\MyHealthcare\Repositories\State\StateInterface;
use App\MyHealthcare\Repositories\State\StateRepository;
use App\MyHealthcare\Repositories\DoctorTimeSlot\DoctorTimeSlotInterface;
use App\MyHealthcare\Repositories\DoctorTimeSlot\DoctorTimeSlotRepository;
use App\MyHealthcare\Repositories\Transaction\TransactionInterface;
use App\MyHealthcare\Repositories\Transaction\TransactionRepository;
use App\MyHealthcare\Repositories\User\UserInterface;
use App\MyHealthcare\Repositories\User\UserRepository;
use App\MyHealthcare\Repositories\Page\PageInterface;
use App\MyHealthcare\Repositories\Page\PageRepository;
use App\MyHealthcare\Repositories\Faq\FaqInterface;
use App\MyHealthcare\Repositories\Faq\FaqRepository;
use App\MyHealthcare\Repositories\InsuranceTieUp\InsuranceTieUpInterface;
use App\MyHealthcare\Repositories\InsuranceTieUp\InsuranceTieUpRepository;
use App\MyHealthcare\Repositories\InternationalProcedureSurgery\InternationalProcedureSurgeryInterface;
use App\MyHealthcare\Repositories\InternationalProcedureSurgery\InternationalProcedureSurgeryRepository;
use App\MyHealthcare\Repositories\Report\DoctorReportInterface;
use App\MyHealthcare\Repositories\Report\DoctorReportRepository;
use App\MyHealthcare\Repositories\Report\DoctorTimeScheduleReportInterface;
use App\MyHealthcare\Repositories\Report\DoctorTimeScheduleReportRepository;
use App\MyHealthcare\Repositories\Report\PatientReportInterface;
use App\MyHealthcare\Repositories\Report\PatientReportRepository;
use App\MyHealthcare\Repositories\Report\BookingReportInterface;
use App\MyHealthcare\Repositories\Report\BookingReportRepository;
use App\MyHealthcare\Repositories\Configuration\ConfigurationInterface;
use App\MyHealthcare\Repositories\Configuration\ConfigurationRepository;
use Illuminate\Support\ServiceProvider;
use App\MyHealthcare\Repositories\HealthOffer\HealthOfferInterface;
use App\MyHealthcare\Repositories\HealthOffer\MockHealthOfferRepository;

use App\MyHealthcare\Repositories\InternationalPatientEstimate\InternationalPatientEstimateInterface;
use App\MyHealthcare\Repositories\InternationalPatientEstimate\InternationalPatientEstimateRepository;

//HealthArticle
use App\MyHealthcare\Repositories\HealthArticle\HealthArticleInterface;
use App\MyHealthcare\Repositories\HealthArticle\HealthArticleRepository;

//SmsLog
use App\MyHealthcare\Repositories\SmsLog\SmsLogInterface;
use App\MyHealthcare\Repositories\SmsLog\SmsLogRepository;

//EmailLog
use App\MyHealthcare\Repositories\EmailLog\EmailLogInterface;
use App\MyHealthcare\Repositories\EmailLog\EmailLogRepository;

//Hospital Holiday
use App\MyHealthcare\Repositories\HospitalHoliday\HospitalHolidayInterface;
use App\MyHealthcare\Repositories\HospitalHoliday\HospitalHolidayRepository;
use App\MyHealthcare\Repositories\DoctorSchedule\DoctorScheduleInterface;
use App\MyHealthcare\Repositories\DoctorSchedule\DoctorScheduleRepository;

use App\MyHealthcare\Repositories\MasterPatientIndex\MasterPatientIndexRepository;
use App\MyHealthcare\Repositories\MasterPatientIndex\MasterPatientIndexInterface;
use App\MyHealthcare\Repositories\PatientHisMapping\PatientHisMappingInterface;
use App\MyHealthcare\Repositories\PatientHisMapping\PatientHisMappingRepository;

use App\MyHealthcare\Repositories\DoctorSessionSlot\DoctorSessionSlotInterface;
use App\MyHealthcare\Repositories\DoctorSessionSlot\DoctorSessionSlotRepository;

use App\MyHealthcare\Repositories\BlockDoctorSlot\BlockDoctorSlotInterface;
use App\MyHealthcare\Repositories\BlockDoctorSlot\BlockDoctorSlotRepository;

// Test Booking
use App\MyHealthcare\Repositories\TestBooking\TestBookingInterface;
use App\MyHealthcare\Repositories\TestBooking\TestBookingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind(SpecialityInterface::class, SpecialityRepository::class);

        $this->app->bind(StateInterface::class, StateRepository::class);

        $this->app->bind(CityInterface::class, CityRepository::class);

        $this->app->bind(CountryInterface::class, CountryRepository::class);

        // $this->app->bind(UserInterface::class, UserRepository::class);

        $this->app->bind(DoctorInterface::class, DoctorRepository::class);

        // $this->app->bind(RoleInterface::class, RoleRepository::class);

        // $this->app->bind(PermissionInterface::class, PermissionRepository::class);

        // $this->app->bind(CustomerCareInterface::class, CustomerCareRepository::class);

        // $this->app->bind(FrontEndDeskInterface::class, FrontEndDeskRepository::class);

        // $this->app->bind(DoctorTimeScheduleInterface::class, DoctorTimeScheduleRepository::class);

        // $this->app->bind(DoctorTimeSlotInterface::class, DoctorTimeSlotRepository::class);

        $this->app->bind(DoctorSessionSlotInterface::class, DoctorSessionSlotRepository::class);

        // $this->app->bind(AdminUserInterface::class, AdminUserRepository::class);

        $this->app->bind(PatientInterface::class, PatientRepository::class);

        $this->app->bind(PatientHealthDetailInterface::class, PatientHealthDetailRepository::class);

        // $this->app->bind(PatientPreferenceInterface::class, PatientPreferenceRepository::class);

        $this->app->bind(BookingInterface::class, BookingRepository::class);

        $this->app->bind(TransactionInterface::class, TransactionRepository::class);

        // $this->app->bind(PrescriptionInterface::class, PrescriptionRepository::class);

        // $this->app->bind(MedicineUsageInterface::class, MedicineUsageRepository::class);

        // $this->app->bind(MedicineInterface::class, MedicineRepository::class);

        // $this->app->bind(PageInterface::class, PageRepository::class);

        // $this->app->bind(HospitalInterface::class, HospitalRepository::class);

        // $this->app->bind(FaqInterface::class, FaqRepository::class);

        // $this->app->bind(InsuranceTieUpInterface::class, InsuranceTieUpRepository::class);

        // $this->app->bind(InternationalProcedureSurgeryInterface::class, InternationalProcedureSurgeryRepository::class);

        // $this->app->bind(AuditInterface::class, AuditRepository::class);

        // $this->app->bind(DoctorReportInterface::class, DoctorReportRepository::class);

        // $this->app->bind(DoctorTimeScheduleReportInterface::class, DoctorTimeScheduleReportRepository::class);

        // $this->app->bind(PatientReportInterface::class, PatientReportRepository::class);

        // $this->app->bind(BookingReportInterface::class, BookingReportRepository::class);

        // $this->app->bind(ConfigurationInterface::class, ConfigurationRepository::class);

        // $this->app->bind(HealthOfferInterface::class, HealthOfferRepository::class);

        // $this->app->bind(InternationalPatientEstimateInterface::class, InternationalPatientEstimateRepository::class);

        // $this->app->bind(HealthArticleInterface::class, HealthArticleRepository::class);

        // $this->app->bind(SmsLogInterface::class, SmsLogRepository::class);

        // $this->app->bind(EmailLogInterface::class, EmailLogRepository::class);

        // $this->app->bind(DoctorHolidayInterface::class, DoctorHolidayRepository::class);

        // $this->app->bind(HospitalHolidayInterface::class, HospitalHolidayRepository::class);

        // $this->app->bind(DoctorScheduleInterface::class, DoctorScheduleRepository::class);

        $this->app->bind(MasterPatientIndexInterface::class, MasterPatientIndexRepository::class);

        // $this->app->bind(PatientHisMappingInterface::class, PatientHisMappingRepository::class);

        $this->app->bind(BlockDoctorSlotInterface::class, BlockDoctorSlotRepository::class);

        $this->app->bind(TestBookingInterface::class, TestBookingRepository::class);

    }

    public function provides()
    {
        return [
            SpecialityInterface::class,
            StateInterface::class,
            CityInterface::class,
            CountryInterface::class,
            UserInterface::class,
            DoctorInterface::class,
            RoleInterface::class,
            PermissionInterface::class,
            CustomerCareInterface::class,
            DoctorTimeScheduleInterface::class,
            DoctorTimeSlotInterface::class,
            FrontEndDeskInterface::class,
            AdminUserInterface::class,
            PatientInterface::class,
            PatientHealthDetailInterface::class,
            PatientPreferenceInterface::class,
            BookingInterface::class,
            TransactionInterface::class,
            PrescriptionInterface::class,
            MedicineUsageInterface::class,
            MedicineInterface::class,
            PageInterface::class,
            HospitalInterface::class,
            FaqInterface::class,
            InsuranceTieUpInterface::class,
            InternationalProcedureSurgeryInterface::class,
            AuditInterface::class,
            DoctorReportInterface::class,
            DoctorTimeScheduleReportInterface::class,
            PatientReportInterface::class,
            BookingReportInterface::class,
            ConfigurationInterface::class,
            HealthOfferInterface::class,
            InternationalPatientEstimateInterface::class,
            HealthArticleInterface::class,
            SmsLogInterface::class,
            EmailLogInterface::class,
            DoctorHolidayInterface::class,
            HospitalHolidayInterface::class,
            DoctorScheduleInterface::class,
            MasterPatientIndexInterface::class,
            PatientHisMappingInterface::class,
            BlockDoctorSlotInterface::class,
            TestBookingInterface::class,
        ];
    }
}
