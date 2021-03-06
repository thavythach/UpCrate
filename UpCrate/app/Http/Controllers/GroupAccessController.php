<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GroupAccess;
use App\GroupMembers;
use App\User;
use App\GroupFile;
use App\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!\Auth::check()){
            return view('auth.register');
        }
        
        // $data['groups'] = GroupAccess::where('user_id', '=', (int) (\Auth::id()))->get();
        $data['groups'] = GroupMembers
            ::where('group_members.user_id', '=', (int) (\Auth::id()))
            ->join('groupAccess', 'groupAccess.group_id', '=', 'group_members.group_id')
            ->get();
       
        // $data['groups'] = GroupAccess::all(); // currently it has my id as a string in the database lmao.
        $data['users'] = User::all();
        $data['count'] = $data['groups']->count();
        return view('groups.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!\Auth::check()){
            return view('auth.register');
        }

        // validation start 
        $input = $request->all();
        $input['GroupFile'] = $request->file('GroupFile');

        $rules = [];
        $rules['group_name'] = 'required|string|max:16';
        $rules['GroupFile'] = 'required|max:2048'; // TODO: add a new file creation

        if (in_array("item_id", $input)){
            foreach($input['item_id'] as $key => $val){
                $rules[$key] = 'exists:users.name';
            }
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $notification = array(
              'message' => $validator->messages()->first(),
              'alert-type' => 'error'
            );
          return back()->with($notification);
        }
        // validation end 

        \DB::beginTransaction();

        $tmp = new GroupAccess;
        $tmp->name = $request->group_name;
        $tmp->user_id = \Auth::id();
        $tmp->save();

        $gmList = $request->item_id;

    
        // if list is countable go through and add file.
        $gmListString = "";
        if ($gmList){
            for ($i=0; $i < count($gmList); $i++){
            
                $gm = new GroupMembers;
                if ($gmList[$i] != "None"){
                    $gm->user_id = $gmList[$i];
                    $gm->group_id = $tmp->group_id; 
                    $gm->save();
                    
                    // create shared string
                    $gmTmp = User::where('id', '=', $gmList[$i])->select('name')->first();
                    $gmListString = $gmListString . $gmTmp['name'];
                    $gmListString = $gmListString . ", ";
                } 
                    
            }
            
            $gm = new GroupMembers;
            $gm->user_id = \Auth::id();
            $gm->group_id = $tmp->group_id; 
            $gm->save();

            // create shared string
            $gmListString = $gmListString . \Auth::user()->name;

        } else {
            $gm = new GroupMembers;
            $gm->user_id = \Auth::id();
            $gm->group_id = $tmp->group_id; 
            $gm->save();
            $gmListString = \Auth::user()->name;
        }

        // generate a new filename. getClientOriginalExtension() for the file extension
        $new_file = new File;
        $new_file->name = $input['GroupFile']->getClientOriginalName();
        $new_file->file_path = $input['GroupFile']->storeAs('files', $new_file->name . time());
        $new_file->visibility = 0; // groupFiles are always private

        $new_file->save(); // persist

        // haha generate new groupfile 
        $grpFile = new GroupFile;
        $grpFile->group_id = $tmp->group_id;
        $grpFile->file_id = $new_file->id; 
        $grpFile->save(); // persist 

        \DB::commit();



         $notification = array(
            'message' => "Successfully created new group called " . $tmp->name . ". Members: " . $gmListString,
            'alert-type' => 'success'
        );
        return back()->with($notification);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $data['members'] = GroupMembers
        ::where('group_id', '=', $id)
        ->join('users', 'users.id', '=', 'group_members.user_id')
        ->select('group_members.group_id', 'users.id', 'users.email', 'users.name')
        ->get();

        $data['grpName'] = GroupAccess::where('group_id', '=', $id)->select('name')->get();

        $data['files'] = GroupFile
            ::where('group_id','=', $id)
            ->join('file', 'file.id', '=', 'groupFile.file_id')
            ->distinct('file.id')
            ->select('file_path', 'name', 'groupFile.file_id')
            ->get();

        return view('groups.show')->with('data', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
