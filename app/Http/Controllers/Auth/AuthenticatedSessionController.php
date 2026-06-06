<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard', absolute: false))
                    ->with('success', 'Bienvenue ! Vous êtes maintenant connecté.');
            }

            return back()->withErrors([
                'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
            ])->onlyInput('email')
              ->with('error', 'Échec de la connexion. Veuillez vérifier vos identifiants.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la connexion : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la tentative de connexion.');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('info', 'Vous avez été déconnecté.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la déconnexion : ' . $e->getMessage());
            return redirect('/');
        }
    }
}
