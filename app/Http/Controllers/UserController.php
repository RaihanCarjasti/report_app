<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Document;

class UserController extends Controller
{
    public function addReview(string $criteria,Request $request){
        $id = $request->get("doc");
        $document = Document::where("id",$id)->where("type",$criteria)->get()[0];

        if(  $document == null){
            abort(404);
        }

        if($request->method() == "POST"){
            $document->update([
                "comment" => $request->input("comment"),
                "status" => $request->input("status") 
            ]);
            return redirect()->route("typeCriteria",["criteria" => $criteria]);
            
        }
        return view("admin.addReview",["title" => $criteria,"document" => $document]);
    }
    
}
