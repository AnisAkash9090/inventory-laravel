<?php
namespace App\Http\Controllers;

use App\Models\Userlist;
use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller

{


    public function userindex(){
        $user=DB::table('userlist')->get();
        return view('userlist',compact('users'));
    }
    public function index()
    {
        // Fetch all users from the database
        $users = Userlist::all(); 
        // Return the view and pass the users data
      return view('userlist', compact('users'));
    }

 // Don't forget this!

public function getManagers()
{
    $managers = Userlist::where('type_manage', 'manager')
        ->select('idU', 'name', 'email')
        ->orderBy('name')
        ->get();

    return response()->json($managers);
}
public function store(Request $request)
{
    $sess_att = Auth::user()->manager_id;

    // 1. Validation
    $request->validate([
        'username'     => 'required|string|max:255',
        'address'      => 'required|string',
        'contact'      => 'required|string',
        'user_id'      => 'required|email|unique:userlists,email|unique:users,email',
        'pass'         => 'required|min:6',
        'img'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'type_manage'  => 'required|in:Manager,Sub_Manager',
        'manager_id'   => 'required_if:type_manage,Sub_Manager|nullable|exists:userlists,idU',
    ]);

    try {
        // 2. Handle File Upload
        $imageName = null;
        if ($request->hasFile('img')) {
            $imageName = time() . '.' . $request->img->extension();
            $request->img->move(public_path('images/user'), $imageName);
        }

        // 3. Save to Userlist Table
        $userlist = new Userlist();
        $userlist->name         = $request->username;
        $userlist->address      = $request->address;
        $userlist->contact      = $request->contact;
        $userlist->email        = $request->user_id;
        $userlist->password     = $request->pass;
        $userlist->createinfo   = $sess_att;
        $userlist->img          = $imageName;
        $userlist->type_manage  = $request->type_manage;
        $userlist->save();

        // 4. Resolve manager_id + manager name
        if ($request->type_manage === 'Manager') {
            $resolvedManagerId = $userlist->idU;
            $managerName        = $request->username;
        } else {
            $resolvedManagerId = $request->manager_id;
            $managerName        = optional(
                Userlist::find($request->manager_id)
            )->name ?? 'Unknown';
        }

        // 5. Save to User Table (for login)
        User::create([
            'name'       => $request->username,
            'email'      => $request->user_id,
            'password'   => Hash::make($request->pass),
            'manager_id' => $resolvedManagerId,
        ]);

        // 6. Success
        return redirect()->back()->with('success_message',
            "User \"{$request->username}\" created successfully as " .
            ($request->type_manage === 'Manager' ? 'Manager' : "Sub Manager under \"{$managerName}\"") . "."
        );

    } catch (\Exception $e) {
        // 7. Failure
        return redirect()->back()->with('error_message',
            'Failed to create user. Please try again. (' . $e->getMessage() . ')'
        );
    }
}
}