<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\LoanInterface;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Log;
use Exception;

class LoanController extends Controller
{

    //Initialize Interface in one variable
    protected $loanInterface = null;

    public function __construct()
    {
        //Initializing Interface
        $this->loanInterface = app(LoanInterface::class);
    }

    public function createUserLoan(Request $request){

        Log::useDailyFiles(storage_path().'/logs/loan/create_loan.log');

        //Authenticate User Through Access Token
        try {
            $user  = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            throw new \Exception("Unable to find user");
        }

        //Sanitizing The Input
        $data = $request->all();

        //Validating the Request
        $validator = Validator::make($data, [
            'total_amount' => 'required|numeric',
            'loan_term'    => 'required|string|min:20|max:10000',
            'start_date'   => 'date_format:Y-m-d'
        ]);

        //If Any Validation fails error will be thrown
        if($validator->fails()){
            return [ "error" => json_decode($validator->errors()->toJson()) ];
        }

        //Assigning users_id to passed data
        $data['users_id'] = $user['id'];

        //Assigning Other Required fields
        $data['status'] = 'pending';
        $data['total_amount_due'] = $data['total_amount'];
        $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : date('Y-m-d');

        //Logging Input Data
        Log::info("Input Data ".json_encode($data));

        try{
            return [ 'data' => $this->loanInterface->createUserLoan($data) ];
        }
        catch(\Exception $e){
            Log::error('Loan create API error message: '.$e->getMessage());
            throw new \Exception('Unable to create loan: '. $e->getMessage());
        }
    }

    public function getLoanDetails(){

        //Authenticate User Through Access Token
        try {
            $user  = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            throw new \Exception("Unable to find user");
        }

        //Assigning users_id to passed data
        $data['users_id'] = $user['id'];

        try{
            return [ 'data' => $this->loanInterface->getLoanDetails($user['id']) ];
        }
        catch(\Exception $e){
            throw new \Exception('Unable to get loan Details: '. $e->getMessage());
        }
    }

    public function approveUserLoan(Request $request,$loan_id){

        Log::useDailyFiles(storage_path().'/logs/loan/approve_loan.log');

        //Checking Loan ID is provided or not
        if( empty($loan_id) ){
            throw new \Exception('Loan ID not provided');
        }

        //Authenticate User Through Access Token
        try {
            $user  = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            throw new \Exception("Unable to find user");
        }

        $data['loans_id'] = $loan_id;

        //Assigning users_id to passed data
        $data['users_id'] = $user['id'];

        //Validating the Request
        $validator = Validator::make($data, [
            'loans_id' => 'required|numeric'
        ]);

        //If Any Validation fails error will be thrown
        if($validator->fails()){
            return [ "error" => json_decode($validator->errors()->toJson()) ];
        }

        //Logging Input Data
        Log::info("Input Data ".json_encode($data));

        try{
            return $this->loanInterface->approveUserLoan($data);
        }
        catch(\Exception $e){
            Log::error('Loan approve API error message: '.$e->getMessage());
            throw new \Exception('Unable to approve loan: '. $e->getMessage());
        }
    }

    public function rejectUserLoan(Request $request,$loan_id){

        Log::useDailyFiles(storage_path().'/logs/loan/reject_loan.log');

        //Checking Loan ID is provided or not
        if( empty($loan_id) ){
            throw new \Exception('Loan ID not provided');
        }

        //Authenticate User Through Access Token
        try {
            $user  = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            throw new \Exception("Unable to find user");
        }

        $data['loans_id'] = $loan_id;

        //Assigning users_id to passed data
        $data['users_id'] = $user['id'];

        //Validating the Request
        $validator = Validator::make($data, [
            'loans_id' => 'required|numeric'
        ]);

        //If Any Validation fails error will be thrown
        if($validator->fails()){
            return [ "error" => json_decode($validator->errors()->toJson()) ];
        }

        //Logging Input Data
        Log::info("Input Data ".json_encode($data));

        try{
            return $this->loanInterface->rejectUserLoan($data);
        }
        catch(\Exception $e){
            Log::error('Loan reject API error message: '.$e->getMessage());
            throw new \Exception('Unable to reject loan: '. $e->getMessage());
        }
    }

    public function processLoanPayment(Request $request){

        Log::useDailyFiles(storage_path().'/logs/loan/loan_payment.log');

        //Authenticate User Through Access Token
        try {
            $user  = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            throw new \Exception("Unable to find user");
        }

        $data = $request->all();

        //Validating the Request
        $validator = Validator::make($data, [
            'loans_id' => 'required|numeric',
            'amount'   => 'required|numeric'
        ]);

        //If Any Validation fails error will be thrown
        if($validator->fails()){
            return [ "error" => json_decode($validator->errors()->toJson()) ];
        }

        if( $data['amount'] < 1 ){
            return response()->json(['status' => 'Pls Provide Amount Greater Than Or Equal To One']);
        }

        //Assigning users_id to passed data
        $data['users_id'] = $user['id'];

        //Logging Input Data
        Log::info("Input Data ".json_encode($data));

        try{
            return $this->loanInterface->processLoanPayment($data);
        }
        catch(\Exception $e){
            Log::error('Loan payment API error message: '.$e->getMessage());
            throw new \Exception('Unable to process loan payment: '. $e->getMessage());
        }
    }

}