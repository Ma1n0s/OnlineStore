<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('profile');
        
        return response()->json([
            'profile' => $this->formatProfileData($user)
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:20|unique:users,phone,'.$user->id,
        ]);

        $user->update([
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $profileData = [
            'last_name' => $validated['lastname'],
            'first_name' => $validated['firstname'],
            'patronymic' => $validated['middlename'],
        ];

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        return response()->json([
            'message' => 'Профиль успешно обновлен',
            'profile' => $this->formatProfileData($user->fresh()->load('profile'))
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
            'message' => 'Данные компании успешно обновлены',
            'profile' => $this->formatProfileData($user->fresh()->load('profile'))
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Пароль успешно изменен']);
    }

    private function formatProfileData(User $user)
    {
        $profile = $user->profile;
        
        return [
            'lastname' => $profile->last_name ?? null,
            'firstname' => $profile->first_name ?? null,
            'middlename' => $profile->patronymic ?? null,
            'company' => $profile->company_name ?? null,
            'companyDetails' => $profile ? [
                'name' => $profile->company_name,
                'inn' => $profile->inn,
                'kpp' => $profile->kpp,
                'address' => $profile->legal_address,
                'director' => $profile->director,
                'phone' => $profile->company_phone,
                'email' => $profile->company_email,
            ] : null,
            'email' => $user->email,
            'phone' => $user->phone,
        ];
    }
}