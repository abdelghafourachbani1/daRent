<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:tenant,owner',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => $request->password,
            'telephone' => $request->telephone,
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'compte cree avec succes',
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'email' => $user->email,
                'role' => $user->role,
                'telephone' => $user->telephone,
                'avatar' => $user->avatar,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ],201);
    }

    public function login(Request $request): JsonResponse {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'email ou mot de passe incorrect',
            ],401);
        }
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json([
            'message'    => 'Connexion reussie',
            'user'       => [
                'id'        => $user->id,
                'nom'       => $user->nom,
                'email'     => $user->email,
                'role'      => $user->role,
                'telephone' => $user->telephone,
                'avatar'    => $user->avatar,
            ],
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request): JsonResponse {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id'        => $user->id,
                'nom'       => $user->nom,
                'email'     => $user->email,
                'role'      => $user->role,
                'telephone' => $user->telephone,
                'avatar'    => $user->avatar,
                'created_at'=> $user->created_at,
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse {
        $user = $request->user();

        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'telephone' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|string|min:8|confirmed',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nom','email','telephone']);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        // Check if avatar file is present
        file_put_contents('/tmp/avatar_debug.log', "All files: " . json_encode(array_keys($request->files->all())) . "\n", FILE_APPEND);
        file_put_contents('/tmp/avatar_debug.log', "All input: " . json_encode(array_keys($request->all())) . "\n", FILE_APPEND);
        
        if($request->hasFile('avatar')) {
            // Store the file
            $path = $request->file('avatar')->store('avatars','public');
            // Add to data array
            $data['avatar'] = $path;
            // Debug: write to file
            file_put_contents('/tmp/avatar_debug.log', "Avatar stored: $path, data array: " . json_encode($data) . "\n", FILE_APPEND);
        }

        // Debug: write all request data
        file_put_contents('/tmp/avatar_debug.log', "Has file: " . ($request->hasFile('avatar') ? 'yes' : 'no') . "\n", FILE_APPEND);
        file_put_contents('/tmp/avatar_debug.log', "Data array before update: " . json_encode($data) . "\n", FILE_APPEND);

        $updated = $user->updateProfile($data);

        file_put_contents('/tmp/avatar_debug.log', "Update result: " . ($updated ? 'true' : 'false') . "\n", FILE_APPEND);

        return response()->json([
            'message' => 'profile updated successfuly',
            'user'    => [
                'id'        => $user->fresh()->id, 
                'nom'       => $user->fresh()->nom,
                'email'     => $user->fresh()->email,
                'role'      => $user->fresh()->role,
                'telephone' => $user->fresh()->telephone,
                'avatar'    => $user->fresh()->avatar,
                'created_at'=> $user->fresh()->created_at,
            ],
        ]);
    }

    public function uploadAvatar(Request $request): JsonResponse {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars','public');
            $user->update(['avatar' => $path]);
        }

        return response()->json([
            'message' => 'avatar uploaded successfully',
            'user'    => [
                'id'        => $user->fresh()->id,
                'nom'       => $user->fresh()->nom,
                'email'     => $user->fresh()->email,
                'role'      => $user->fresh()->role,
                'telephone' => $user->fresh()->telephone,
                'avatar'    => $user->fresh()->avatar,
                'created_at'=> $user->fresh()->created_at,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'deconnexion reussite',
        ]);
    }
}
