<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CriterionScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'criterion'  => [
                'id'        => $this->criterion->id,
                'name'      => $this->criterion->name,
                'max_score' => $this->criterion->max_score,
                'weight'    => (float) $this->criterion->weight,
            ],
            'score'      => (int) $this->score,
            'comment'    => $this->comment,
            'evaluator'  => [
                'id'   => $this->evaluator->id,
                'user' => [
                    'id'    => $this->evaluator->user->id,
                    'name'  => $this->evaluator->user->name,
                    'email' => $this->evaluator->user->email,
                ]
            ],
        ];
    }
}
