<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Spatie\MediaLibrary\MediaCollections\Models\Media as Model;

class Media extends Model
{
    use HasUlids;
}
