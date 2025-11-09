<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'type'  => $this->type,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'total_marks'  => $this->total_marks,
            'is_active'    => $this->is_active,
            'modules'    => ModuleResource::collection($this->whenLoaded('modules')),
            'modules_count' => $this->modules()->count(),
            'rubric'       => new RubricResource($this->whenLoaded('rubric')),
        ];
    }
}
