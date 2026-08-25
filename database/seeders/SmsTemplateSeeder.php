<?php

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Mirrors DEMO_TEMPLATES from sms-configuration.blade.php exactly, so the
         * "Message Templates" panel shows the same 5 cards as before -- except
         * now they are real, editable/deletable rows instead of hardcoded demo data.
         *
         * Uses updateOrCreate(name) so re-running `db:seed` is safe and won't
         * create duplicates.
         */

        $templates = [
            [
                'name'    => 'Employee Absence Reminder',
                'type'    => 'absence_notice',
                'message' => 'Dear {name}, you were marked absent on {date}. Please contact HR at {company_name} if this is incorrect.',
            ],
            [
                'name'    => 'Office Holiday Announcement',
                'type'    => 'general_notice',
                'message' => 'Dear {name}, please note {company_name} will remain closed on {date} for a public holiday.',
            ],
            [
                'name'    => 'Attendance Warning',
                'type'    => 'warning_notice',
                'message' => 'Dear {name}, this is a formal warning regarding repeated late attendance. Please report to HR immediately.',
            ],
            [
                'name'    => 'Welcome Aboard',
                'type'    => 'joining_notice',
                'message' => 'Dear {name}, welcome to {company_name}! Your first day is {date}. We are excited to have you in the {department} team.',
            ],
            [
                'name'    => 'Payment Reminder',
                'type'    => 'customer_notice',
                'message' => 'Dear {name}, your invoice payment is due. Please contact us at your earliest convenience. - {company_name}',
            ],
        ];

        foreach ($templates as $t) {
            SmsTemplate::updateOrCreate(
                ['name' => $t['name']],
                ['type' => $t['type'], 'message' => $t['message']]
            );
        }
    }
}
