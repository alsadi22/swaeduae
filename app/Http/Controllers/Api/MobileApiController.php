<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MobileApiController extends Controller
{
    // User login, returns token
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        // Create token
        $token = $user->createToken('mobile-token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    // User registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'volunteer',
        ]);
        $token = $user->createToken('mobile-token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    // Get upcoming events
    public function events(Request $request)
    {
        $events = Event::where('date', '>=', now())->orderBy('date')->get();
        return response()->json(['events' => $events]);
    }

    // Register volunteer for event
    public function registerForEvent(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $existing = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $request->event_id)
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Already registered'], 200);
        }
        EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $request->event_id,
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Registered successfully']);
    }

    // Get user certificates
    public function certificates(Request $request)
    {
        $user = $request->user();
        $certificates = Certificate::where('user_id', $user->id)->get();
        return response()->json(['certificates' => $certificates]);
    }

    // Get volunteer profile info
    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->json(['user' => $user]);
    }

    // Logout (revoke token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
