<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return "hello";
        if ($request->hasFile('photos')) {
            $photos = $request->file('photos');
            $savedPhotos = [];
            foreach ($photos as $photo) {
                $savedPhoto = $photo->store("public/photo");
                $savedPhotos[] = [
                    "article_id" => $request->article_id,
                    "address" => $savedPhoto,
                    "created_at" => now(),
                    "updated_at" => now()

                ];
            }
            Photo::insert($savedPhotos);


            //  foreach($photos as $photo){
            //     $savedPhoto = $photo->store("public/photo");
            //     $savedPhotos [] = [ "address" => $savedPhoto];
            //  }
            //  $article->photos()->createMany($savedPhotos);
        }
        return redirect()->back();
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
