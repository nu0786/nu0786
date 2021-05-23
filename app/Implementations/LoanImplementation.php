<?php

namespace App\Implementations;

use App\Interfaces\LoanInterface;
use App\Entities\Loan;
use App\Entities\LoanPaymentsHistories;
use Log;
use Exception;
use App\Helpers\ResourcePermissionHelper;

class LoanImplementation implements LoanInterface
{

    /**
     * Create loan for the particular user
     *
     * @param $mixed
     *
     * return Created Loan Data
     *
     * @throws Exception
     */
    public function createUserLoan($data){

        Log::useDailyFiles(storage_path().'/logs/loan/create_loan.log');
        Log::debug('Inside '.__METHOD__.' function.');
        try{
            Log::info('Creating Data');
            $loanData = Loan::create($data);
            Log::info('Data Created Successful');
            return $loanData;
        }
        catch (\Exception $e) {
            Log::error('Loan create API error message: '.$e->getMessage());
            throw new \Exception('Unable to create loan: '. $e->getMessage());
        }
    }

    /**
     * Get loan Details for the particular User
     *
     * @param $users_id
     *
     * return User Loan Data
     *
     * @throws Exception
     */
    public function getLoanDetails($users_id){
        try{
            $loanData = Loan::where('users_id',$users_id)->with(['loan_payments_histories','users'])->get();
            return $loanData;
        }
        catch (\Exception $e) {
            throw new \Exception('Unable to get loan Details: '. $e->getMessage());
        }
    }

    /**
     * Approve loan for the particular user
     *
     * @param $loan_id
     *
     * return message
     *
     * @throws Exception
     */
    public function approveUserLoan($data){

        Log::useDailyFiles(storage_path().'/logs/loan/approve_loan.log');
        Log::debug('Inside '.__METHOD__.' function.');
        if( app(ResourcePermissionHelper::class)->can_user_approve($data['users_id']) ){
            try{
                //Checking Loan is already approved or not
                $loanData = Loan::where('loans_id',$data['loans_id'])->where('users_id',$data['users_id'])->where('status','approved')->get();
                if( sizeof($loanData) > 0 ){
                    return response()->json(['status' => 'Loan is Already Approved']);
                }
                Log::info('Updating Data');
                Loan::where('loans_id',$data['loans_id'])->where('users_id',$data['users_id'])->update([ 'status' => 'approved' ]);
                Log::info('Data Updated Successful');
                return response()->json(['status' => 'Loan is Approved']);
            }
            catch (\Exception $e) {
                Log::error('Loan Approve API error message: '.$e->getMessage());
                throw new \Exception('Unable to approve loan: '. $e->getMessage());
            }
        }
        else{
            throw new \Exception('User Is Not Permitted');
        }
    }

    /**
     * Reject loan for the particular user
     *
     * @param $loan_id
     *
     * return User Loan Data
     *
     * @throws Exception
     */
    public function rejectUserLoan($data){

        Log::useDailyFiles(storage_path().'/logs/loan/reject_loan.log');
        Log::debug('Inside '.__METHOD__.' function.');
        if( app(ResourcePermissionHelper::class)->can_user_approve($data['users_id']) ){
            try{
                //Checking Loan is already approved or not
                $loanData = Loan::where('loans_id',$data['loans_id'])->where('users_id',$data['users_id'])->whereIn('status',[ 'approved', 'rejected' ])->get();
                if( sizeof($loanData) > 0 ){
                    $loanStatus = $loanData[0]['status'];
                    return response()->json(['status' => 'Loan is Already '.ucwords($loanStatus)]);
                }
                Log::info('Updating Data');
                Loan::where('loans_id',$data['loans_id'])->where('users_id',$data['users_id'])->update([ 'status' => 'rejected' ]);
                Log::info('Data Updated Successful');
                return response()->json(['status' => 'Loan is Rejected']);
            }
            catch (\Exception $e) {
                Log::error('Loan Reject API error message: '.$e->getMessage());
                throw new \Exception('Unable to reject loan: '. $e->getMessage());
            }
        }
        else{
            throw new \Exception('User Is Not Permitted');
        }
    }

    /**
     * Loan Payment for the given loan_id
     *
     * @param $loan_id,$amount
     *
     * return message
     *
     * @throws Exception
     */
    public function processLoanPayment($data){

        Log::useDailyFiles(storage_path().'/logs/loan/loan_payment.log');
        Log::debug('Inside '.__METHOD__.' function.');
        try{
            //Checking Loan is already approved or not
            $loanData = Loan::where('loans_id',$data['loans_id'])->where('users_id',$data['users_id'])->whereIn('status',[ 'approved' ])->get();
            if( sizeof($loanData) > 0 ){

                Log::info('Updating Data');
                Loan::where('loans_id',$data['loans_id'])->where('users_id',$data['users_id'])->update([ 'last_paid_at' => date('Y-m-d'),'total_amount_due' => $loanData[0]['total_amount_due'] - $data['amount'] ]);
                Log::info('Data Updated Successful');

                $loanPaymentsHistoriesData = array(
                                                'users_id'       => $data['users_id'],
                                                'loans_id'       => $data['loans_id'],
                                                'amount_paid'    => $data['amount'],
                                                'payment_status' => 'success',
                                                'paid_at'        => date('Y-m-d')
                                            );
                Log::info('Inserting Data In LoanPaymentsHistories Table: '.json_encode($loanPaymentsHistoriesData));
                LoanPaymentsHistories::create($loanPaymentsHistoriesData);
                Log::info('Data Created Successful In LoanPaymentsHistories Table');
                return response()->json(['status' => 'Payment Is Processed']);
            }
            else{
                return response()->json(['status' => 'Unable To Find Loan Details']);
            }
        }
        catch (\Exception $e) {
            Log::error('Loan payment API error message: '.$e->getMessage());

            $loanPaymentsHistoriesData = array(
                                            'users_id'       => $data['users_id'],
                                            'loans_id'       => $data['loans_id'],
                                            'amount_paid'    => $data['amount'],
                                            'payment_status' => 'failed',
                                            'paid_at'        => date('Y-m-d')
                                        );
            Log::info('Inserting Data In LoanPaymentsHistories Table: '.json_encode($loanPaymentsHistoriesData));
            LoanPaymentsHistories::create($loanPaymentsHistoriesData);
            Log::info('Data Created Successful In LoanPaymentsHistories Table');

            throw new \Exception('Unable to process loan payment: '. $e->getMessage());
        }
    }
}