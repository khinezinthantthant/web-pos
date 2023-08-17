<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->role === "admin") {
            $photos = Photo::all();
            return $photos;
        } else {
            $photos = Auth::user()->photos;
        }

        return PhotoResource::collection($photos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return "hello";
        if($request->hasFile("photos")){
            $photos = [];
            foreach($request->file("photos") as $photo){

                $url = $photo->store("public/photos");
                $fileName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();

                $photoModel = new Photo([
                    "url" => $url,
                    "fileName" => $fileName,
                    "extension" => $extension,
                    "user_id" => Auth::id(),
                    "created_at" => now(),
                    "updated_at" => now()
                ]);

                $photoModel->save();

                $photos[] = $photoModel;

            }
            return response()->json([
                "message" => "Photo Uploaded successfully",
                "photos" => $photos
            ],200);

        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
