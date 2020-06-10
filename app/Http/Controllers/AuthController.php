<?php

namespace App\Http\Controllers;

use App\User;
use App\UserSalon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {


        $credentials = $request->only('email', 'password');
        $user = User::where('email',$request->email)->get()[0];

        if ($token = $this->guard()->attempt($credentials)) {
        return compact('token','user');
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {

        if ($request->type == 'hair stylist') {
            $rules = [
                'name' => 'required',
                'email' => 'unique:users|required',
                'phone' => 'unique:users|required',
                'password' => 'required',
                'type' => 'required',
                'salonName' => 'required',
                'salonPhone' => 'required',
                'salonAddress' => 'required',
                'salonServices' => 'required',
                'salonTiming' => 'required'
            ];

            $input = $request->only(
                'name',
                'email',
                'password',
                'phone',
                'type',
                'salonName',
                'salonPhone',
                'salonAddress',
                'salonServices',
                'salonTiming');
        } else {
            $rules = [
                'name' => 'required',
                'email' => 'unique:users|required',
                'phone' => 'unique:users|required',
                'password' => 'required',
                'type' => 'required'
            ];

            $input = $request->only(
                'name',
                'email',
                'password',
                'phone',
                'type');
        }
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        } else {
            if ($request->file('profilePic')) {
                $imageName = time() . '.' . $request->file('profilePic')->getClientOriginalExtension();
                $request->file('profilePic')->move(public_path('images/users'), $imageName);
            } else {
                $imageName = "no-image.png";
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'profilePic' => $imageName,
                'type' => $request->type,
                'about' => $request->about
            ]);
            if ($request->type == "hair stylist") {
                $userSalon = UserSalon::create([
                    'user_id' => $user->id,
                    'name' => $request->salonName,
                    'phone' => $request->salonPhone,
                    'address' => $request->salonAddress,
                    'services' => $request->salonServices,
                    'timing' => $request->salonTiming,
                    'achievements' => $request->salonAchievements
                ]);
            }

            $token = auth()->login($user);

            return $this->respondWithToken($token);
        }
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = $this->guard()->user();
        $userSalon = UserSalon::where('user_id', $user->id)->first();

        return response()->json(array([
            "user" => $user,
            "userSalon" => $userSalon
        ]));
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
    
    public function getHairStylist()
    {
        $hairStylists = User::where('type',"hair stylist")->get();

        // dd($hairStylists);
        return response()->json($hairStylists);
    }
}
