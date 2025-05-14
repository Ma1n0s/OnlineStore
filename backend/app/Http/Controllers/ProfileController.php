<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\UserProfile;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update($validated);
        
        // $profileData = [
        //     'first_name' => $validated['name'], 
            
        // ];
        
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }
        
        return response()->json([
            'user' => $user->load('profile'),
            'message' => 'Профиль успешно обновлен'
        ]);
    }

    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'required|string|max:20',
            'kpp' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'director' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]);
        
        $profileData = [
            'company_name' => $validated['name'],
            'inn' => $validated['inn'],
            'kpp' => $validated['kpp'],
            'legal_address' => $validated['address'],
            'director' => $validated['director'],
            'company_phone' => $validated['phone'],
            'company_email' => $validated['email'],
        ];
        
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }
        
        return response()->json([
            'user' => $user->load('profile'),
            'message' => 'Данные компании успешно обновлены'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', Password::min(8)],
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Текущий пароль неверный'
            ], 422);
        }
        
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return response()->json([
            'message' => 'Пароль успешно изменен'
        ]);
    }

    public function show()
    {
        $user = Auth::user()->load('profile');
        
        return response()->json([
            'user' => $user,
            'companyDetails' => $user->profile ? [
                'name' => $user->profile->company_name,
                'inn' => $user->profile->inn,
                'kpp' => $user->profile->kpp,
                'address' => $user->profile->legal_address,
                'director' => $user->profile->director,
                'phone' => $user->profile->company_phone,
                'email' => $user->profile->company_email,
            ] : null
        ]);
    }
}