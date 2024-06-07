<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBoats;
use Auth;
class UserBoatController extends Controller
{
    public function allUserBoats(Request $request)
    {
        if ($request->ajax()) {

            $query = UserBoats::with('boat', 'user', 'assigneeUser')->latest();
            $totalRecords = UserBoats::count();

            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)->take($length)->get();
            $draw = $request->get('draw');

            foreach ($data as $item) {
                $item->boatname = $item->boat->boatname ?? '';
                $item->mac = $item->boat->mac ?? '';
                $item->assignee_user_name = $item->assigneeUser->name ?? '';
                $item->user_name = $item->user->name ?? '';
                
                $item->actions = '<div class="d-flex btn-group-lg" role="group" aria-label="Basic example">
                                    <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-danger btn-sm deleteUserBoat"> <i class="fa fa-trash"></i> Delete</a> </div>
                                </div>';
        
            }

            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }
        return view('admin.user_boat.index');
    }
    public function MasterAllUserBoats(Request $request)
    {
        if ($request->ajax()) {

            $query = UserBoats::where('assignee_user_id', Auth::id())->with('boat', 'user', 'assigneeUser')->latest();
            $totalRecords = UserBoats::where('assignee_user_id', Auth::id())->count();

            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)->take($length)->get();
            $draw = $request->get('draw');

            foreach ($data as $item) {
                $item->boatname = $item->boat->boatname ?? '';
                $item->mac = $item->boat->mac ?? '';
                $item->assignee_user_name = $item->assigneeUser->name ?? '';
                $item->user_name = $item->user->name ?? '';
                
                $item->actions = '<div class="d-flex btn-group-lg" role="group" aria-label="Basic example">
                                    <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-danger btn-sm deleteUserBoat"> <i class="fa fa-trash"></i> Delete</a> </div>
                                </div>';
        
            }

            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }
        return view('master.user_boat.index');
    }
    public function userBoats(Request $request)
    {
        if ($request->ajax()) {

            $query = UserBoats::where('user_id', Auth::id())->with('boat', 'user', 'assigneeUser')->latest();
            $totalRecords = UserBoats::where('user_id', Auth::id())->count();

            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)->take($length)->get();
            $draw = $request->get('draw');

            foreach ($data as $item) {
                $item->boatname = $item->boat->boatname;
                $item->mac = $item->boat->mac;
                $item->lastseen = $item->boat->lastseen;
                $item->assignee_user_name = $item->assigneeUser->name;
                $item->user_name = $item->user->name;
                
                $item->actions = '<div class="d-flex btn-group-lg" role="group" aria-label="Basic example">
                                    <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->boat->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm addNoteInBoat"> <i class="fa fa-edit"></i> Add Note</a> </div>
                                    <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->boat->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm addFileInBoat"> <i class="fa fa-file"></i> Add File</a> </div>
                                    <div style="margin-right:10px"> <a href="' . route('view_boat', $item->boat->id) . '" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm "> <i class="fa fa-eye"></i> View Notes and Files</a> </div>
                                </div>';
        
            }

            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }
        return view('user.boat.index');
    }
    public function delUserBoat($id)
    {
        UserBoats::find($id)->delete();
        return response()->json(['success' => 'User Boat deleted successfully.']);
    }
    public function MasterDelUserBoat($id)
    {
        UserBoats::find($id)->delete();
        return response()->json(['success' => 'User Boat deleted successfully.']);
    }
    public function delBoat($id)
    {
        UserBoats::find($id)->delete();
        return response()->json(['success' => 'Boat deleted successfully.']);
    }
}
