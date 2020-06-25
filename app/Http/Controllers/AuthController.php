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
        $this->middleware('auth:api', ['except' => ['login', 'register','registerAsHairstylist']]);
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
        $validateData = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // $userEmail = User::where('email',$request->email)->first();

        // if(!$userEmail)
        // {
        //     return response()->json(['error' => 'User doesn\'t exist'],401  );
        // }
        // $credentials = $request->only('email', 'password');
        $user = User::where('email',$request->email)->first();

        if(!$user)
        {
            return response()->json(['error' => 'User doesn\'t exist'], 401);
        }
        $userSalon = null;

        if ($token = $this->guard()->attempt($validateData)) {
            $user->setAttribute('token', $token);
            if($user->type_id == '2'){

              $userSalon =  UserSalon::where('user_id',$user->id)->get()[0];
              $user->setAttribute('userSalon', $userSalon);

            }
            
         return $user;
        }

        return response()->json(['error' => 'Incorrect password'],401);
    }

    public function register(Request $request)
    {

        if ($request->type_id == '2') {
            $rules = [
                'name' => 'required',
                'email' => 'unique:users|required',
                'phone' => 'required',
                'image' => 'required|image:png,jpg,jpeg',
                'password' => 'required',
                'about' => 'required',
                'type_id' => 'required',
                'salon_name' => 'required',
                'salon_phone' => 'required',
                'salon_address' => 'required',
                'salon_services' => 'required',
                'salon_timing' => 'required',
                'salon_achievements' => 'required'
            ];

            $input = $request->only(
                'name',
                'email',
                'password',
                'image',
                'phone',
                'type_id',
                'salon_name',
                'salon_phone',
                'about',
                'salon_address',
                'salon_services',
                'salon_timing',
                'salon_achievements'
            );
        }
        else {
            $rules = [
                'name' => 'required',
                'email' => 'unique:users|required',
                'phone' => 'required',
                'password' => 'required',
                'type_id' => 'required'
            ];

            $input = $request->only(
                'name',
                'email',
                'password',
                'phone',
                'type_id'
            );
        }
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()],409);
        }
        
            // if ($request->file('profile_pic')) {
            //     $imageName = time() . '.' . $request->file('profile_pic')->getClientOriginalExtension();
            //     $request->file('profile_pic')->move(public_path('images/users'), $imageName);
            // } else {
            //     $imageName = "no-image.png";
            // }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'type_id' => $request->type_id,
            ]);

            if ($request->type_id == "2") {
                
                $imagename = 'images/' + time(). '.' .$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(\public_path('images'),$imagename);

                $userSalon = UserSalon::create([
                    'user_id' => $user->id,
                    'name' => $request->salon_name,
                    'phone' => $request->salon_phone,
                    'about' => $request->about,
                    'image' => $imagename,
                    'address' => $request->salon_address,
                    'services' => $request->salon_services,
                    'timing' => $request->salon_timing,
                    'achievements' => $request->salon_achievements
                ]);
            }

            // $token = auth()->login($user);
             return $this->login($request);

            // return $this->respondWithToken($token);
    
    }

    public function registerAsHairstylist(Request $request){

        $rules = [
            'user_id' => 'required',
            'salon_name' => 'required',
            'salon_phone' => 'required',
            'salon_address' => 'required',
            'salon_services' => 'required',
            'image' => 'required|image:png,jpg,jpeg',
            'about' => 'required',
            'salon_timing' => 'required',
            'salon_achievements' => 'required'

        ];

        $input = $request->only(
            'user_id',
            'about',
            'salon_name',
            'image',
            'salon_phone',
            'salon_address',
            'salon_services',
            'salon_timing',
            'salon_achievements'
        );
        

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        }

        $imagename = time(). '.' .$request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(\public_path('images'),$imagename);
        
        $user = User::where('id',$request->user_id)->update(['type_id'=>'2']);
        $userSalon = UserSalon::create([
            'user_id' => $request->user_id,
            'name' => $request->salon_name,
            'about' => $request->about,
            'image' => $imagename,
            'phone' => $request->salon_phone,
            'address' => $request->salon_address,
            'services' => $request->salon_services,
            'timing' => $request->salon_timing,
            'achievements' => $request->salon_achievements
        ]);
        return $userSalon;
        // $usertype = $user->type;
        // $user->type = 'Hair Stylist';        
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
