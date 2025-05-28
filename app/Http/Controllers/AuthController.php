<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;

class AuthController extends Controller
{
    public function login(){
        return view('login');
    }

    public function loginSubmit(Request $request){
        // Form validation

        $request->validate(
            
            // Rules 
            [
                'text_username' => 'required|email',
                'text_password' => 'required|min:6|max:16'
            ],
            // Messages
            [
                'text_username.required' => "O username é obrigatorio",
                'text_username.email' => "O username deve ser um email válido",
                'text_password.required' => "A senha é obrigatoria",
                'text_password.min' => "A senha deve conter pelo menos :min caracteres",
                'text_password.max' => "A senha não pode ultrapassar de :max caracteres",
            ]
        );

        // get user input

        $username = $request->input('text_username');
        $password = $request->input('text_password');
        
        try{
            DB::connection()->getPdo();
            echo "Connection is ok";
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function logout(){
        echo "logout";
    }
}
