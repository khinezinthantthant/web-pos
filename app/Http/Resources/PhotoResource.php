<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "url" => asset(Storage::url($this->url)),
            "name" => $this->name,
            "extension" => $this->extension,
            "fileSize" => $this->size
        ];
        return parent::toArray($request);
    }
}
