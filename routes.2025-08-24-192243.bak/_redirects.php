<?php
use Illuminate\Support\Facades\Route;
// Legacy to canonical (examples):
Route::permanentRedirect('/login/organization', '/org/login');
Route::permanentRedirect('/organizations/register', '/org/register');
