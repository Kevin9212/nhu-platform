<?php
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('conversations.{conversationId}', function ($user, $conversationId) {
    $conv = Conversation::find($conversationId);
    if (!$conv) return false;
    return in_array($user->id, [$conv->buyer_id, $conv->seller_id], true);
});




