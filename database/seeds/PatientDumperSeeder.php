<?php

use Illuminate\Database\Seeder;
use App\Models\Patient_dumper;

class PatientDumperSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $totalRecords = 0;
        $recordNotInserted = 0;
        $startTime = round(microtime(true), 4);
        $faker = \Faker\Factory::create();
        $this->command->getOutput()->progressStart($totalRecords);
        for ($i = 1; $i <= $totalRecords; $i++) {
            $newPatient = [
                'uhid' => 'FHL_' . $faker->numberBetween(100000, 999999),
                'facility_code' => 'HOS-'.$faker->numberBetween(50, 99),
                'facility_name' => 'load_test_' . $faker->firstName,
                'first_name' => 'load' . $faker->firstName,
                'last_name' => $faker->lastName,
                'dob' => $faker->date("Y-m-d"),
                'gender' => 'Male',
                'mobile_no' => rand(1000000000, 9999999999),
                'address' => $faker->address,
                'pin_code' => $faker->numberBetween(100000, 999990)
            ];
            try {
                $this->command->getOutput()->progressAdvance();
                $client = new \GuzzleHttp\Client();
                $url = "local.spine-dumper.com/api/v1/patients/create";
                $AuthorizationToken = "eyJpdiI6IjZObmxIa2liclwvSmR6ME5RQU1GeXlBPT0iLCJ2YWx1ZSI6ImNLTXVvOXU1R0pDMXhUbGpvRmVtREo1Z2F3dVJHZFRWQk5Pd3Q4NEkzV2c5ZFhSOHNZRWpsdDU1R1l0WVhjNkQ5bXhmdEs5cGhNSEg3dmwySWdHMUJBPT0iLCJtYWMiOiJlY2RhZWVmOGMzNzVmNTEyYTVhMGEzOTk0NTFlOGY2ZGRmNjJiN2E3YmU2N2UyMzEwNzRjN2ViMjM4ODc2ZDA2In0=";

                $response = $client->request('post', $url, ['content-type' => 'applicaton/json',
                    'headers' => ['Authorization' => $AuthorizationToken],
                    'json' => $newPatient]);
            } catch (\Exception $e) {
                $recordNotInserted++;
                logger()->error($e->getMessage());
            }
        }
        $this->command->getOutput()->progressFinish();
        logger()->info('$totalRecords = ' . ($totalRecords) . ' Time = ' . (round(microtime(true), 2) - $startTime));
        echo "\n Total Records = " . $totalRecords;
        echo "\n Total Records not inserted = " . $recordNotInserted;
        echo "\n Total Records Inserted = " . ($totalRecords - $recordNotInserted);
        echo "\n Start - End At = " . $startTime . "-" . (\Carbon\Carbon::now()->format('Y-m-d H:i:s'));
        echo "\n Time Taken = " . ((round(microtime(true), 2) - $startTime)) . " seconds";
        echo "\n";
    }

}
