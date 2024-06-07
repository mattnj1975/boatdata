<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminBoats;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
class ManageMastersController extends Controller
{
    public function index(Request $request)
    {
        $data = User::where('role_as', 2)->latest()->get();                       
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
                            <a href="' . route('masters.edit', $data->id) . '" class="btn btn-outline-primary btn-sm"><i class="fa fa-edit"></i>Edit</a>
                            </div>
                            <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-danger btn-sm deleteMaster"> <i class="fa fa-trash"></i> Delete</a> </div>
                            </div>';
                    return $actions;
                })
                ->rawColumns(['status','actions'])
                ->make(true);
        }
        return view('admin.masters.index');
    }
    public function create()
    {
        return view('admin.masters.edit');
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
        $user->role_as = 2;
        $user->active = $request->status;
        if (isset($request->password)) {
            $user->password = Hash::make($request['password']);
        }
        $user->save();

        return redirect()->route('masters.index')->with('success', 'Master User Added Successfully');
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
        $master = User::find($id);
        return view('admin.masters.edit', compact('master'));
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
        $user->active = $request->status;
        if (isset($request->password)) {
            $user->password = Hash::make($request['password']);
        }
        $user->save();
        return redirect()->route('masters.index')->with('success', 'Master User Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        AdminBoats::where('user_id', $id)->delete();
        User::find($id)->delete();
        return response()->json(['success' => 'Master User deleted successfully.']);
    }
}
