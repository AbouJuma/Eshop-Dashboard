<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->image ? url('/uploads/' . $this->image) : null,
            'title' => $this->title,
            'location' => $this->location,
            'date' => $this->date,
            'time' => date('h:i:s A', strtotime($this->from)) . ' - ' . date('h:i:s A', strtotime($this->to)),
        ];
    }
}
