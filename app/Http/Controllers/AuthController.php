<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    protected $user;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|between:2,255',
            'last_name' => 'required|string|between:2,255',
            'gender' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:password',
            'image' => 'sometimes|string',
            'date_of_birth' => 'nullable',
            'academic_year' => 'nullable'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return  response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {

        $user_id = auth()->id();
        $user = User::whereId($user_id)->with("roles")->first();

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
        $token = JWTAuth::getToken();

        JWTAuth::invalidate($token);
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    } catch (\Exception $e) {
        //throw $th;
    }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $oldToken = JWTAuth::getToken();
            $newToken = auth()->refresh();

            if($oldToken){
                try {
                    JWTAuth::invalidate($oldToken);
                } catch (\Throwable $th) {
                    \Log::warning("Token could not be invalidated");
                }
            }

            return $this->respondWithToken($newToken);
        } catch (\Exception $e) {
            return response()->json(['error' => 'could not refresh token', "message" => $e->getMessage()], 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {

        $user_id = auth()->id();
        $user = User::whereId($user_id)->with("roles")->first();

        return response()->json([

            
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }


    public function sendRegistrationInvite(Request $request) {
        if ($this->user->hasRole('Admin')){
            try {
                $validator = Validator::make($request->all(), [
                    'invited_users'=> 'required|string',
                ]);
             

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $invited_users = array_map('trim', explode(', ', $request->invited_users));
            $invited_users = array_unique($invited_users);
            $invited_users = array_filter($invited_users);
            $invited_users = array_values($invited_users);
            $invalid_emails = [];
            $valid_emails = [];
            $existing_users = [];

            foreach($invited_users as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalid_emails[] = $email;
                }else {
                    $valid_emails[] = $email;
                }
            }

            if(!empty($valid_emails)){
                $existingUsersQuery = User::whereIn('email', $valid_emails)->pluck('email')->toArray();
                $existing_users = $existingUsersQuery;

                $valid_emails = array_diff($valid_emails, $existing_users);
            }

            $invalid_emails = implode(', ', $invalid_emails);
            $existing_users = implode(', ', $existing_users);

            if (count($valid_emails) > 0) {
                //send registration invite
                // return count($valid_emails);
            }

            return response()->json([
                'valid_emails' => $valid_emails,
                'invalid_emails' => $invalid_emails,
                'existing_users' => $existing_users 
            ]);

        } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            // return response()->json(['message' => 'inside method', 'AuthUser' => $this->user]);
        }else {
            return response()->json(['message' => 'You do not have permission to access this method', 'user' => $this->user], 403);
        }
    }

    const CODE_VERIFICATION_DURATION_MINUTES = 3.5;
    const TOKEN_VERIFICATION_DURATION_MINUTES = 60;
    const TOKEN_PASSWORD_RESET_DURATION_MINUTES = 5;
}