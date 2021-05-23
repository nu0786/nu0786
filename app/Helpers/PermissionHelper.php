<?php

namespace App\Helpers;

use DB;
use Log;

class ResourcePermissionHelper
{
	
	public function can_user_approve($users_id) {

        Log::debug('Inside '.__METHOD__.' function.');

        if(empty($users_id)) {
            Log::info('Users id is not provided');
            return false;
        }
        try {
            Log::info('Getting Data for given users_id');
            $usersData = DB::table('users')->where('id',$users_id)->where('is_loan_approver',1)->get();
            if( sizeof($usersData) > 0 ){
                Log::info('Data is present for given users_id');
                return true;
            }
            Log::info('Data is not present for given users_id');
            return false;
        } catch(\Exception $e) {
            Log::error('User Approval Permission error message: '.$e->getMessage());
            throw new \Exception('User Approval Permission error message: '.$e->getMessage());
        }
	}

}