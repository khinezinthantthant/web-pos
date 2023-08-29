<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandDetailResource;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brand = Brand::latest("id")->paginate(5)->withQueryString();
        // return response()->json($brand);
        return BrandResource::collection($brand);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        Gate::authorize("admin");
        $brand = Brand::create([
            "name" => $request->name,
            "company" => $request->company,
            'agent' => $request->agent,
            'description' => $request->description ?? null,
            'phone_no' => $request->phone_no,
            "photo" => $request->photo,
            "user_id" => Auth::id()
        ]);


        // return response()->json($brand);
        return new BrandResource($brand);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::find($id);

        if (is_null($brand)) {
            return response()->json([
                // "success" => false,
                "message" => "Brand not found",

            ], 404);
        }

        // return response()->json($brand);
        return new BrandDetailResource($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, string $id)
    {
        Gate::authorize("admin");
        $brand = Brand::find($id);

        if (is_null($brand)) {
            return response()->json([
                // "success" => false,
                "message" => "Brand not found",

            ], 404);
        }

        $brand->update([
            "name" => $request->name,
            "company" => $request->company,
            "agent" => $request->agent,
            'description' => $request->description ?? null,
            'phone_no' => $request->phone_no,
            'photo' => $request->photo,
        ]);


        // return response()->json($brand);
        return new BrandResource($brand);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize("admin");
        $brand = Brand::find($id);

        if (is_null($brand)) {
            return response()->json([
                // "success" => false,
                "message" => "Brand not found",
            ], 404);
        }

        $brand->delete();

        return response()->json([
            "message" => "brand deleted"
        ], 204);
    }
}
