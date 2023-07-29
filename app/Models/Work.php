<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property Carbon $date
 * @property float $hours
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property Project $project
 */
class Work extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'hours' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
