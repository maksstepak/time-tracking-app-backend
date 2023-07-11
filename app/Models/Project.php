<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property ?string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property Client $client
 */
class Project extends Model
{
    use HasFactory, SoftDeletes;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
