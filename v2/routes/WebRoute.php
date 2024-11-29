<?php

use Lib\Route;
use app\Controllers\AuthController;

Route::get("/", [AuthController::class, "main"]);
Route::get("/login", [AuthController::class, "login"]);