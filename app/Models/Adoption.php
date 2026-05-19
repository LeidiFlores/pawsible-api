<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['pet_id', 'user_id', 'status'])]
class Adoption extends Model
{

}
