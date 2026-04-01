<?php

use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\BroadcastController;

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

// Kênh thông báo user cá nhân theo subdomain
Broadcast::channel('user.{id}.{sub}.notifications', function ($user, $id, $sub) {
    return app(BroadcastController::class)->authorizeUserChannel($user, $id, $sub);
});
