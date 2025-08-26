<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('clients', function ($user) {
    return true;
});

Broadcast::channel('product', function ($product) {
    return true;
});

Broadcast::channel('plan', function ($plan) {
    return true;
});