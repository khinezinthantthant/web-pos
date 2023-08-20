<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->role === "admin") {
            $photos = Photo::all();
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
        $request->validate([
            // 'files.*' => 'required|mimes:jpeg,png,jpg,gif'
            "photos" => "required|array",
            "photos.*" => "required|file|max:500|min:50|mimes:jpeg,png,jpg,gif"
        ]);


        if($request->hasFile("photos")){
            $photos = [];
            foreach($request->file("photos") as $photo){

                $url = $photo->store("public/photos");
                $fileName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $fileSize = $photo->getSize();

                $photoModel = new Photo([
                    "url" => $url,
                    "fileName" => $fileName,
                    "extension" => $extension,
                    "fileSize" => $fileSize,
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
        $photo = Photo::find($id);

        if (is_null($photo)) {
            return response()->json([
                'message' => 'photo not found'
            ]);
        }

        Storage::delete($photo->url);
        $photo->delete();

        return response()->json([
            "message" => "photo deleted"
        ]);

    }
}
