<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class Notification extends Model
{
    use HasFactory, HasAutoIncrementId;
}
