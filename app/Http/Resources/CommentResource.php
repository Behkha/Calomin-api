<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'body' => $this->body,
            'commented_by' => new User($this->commentedBy),
            'created_at' => Jalalian::forge($this->created_at)->format('%d-%m-%Y | %H:i'),
        ];
    }
}
