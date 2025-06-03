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
            'inn' => ['required', 'string', 'max:20', function ($attribute, $value, $fail) {
                if (!preg_match('/^\d{10,12}$/', $value)) {
                    $fail('ИНН должен содержать 10 или 12 цифр');
                }
            }],
            'kpp' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'director' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        $profileData = [
            'company_name' => $validated['name'],
            'inn' => $validated['inn'],
            'kpp' => $validated['kpp'] ?? null,
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
            'new_password' => ['required', 'string', Password::min(6)],
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

    public function deleteCompany(Request $request)
    {
        $user = Auth::user();
        
        if ($user->profile) {
            $user->profile->delete();
            return response()->json([
                'message' => 'Данные компании успешно удалены'
            ]);
        }
        
        return response()->json([
            'message' => 'Нет данных компании для удаления'
        ], 404);
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