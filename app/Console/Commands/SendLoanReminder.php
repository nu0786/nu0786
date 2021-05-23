<?php

namespace App\Console\Commands;

use App\Entities\Loan;
use Illuminate\Console\Command;
use Log;
use DB;
# Include the Autoloader (see "Libraries" for install instructions)
require 'vendor/autoload.php';
use Mailgun\Mailgun;

class SendLoanReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remider:send_loan_reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useDailyFiles(storage_path().'/logs/cron/send_loan_reminders.log');
        Log::debug('Inside '.__METHOD__.' function.');

        try {
            Log::info('starting cron');
            Log::info('getting all loans details for which reminder has to be send');
            $loans_data = DB::table('loans')
                                ->select('*')
                                ->orderBy('loans_id','ASC')
                                ->where('status','approved')
                                ->whereRaw('COALESCE(DATEDIFF(CURDATE(),COALESCE(last_send_at,start_date)),0) >= 7')
                                ->chunk(10000, function($loansData){

                                    //Variables Of Query To Update Send Date Of Loans
                                    $cases = [];
                                    $ids = [];
                                    $params = [];

                                    //creating mailgun object
                                    $mgClient = new Mailgun('YOUR_API_KEY');
                                    $domain = "YOUR_DOMAIN_NAME";

                                    foreach ($loansData as $loanData)
                                    {
                                        Log::info('Sending Email');
                                        
                                        //Send Loan Mail
                                        # Make the call to the client.
                                        $result = $mgClient->sendMessage($domain, array(
                                            'from'	=> 'Test User <nu0786@gmail.com>',
                                            'to'	=> 'Nishant <nu0786@gmail.com>',
                                            'subject' => 'Reminder!! For Loan Payment',
                                            'text'	=> 'Hi, This a reminder for loan payment'
                                        ));
                                        Log::info('Mail sent result. '.json_encode($result));

                                        //Assign Data To Update Loan Data
                                        $cases[] = "WHEN {$loanData->loans_id} then ?";
                                        $params[] = date('Y-m-d');
                                        $ids[] = $loanData->loans_id;
                                    }
                                    //Updating The last_send_at For Approved Loans
                                    Log::info('Updating The last_send_at For Approved Loans Ids: '.json_encode($ids));
                                    $ids = implode(',', $ids);
                                    $cases = implode(' ', $cases);
                                    if (!empty($ids)) {
                                        \DB::update("UPDATE loans SET `last_send_at` = CASE `loans_id` {$cases} END WHERE `loans_id` in ({$ids})", $params);
                                    }
                                });
        }catch (\Exception $e){
            Log::error($e);
            throw $e;
        }
    }
}
