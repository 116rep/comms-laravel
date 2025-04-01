<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VoiceChatController;

Route::get("/", function () {
    return view("welcome");
})->middleware("auth"); // Protect home page

Route::post("/messages", [MessageController::class, "store"]);
Route::post("/logout", [AuthController::class, "logout"])->middleware("auth");
Route::post("/voice-chat/join", [
    VoiceChatController::class,
    "join",
])->middleware("auth");
Route::get("/login", [AuthController::class, "showLogin"])->name("login");
Route::post("/login", [AuthController::class, "login"]);
Route::post("/messages", [MessageController::class, "store"])->middleware(
    "auth"
);
Route::get("/settings", function () {
    return view("settings");
})->middleware("auth");
