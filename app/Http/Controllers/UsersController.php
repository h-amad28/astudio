<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * @api {post} /register 
     * @apiVersion 0.1.0
     * @apiName Register
     * @apiGroup User
     *
     * @apiDescription This API is used to register user.
     *
     * @apiParam {String} first_name user's first name
     * @apiParam {String} last_name user's last name
     * @apiParam {String} email user's email
     * @apiParam {String} password user's password
     * 

     * @apiSuccess {Object[]} projects list of prjects
     * @apiSuccess {Number} projects.id id of the project
     * @apiSuccess {String} projects.name name of the project
     * @apiSuccess {String} projects.status status of the project
     * @apiSuccess {Object[]} projects.attributes list of attributes attached to project
     * @apiSuccess {String} projects.attributes.name attribute's name
     * @apiSuccess {String} projects.attributes.type attribute's type
     * @apiSuccess {String} projects.attributes.value attribute value's stored against project
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create(
            array_merge(
                $request->all(),
                ['password' => bcrypt($request->get('password'))]
            )
        );

        $token = $user->createToken('UserToken')->accessToken;
        
        return response()->json([
            'user' => $user->toArray(),
            'token' => $token
        ],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Users $users)
    {
        //
    }

    /**
     * @api {post} /login 
     * @apiVersion 0.1.0
     * @apiName Login
     * @apiGroup User
     *
     * @apiDescription This API is used to login user.
     *
     * @apiParam {String} email user's email
     * @apiParam {String} password user's password
     * 

     * @apiSuccess {Object[]} user user data
     * @apiSuccess {Number} user.id id of the project
     * @apiSuccess {String} user.first_name first name
     * @apiSuccess {String} user.last_name last name
     * @apiSuccess {String} user.email email address
     * @apiSuccess {String} token email address
     *
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $creds = $request->only(['email', 'password']);

        if (auth()->attempt($creds)) {
            $user = auth()->user();
            $token = $user->createToken('UserToken')->accessToken;
            return response()->json([
                'user' => $user->toArray(),
                'token' => $token
            ], 200);
        }
        
        return response()->json([
            'error' => 'Unauthorized. Invalid email OR password',
        ], 401);
    }

    /**
     * @api {get} /logout 
     * @apiVersion 0.1.0
     * @apiName Logout
     * @apiGroup User
     *
     * @apiDescription This API is used to logout user.
     *
     * 
     * @apiSuccess {Boolean} success true
     *
     */
    public function logout(Request $request)
    {
        if (!empty(auth()->user()->id)) {
            auth()->user()->token()->revoke();
            return response()->json([ 'success' => true],200);
        }
        return response()->json([ 'success' => false], 422);
    }
}
