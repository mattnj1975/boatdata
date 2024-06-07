<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBoats;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;
class ManageUserController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role_as == 1) {
            $data = User::where('role_as', 3)
                ->latest()
                ->get();
        } else {
            $data = User::where('created_by', Auth::id())
                ->where('role_as', 3)
                ->latest()
                ->get();
        }

        if ($request->ajax()) {

            return DataTables::of($data)
                ->addColumn('status', function ($data) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($data->active == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('actions', function ($data) {

                    $actions = '<div class="d-flex btn-group-sm" role="group" aria-label="Basic example">
                                <div style="margin-right:10px;">
                            <a href="' . route('users.edit', $data->id) . '" class="btn btn-outline-primary btn-sm"><i class="fa fa-edit"></i>Edit</a>
                            </div>
                            <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-danger btn-sm deleteUser"> <i class="fa fa-trash"></i> Delete</a> </div>
                            </div>';
                    return $actions;
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
        return view('admin.users.index');
    }
    public function create()
    {
        return view('admin.users.edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',

        ]);
        if ($request->has('status') && $request->status == 'on') {
            $request['status'] = 1;
        } else {
            $request['status'] = 0;
        }
        $user = new User();
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->role_as = 3;
        $user->created_by = Auth::id();
        $user->active = $request->status;
        if (isset($request->password)) {
            $user->password = Hash::make($request['password']);
        }
        $user->save();


        return redirect()->route('users.index')->with('success', 'User Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users,email,' . $id,
        ]);
        if (isset($request->password)) {
            $request->validate([
                'password' => 'required|min:6',
            ]);
        }
        if ($request->has('status') && $request->status == 'on') {
            $request['status'] = 1;
        } else {
            $request['status'] = 0;
        }

        $user = User::find($id);
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->role_as = 3;
        $user->active = $request->status;
        if (isset($request->password)) {
            $user->password = Hash::make($request['password']);
        }
        $user->save();
        return redirect()->route('users.index')->with('success', 'User Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        UserBoats::where('user_id', $id)->delete();
        User::find($id)->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}
