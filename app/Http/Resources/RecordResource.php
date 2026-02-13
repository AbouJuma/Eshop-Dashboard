<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class RecordResource extends JsonResource
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
            'status' => $this->status,
            'location' => $this->address->location,
            'grandTotal' => $this->grand_total,
            'finalTotal' => $this->final_total,
            'appointmentDate' => $this->date,
            'appointmentTime' => date('h:i:s A', strtotime($this->from)) . ' - ' . date('h:i:s A', strtotime($this->to)),
            'client' => new UserResource($this->client),
            'sub_services' => BookingSubServiceResource::collection($this->bookingSubServices),
        ];
    }
}
