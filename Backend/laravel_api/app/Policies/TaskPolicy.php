<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    public function update(User $user, Task $task)
    {
        // Allow only if the task belongs to the logged-in user
        return $user->id === $task->user_id;
    }
    public function delete(User $user)
    {
        // Allow only if the user is an admin
        return $user->role === 'admin';
    }
}
