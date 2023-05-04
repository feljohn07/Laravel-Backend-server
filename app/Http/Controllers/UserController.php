<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function login(Request $request)
    {

        $data = $request->all();

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $access_token = $data['access_token'] ?? null;

        try{

            if($access_token == null || $access_token == "") {

                $validator = Validator::make($request->all(), [
                    'email' => 'required|string|email|max:255',
                    'password' => 'required|string|min:6',
                ]);
                if ($validator->fails())
                {
                    return response(['errors'=>$validator->errors()->all()], 422);
                }

                $user = User::where('email', $request->email)->first();
                if ($user) {
                    if (Hash::check($request->password, $user->password)) {
                        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                        $response = [
                            'token' => $token,
                            'email' => $user->email,
                            'firstname' => $user->firstname,
                            'lastname' => $user->lastname,
                        ];

                        return response($response, 200);
                    } else {
                        $response = ["error" => "Wrong Password, Please try again"];
                        return response($response, 422);
                    }
                } else {
                    $response = ["error" =>'Email does not exist, Please register first.'];
                    return response($response, 422);
                }

            }else{

                // Google OAuth2.0
                $client = new \GuzzleHttp\Client();
                $response = $client->request(
                    'GET',
                    "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $data['access_token'],
                );

                // Google OAuth2.0 response
                $Oath_response = json_decode($response->getBody()->getContents());

                // Check if user exists
                $exist = User::where('email', $Oath_response->email)->first();
                if(!$exist){
                    return response()->json(["error"=>"Email does not exist, Please register first."], 400);
                }
                return response()->json([
                    "action"=>"Google OAuth2.0",
                    "action"=>"normal login",
                    "email"=>$exist->email,
                    "firstname"=>$exist->firstname,
                    "lastname"=>$exist->lastname,
                    "picture"=>$Oath_response->picture,
                    "token"=>"token",
                ], 200);

            }

        }catch (Exception  $e) {
            return response()->json(["error"=>"Something went wrong, " . $e], 400);
        }

    }

    public function register(Request $request)
    {
        $data = $request->all();
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $access_token = $data['access_token'] ?? null;

        // return response()->json($request->toArray(), 200);

        try{

            if($access_token === null) {
                $validator = Validator::make($request->all(), [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6',
                ]);

                if ($validator->fails())
                {
                    return response(['error'=>$validator->errors()->all()], 422);
                }

                $request['password']=Hash::make($request['password']);
                $user = User::create([
                    'firstname'=> $firstname,
                    'lastname'=> $lastname,
                    'email'=> $email,
                    'password'=> Hash::make($password),
                    'api_token'=> Str::random(60)
                ]);

                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = [
                    'token' => $user->api_token,
                    'email' => $user->email,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                ];
                return response()->json($response, 200);

            } else {

                // Google OAuth2.0
                $client = new \GuzzleHttp\Client();
                $response = $client->request(
                    'GET',
                    "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $data['access_token'],
                );

                // Google OAuth2.0 response
                $Oath_response = json_decode($response->getBody()->getContents());

                // return response()->json(["thisssss", $Oath_response->email], 200);

                // Check if user exists
                $exist = User::where('email', $Oath_response->email)->first();
                if($exist){
                    return response()->json(["error"=>"Email already exists, Please login."], 400);
                }else{
                    // register and login
                    $user = new User();
                    $user->password = Hash::make("loefeljohn2001");
                    $user->email = $Oath_response->email;
                    $user->firstname = $Oath_response->given_name;
                    $user->lastname = $Oath_response->family_name;
                    $user->api_token = Str::random(60);
                    $user->save();

                    return response()->json([
                        "action"=>"Register with Google OAuth2.0",
                        "action"=>"normal login",
                        "email"=>$user->email,
                        "firstname"=>$user->firstname,
                        "lastname"=>$user->lastname,
                        "picture"=>$Oath_response->picture,
                        "token"=>$user->api_token,
                    ], 200);
                }
            }

        }catch (Exception  $e) {
            return response()->json(["error"=>"Something went wrong, " . $e], 400);
        }

    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message'=>'Successfully logged out']);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');

        $user->save();

        return response()->json($user);
    }

}
