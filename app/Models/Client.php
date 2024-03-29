<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property ?string $description
 * @property ?string $contact_email
 * @property ?string $contact_phone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property Collection<Project> $projects
 */
class Client extends Model
{
    use HasFactory, SoftDeletes;

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
