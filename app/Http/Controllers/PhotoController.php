<?php

namespace App\Http\Controllers;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->hasFile('photos')) {
            $photos = $request->file('photos');
            // return $photos->extension();
            // return $photos->getClientOriginalName();
            // return $photos->hashName();
            $savedPhotos = [];
            foreach ($photos as $photo) {
                
                $name = $photo->getClientOriginalName();
                $ext = $photo->extension();
                $savedPhoto = $photo->store("public/media");

                $storeUrl = asset(Storage::url($savedPhoto));
dd($storeUrl);
                $savedPhotos[] = [
                    "url" => $savedPhoto,
                    "name" => $name,
                    "ext" => $ext,
                    "user_id" => Auth::id(),
                    "created_at" => now(),
                    "updated_at" => now()

                ];
            }
            Photo::insert($savedPhotos);
        }

        return response()->json([
            "message" => "photo upload successfully"
        ]);
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
        if(is_null($photo)){
            return response()->json([
                "message" => "there is no photo"
            ]);
        }
        $photo->delete();

        return response()->json([
            "message" => "photo delete successfully"
        ]);
    }
}
