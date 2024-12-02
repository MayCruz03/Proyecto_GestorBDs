<?php

use Lib\Route;
use app\Controllers\AuthController;
use App\Controllers\ServerObjectsController;

Route::get("/", [AuthController::class, "main"]);
Route::get("/login", [AuthController::class, "login"]);
Route::post("/login", [AuthController::class, "loginPostBack"]);
Route::get("/logout", [AuthController::class, "logout"]);


Route::post("/serveObjects/types", [ServerObjectsController::class, "getServeObjectsTypes"]);
Route::post("/serveObjects/list", [ServerObjectsController::class, "getServeObjects"]);
Route::post("/serveObjects/dataTypes", [ServerObjectsController::class, "getDataTypes"]);

Route::get("/table/create", [ServerObjectsController::class, "createTable"]);
Route::post("/table/create", [ServerObjectsController::class, "createTablePostBack"]);

