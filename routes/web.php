<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;

Route::get("/", function () {
    return view("welcome");
})->middleware("auth"); // Protect home page

Route::post("/messages", [MessageController::class, "store"]);
Route::post("/logout", [AuthController::class, "logout"])->middleware("auth");

Route::get("/login", [AuthController::class, "showLogin"])->name("login");
Route::post("/login", [AuthController::class, "login"]);
Route::post("/messages", [MessageController::class, "store"])->middleware(
    "auth"
);
