<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginSubmit(Request $request)
    {
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

        // Check users exists

        $user = User::where('username', $username)
            ->where('deleted_at', NULL)
            ->first();

        // if (!$user) {
        //     return redirect()
        //         ->back() // <- Volta a página anterior
        //         ->withInput() // <- Mantem as informações do formulario
        //         ->with('loginError', 'Username ou password incorretos'); // <- Envia um erro ao usuário
        // }

        // Check if password is correct

        if (!password_verify($password, $user->password)) {
            return redirect()
                ->back() // <- Volta a página anterior
                ->withInput() // <- Mantem as informações do formulario
                ->with('loginError', 'Username ou password incorretos'); // <- Envia um erro ao usuário
        }

        // update last login

        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // login user

        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);

        // Redirect to home
        return redirect()->to('/');
    }

    public function logout()
    {
        // Logou from the application
        session()->forget('user');
        return redirect()
            ->to('/login');
    }
}
