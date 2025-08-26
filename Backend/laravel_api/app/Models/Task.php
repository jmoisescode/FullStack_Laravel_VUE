<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @OA\Schema(
 *   schema="Task",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="title", type="string", example="Fix bug"),
 *   @OA\Property(property="description", type="string", example="Resolve cache issue"),
 *   @OA\Property(property="status", type="string", example="pending"),
 *   @OA\Property(property="priority", type="string", example="high"),
 *   @OA\Property(property="order", type="integer", example=0),
 *   @OA\Property(property="user_id", type="integer", example=1)
 * )
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'status', 'priority', 'order'
    ];

    // Add this relationship method
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope for filtering by status
    public function scopeStatus($query, $status)
    {
        if (!empty($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    // Scope for filtering by priority
    public function scopePriority($query, $priority)
    {
        if (!empty($priority)) {
            return $query->where('priority', $priority);
        }
        return $query;
    }
}
