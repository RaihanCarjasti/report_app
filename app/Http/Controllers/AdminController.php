<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Document;


class AdminController extends Controller
{
    public function profile(Request $request){
        if($request->method() == "POST"){
            $validate = $request->validate([
                "oldpassword" => "min:8",
                "newpassword" => "min:8|different:password",
            ]);
            if($validate){
                $data = $request->except(["_token"]);
                $user = User::find(Auth::id());

                if (Hash::check($data["oldpassword"], $user->password)) { 
                    $user->fill([
                     "password" => Hash::make($data["newpassword"])
                     ])->save();
                 
                     return redirect()->route("adminIndex");
                 }else{
                    return redirect()->route("logout");
                 }
                
            }
        }
        return view("profile");
    }

    function index(){
        $unreview_document = Document::where("comment",null)->where("passed",false)->count();
        $passed_document = Document::whereNotNull("comment")->where("passed",true)->count();
        $revised_document = Document::whereNotNull("comment")->where("passed","=",0)->count();
        return view("admin.index",["admin" => User::count(),"unreview_document" => $unreview_document,"passed_document" => $passed_document,"revised_document" => $revised_document]);
    }

    function admin(){
        $users = User::query()->where("role","!=","admin")->get();

        return view("admin.admin",["title" => "Daftar petugas","users" => $users]);
    }

    function inputAdmin(Request $request){
        
        if($request->method() == "POST"){
            $data = $request->except(["_token"]);
            $validate = $request->validate([
                "username" => "unique:users",
            ]);

            if($validate){
                User::create($data);
                return redirect()->route("admin");
            }
        }

        return view("admin.inputAdmin",["title" => "Input admin"]);
    }

    function editAdmin(Request $request){
        $user = User::find($request->query("id"));
        if($request->method() == "POST"){
            $data = $request->except(["_token"]);
            
            $validate = $request->validate([
                "username" => "unique:users,username".$user->id,
            ]);

            if($validate){
                $user->update($data);
                return redirect()->route("admin");
            }
        }

        return view("admin.inputAdmin",["title" => "Edit admin","user" => $user]);
    }

    function deleteAdmin(Request $request){
        $id = $request->get("id",1);
        $user = User::find($id);

        if($user == null){
            abort(404);
        }

        User::destroy($id);
        return redirect()->route("admin");
        
    }      

    // public function task(){
    //     $task = Task::all();
    //     return view("admin.task",["tasks" => $task]);
    // }
    
    // public function inputTask(Request $request){
    //     if($request->method()  == "POST"){
    //         $data = $request->except(["_token"]);
    //         Task::create($data);
    //         return redirect()->route("task");
    //     }
    //     return view("admin.inputTask",["title" => "Tambah tugas"]);
    // }
    
    // public function editTask(Request $request){
    //     $id = $request->get("id");
    //     $task = Task::find($id);

    //     if($task == null){
    //         abort(404);
    //     }

    //     if($request->method() == "POST"){
    //         $data = $request->except(["_token"]);
    //         $task->update($data);
    //         return redirect()->route("task");
    //     }
    //     return view("admin.inputTask",["title" => "Edit tugas","task" => $task]);
    // }

    // public function deleteTask(Request $request){
    //     $id = $request->get("id");
    //     $task = Task::find($id);
    //     if($task == null){
    //         abort(404);
    //     }

    //     Task::destroy($id);
    //     return redirect()->route("task");        
    // }

    // public function criteria(Request $request){
    //     $id = $request->get("task");
    //     $task = Task::find($id);
    //     if($task == null){
    //         abort(404);
    //     }

    //     return view("admin.criteria",["title" => "Kelengkapan dokumen","task" => $task]);
    // }

    public function typeCriteria(string $criteria){

        $documents = Document::where("type",$criteria)->get();
        
        return view("admin.typeCriteria",["title" => $criteria,"documents" => $documents]);
    }

    public function inputDocument(string $criteria,Request $request){        
        if($request->method() == "POST"){        
            // Handle file upload
            $filePath = "null";
            if ($request->hasFile("document")) {
                $file = $request->file("document");
                $fileName = time() . "_" . $file->getClientOriginalName(); 
                $filePath = $file->storeAs("documents", $fileName, "public"); 
            }
                        
            Document::create([
                "name" => $request->input("name"),
                "file_url" => $request->input("file_url"),
                "type" => $criteria,
                "path" => $filePath ?? null,
            ]);

            
            return redirect()->route("typeCriteria",["criteria" => $criteria]);
        }


        return view("admin.inputDocument",["title" => $criteria]);
    }

    public function editDocument(string $criteria,Request $request){
        $id = $request->get("doc");
        $document = Document::where("id",$id)->where("type",$criteria)->get()[0];
        $filePath = "null";

        if($document == null){
            abort(404);
        }

        if ($request->method() == "POST") {
            if ($request->hasFile("document")) {
                $file = $request->file("document");
                $fileName = time() . "_" . $file->getClientOriginalName(); 
                $filePath = $file->storeAs("documents", $fileName, "public"); 
            }

            $document->update([
                "name" => $request->input("name"),
                "file_url" => $request->input("file_url"),
                "type" => $criteria,
                "path" => $filePath ?? $document->path,
            ]);

            return redirect()->route("typeCriteria",["criteria" => $criteria]);

        }

        return view("admin.inputDocument",["title" => "Edit ". $document->name,"document" => $document,"id" => $id]);
    
    }

    public function deleteDocument(string $criteria,Request $request){
        $id = $request->get("doc");
        $document = Document::where("id",$id)->where("type",$criteria)->get()[0];
        if($document == null){
            abort(404);
        }

        Document::destroy($document->id);
        return redirect()->route("typeCriteria",["criteria" => $criteria]);
    }

    public function document(string $criteria,Request $request){
        $id = $request->get("doc");
        $document = Document::where("id",$id)->where("type",$criteria)->get()[0];

        if($document == null){
            abort(404);
        }

        return view("admin.document",["title" => $criteria,"document" => $document]);
    }

    public function documents(){
        $documents = Document::all();
        return view("admin.documents",["documents" => $documents]);
    }

}
