<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

Route::get("/", function () {
    return view("welcome");
});

Route::post("/messages", [MessageController::class, "store"]);
