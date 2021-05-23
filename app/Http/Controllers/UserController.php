<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    /**
     * Autherizing user email and password provided for login
     *
     * @param $email_id,$password
     *
     * return token if authenticated
     *
     * @throws Exception
     */

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email_id', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * Registering user in system based on given data
     *
     * @param $email_id,$password,$name
     *
     * return token if registered successfully
     *
     * @throws Exception
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email_id' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email_id' => $request->get('email_id'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    /**
     * Returns user data from system based on given token
     *
     * @param $token
     *
     * return user data if user is logged in and token is authorized
     *
     * @throws Exception
     */
    public function getAuthenticatedUser(Request $request)
    {
        try {
            $user = JWTAuth::authenticate($request->token);
            return response()->json(['user' => $user]);
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'token_absent'], $e->getStatusCode());
        }
    }

    /**
     * Make the user logout and token expired
     *
     * @param $token
     *
     * return success message if user is authenticate
     *
     * @throws Exception
     */
    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(["status" => 'user is logged out'], 200);
    }

    /**
     * Authorize user in system to approve loans
     *
     * @param $users_id
     *
     * return status
     *
     * @throws Exception
     */
    public function authorizeUser(Request $request,$users_id)
    {

        if( empty($users_id) ){
            throw new \Exception('User ID not provided');
        }

        try {
            $user = JWTAuth::authenticate($request->token);

            if( !empty($user) && !empty($user['id']) && $user['id'] == $users_id ){
                if( !$user['is_loan_approver'] ){
                    try{
                        $user = User::where('id',$users_id)->update(['is_loan_approver'=>'1']);
                        return response()->json(['status' => 'User is Authorized']);
                    }
                    catch(\Exception $e){
                        throw new \Exception('Unable to give permission: '. $e->getMessage());
                    }
                }
                else{
                    throw new \Exception('User Is Already Authorized');
                }
            }
            else{
                throw new \Exception('User ID not Found');
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'token_absent'], $e->getStatusCode());
        }
    }
}