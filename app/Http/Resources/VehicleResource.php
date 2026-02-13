<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Client;
use App\Models\User;
use App\Models\Make;
use App\Models\VehicleModel;

class VehicleResource extends JsonResource
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
            'client' => User::find($this->user_id)->only(['id', 'fname', 'sname', 'phone']),
            'make' => Make::find($this->make_id)->only(['id', 'name']),
            'model' => VehicleModel::find($this->model_id)->only(['id', 'name']),
        ];
    }
}
