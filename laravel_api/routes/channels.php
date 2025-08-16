<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
 
// Private channel for tasks of a specific user
Broadcast::channel('tasks.{userId}', function ($user, $userId) {
    Log::info("Broadcast auth attempt", [
        'auth_user_id' => $user->id,
        'requested_channel_user_id' => $userId
    ]);

    // Only allow access if authenticated user's ID matches the channel ID
    return (int) $user->id === (int) $userId;
});

// Example public channel (if needed)
Broadcast::channel('public.tasks', function () {
    return true; // anyone can listen
});
