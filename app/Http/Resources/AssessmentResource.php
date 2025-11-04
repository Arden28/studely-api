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
            'module_id' => $this->module_id,
            'type'  => $this->type,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'total_marks'  => $this->total_marks,
            'is_active'    => $this->is_active,
            'questions'    => QuestionResource::collection($this->whenLoaded('questions')),
            'rubric'       => new RubricResource($this->whenLoaded('rubric')),
        ];
    }
}
