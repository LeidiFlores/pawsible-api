<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'species', 'breed', 'age', 'gender', 'color', 'size', 'description', 'image'])]
class Pet extends Model
{
    //
}
