<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Company;

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
            'user' => $user->load('companies'),
            'message' => 'Профиль успешно обновлен'
        ]);
    }

    public function getCompanies(Request $request)
    {
        return response()->json($request->user()->companies);
    }

    public function addCompany(Request $request)
    {
        $user = $request->user();
        
        if ($user->companies()->count() >= 3) {
            return response()->json([
                'message' => 'Максимальное количество компаний - 3'
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inn' => ['required', 'string', 'digits_between:10,12', 'unique:companies,inn'],
            'kpp' => 'nullable|string|digits:9',
            'address' => 'required|string|max:500',
            'director' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_main' => 'sometimes|boolean'
        ]);
        
        $isMain = $user->companies()->count() === 0 || ($validated['is_main'] ?? false);
        
        if ($isMain) {
            $user->companies()->update(['is_main' => false]);
        }

        $company = $user->companies()->create(array_merge($validated, [
            'is_main' => $isMain
        ]));
        
        return response()->json([
            'company' => $company,
            'message' => 'Компания успешно добавлена'
        ], 201);
    }

    public function updateCompany(Request $request, Company $company)
    {
        if ($request->user()->id !== $company->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inn' => ['required', 'string', 'digits_between:10,12', 'unique:companies,inn,'.$company->id],
            'kpp' => 'nullable|string|digits:9',
            'address' => 'required|string|max:500',
            'director' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_main' => 'sometimes|boolean'
        ]);
        
        if ($validated['is_main'] ?? false) {
            $request->user()->companies()->where('id', '!=', $company->id)->update(['is_main' => false]);
        }

        $company->update($validated);
        
        return response()->json([
            'company' => $company,
            'message' => 'Данные компании успешно обновлены'
        ]);
    }

    public function deleteCompany(Request $request, Company $company)
    {
        if ($request->user()->id !== $company->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $wasMain = $company->is_main;
        $company->delete();

        if ($wasMain && $request->user()->companies()->count() > 0) {
            $request->user()->companies()->first()->update(['is_main' => true]);
        }
        
        return response()->json([
            'message' => 'Компания успешно удалена'
        ]);
    }

    public function setMainCompany(Request $request, Company $company)
    {
        if ($request->user()->id !== $company->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->user()->companies()->update(['is_main' => false]);
        $company->update(['is_main' => true]);
        
        return response()->json([
            'company' => $company,
            'message' => 'Основная компания успешно изменена'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'confirmed', Password::min(6)],
        ]);
        
        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return response()->json([
            'message' => 'Пароль успешно изменен'
        ]);
    }

    public function show()
    {
        $user = Auth::user()->load('companies');
        
        return response()->json(
            $user->companies,
        );
    }
}