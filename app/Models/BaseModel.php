<?php

namespace App\Models;

use App\Models\Traits\CustomIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at'];
}
