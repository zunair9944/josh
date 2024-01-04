<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\Store;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use HasRoles; 

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show(Request $request, $id)
    {
        $user = User::with('store')->with('store')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $user, 'message' => 'User Found Successfully']);
    }

    public function store(Request $request)
    {
        // dd('here');
        try {
            $validatedData = $request->validate([
                'first_name' => 'string|max:255',
                'last_name' => 'string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            ]);

            $imagePath = null;
            if ($request->hasFile('avatar_url')) {
                $image = $request->file('avatar_url');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('images', $imageName, 'public');
                $imagePath = 'images/' . $imageName;
            }

            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'avatar_url' => $imagePath
            ]);

            // dd($user);

            $role = Role::where('name','store_owner')->firstOrFail();
            $user->assignRole($role);

            $request->validate([
                'store_name' => 'required|string|max:255',
                'shopify_store_public_key' => ['nullable','string'],
                'shopify_store_private_key' => ['nullable','string']
            ]);

            $store = new Store();
            $store->name = $request->input('store_name');
            $store->created_by = $user->id;
            $users = $store->users ?? [];
            $users[] = $user->id;
            $store->users = json_encode($users);
            $store->slug = Str::slug($request->input('store_name'),'-');

            if ($request->has('shopify_store_public_key') && $request->has('shopify_store_private_key')) {
                $store->shopify_store_public_key = $request->shopify_store_public_key;
                $store->shopify_store_private_key = $request->shopify_store_private_key;
            }

            $store->save();
            $user->store_id = $store->id;
            $user->save();

            $token = $user->createToken('api_token')->plainTextToken;

            // Send a welcome email to the user
            // Mail::to($user->email)->send(new WelcomeEmail($user));

            return response()->json([
                'user' => $user,
                'token' => $token,
                'store' => $store,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred.', 'error_message' => $e], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     // dd('here');
    //     try {
    //         // $validatedData = $request->validate([
    //         //     'first_name' => 'string|max:255',
    //         //     'last_name' => 'string|max:255',
    //         //     'username' => 'required|string|max:255',
    //         //     'email' => 'required|email|unique:users,email',
    //         //     'password' => 'required|string|min:8',
    //         //     'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
    //         // ]);

    //         $imagePath = null;
    //         if ($request->hasFile('avatar_url')) {
    //             $image = $request->file('avatar_url');
    //             $imageName = time() . '_' . $image->getClientOriginalName();
    //             $image->storeAs('images', $imageName, 'public');
    //             $imagePath = 'images/' . $imageName;
    //         }

    //         $user = User::create([
    //             'first_name' => $request->first_name,
    //             'last_name' => $request->last_name,
    //             'username' => $request->username,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //             'avatar_url' => $imagePath
    //         ]);

    //         // dd($user);

    //         $role = Role::where('name','store_owner')->firstOrFail();
    //         $user->assignRole($role);

    //         // $request->validate([
    //         //     'store_name' => 'required|string|max:255',
    //         //     'shopify_store_public_key' => ['nullable','string'],
    //         //     'shopify_store_private_key' => ['nullable','string']
    //         // ]);

    //         $store = new Store();
    //         $store->name = $request->input('store_name');
    //         $store->created_by = $user->id;
    //         $users = $store->users ?? [];
    //         $users[] = $user->id;
    //         $store->users = json_encode($users);
    //         $store->slug = Str::slug($request->input('store_name'),'-');

    //         if ($request->has('shopify_store_public_key') && $request->has('shopify_store_private_key')) {
    //             $store->shopify_store_public_key = $request->shopify_store_public_key;
    //             $store->shopify_store_private_key = $request->shopify_store_private_key;
    //         }

    //         $store->save();
    //         $user->store_id = $store->id;
    //         $user->save();

    //         $token = $user->createToken('api_token')->plainTextToken;

    //         // Send a welcome email to the user
    //         // Mail::to($user->email)->send(new WelcomeEmail($user));

    //         return response()->json([
    //             'user' => $user,
    //             'token' => $token,
    //             'store' => $store,
    //             'avatar_url' => asset('storage/' . $imagePath),
    //         ], 201);
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'An error occurred.', 'error_message' => $e], 500);
    //     }
    // }
    public function login(Request $request)
    { 
        try {

            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (Auth::attempt($credentials)) {

                $user = Auth::user();

                $store = Store::findOrFail($user->store_id);

                $token = $user->createToken('api_token')->plainTextToken;

                return response()->json([
                    'user' => $user,
                    'token' => $token,
                    'store' => $store
                ]);
            } else {
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

        } catch (ValidationException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'string',
            'last_name' => 'string',
        ]);
    
        try {
            $user = User::findOrFail($id);
            $user->fill($request->only(['first_name', 'last_name']));
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }
    
            $user->save();
    
            return response()->json($user, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function create_staff_user(Request $request)
    {
        $authenticatedUser = Auth::user();

        try {
            DB::beginTransaction();

            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role' => 'required|exists:roles,name',
            ]);

            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $username = strtolower($firstName . '-' . $lastName);
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = strtolower($firstName . '-' . $lastName . $counter);
                $counter++;
            }

            $user = new User();
            $user->username = $username;
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->store_id = $authenticatedUser->store_id;
            $user->role = $request->input('role');
            $role = Role::where('name', $request->input('role'))->firstOrFail();
            $user->assignRole($role);
            $user->save();

            $store = Store::find($authenticatedUser->store_id);
            $users = json_decode($store->users, true) ?? [];
            $users[] = $user->id;
            $store->users = json_encode($users);

            $store->save();

            DB::commit();

            return response()->json([
                'message' => 'Staff Member Created Successfully',
                'user' => $user,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create staff user.',
                'error' => $e
            ], 500);
        }

        
    }

    public function get_store_users(Request $request)
    {
        $authenticatedUser = $request->user();
        $all_store_users = User::with('bookings')->where('store_id', $authenticatedUser->store_id)->get();
        return response()->json(['success' => true, 'data' => $all_store_users, 'message' => 'All Store Users and Bookings']);



    }

    public function is_calendar_connected(Request $request)
    {
        $connectedCalendars = $request->user()->connectedCalendars;
        if (empty($connectedCalendars)) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
        
    }
}
