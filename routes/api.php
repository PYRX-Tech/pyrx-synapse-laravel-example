<?php

use App\Http\Controllers\Api\SynapseController;
use Illuminate\Support\Facades\Route;

// Core
Route::post('track', [SynapseController::class, 'track']);
Route::post('track/batch', [SynapseController::class, 'trackBatch']);
Route::post('identify', [SynapseController::class, 'identify']);
Route::post('identify/batch', [SynapseController::class, 'identifyBatch']);
Route::post('send', [SynapseController::class, 'sendEmail']);

// Contacts
Route::get('contacts', [SynapseController::class, 'listContacts']);
Route::put('contacts/{id}', [SynapseController::class, 'updateContact']);
Route::delete('contacts/{id}', [SynapseController::class, 'deleteContact']);

// Templates
Route::get('templates', [SynapseController::class, 'listTemplates']);
Route::post('templates', [SynapseController::class, 'createTemplate']);
Route::get('templates/{slug}', [SynapseController::class, 'getTemplate']);
Route::put('templates/{slug}', [SynapseController::class, 'updateTemplate']);
Route::post('templates/{slug}/preview', [SynapseController::class, 'previewTemplate']);
Route::delete('templates/{slug}', [SynapseController::class, 'deleteTemplate']);
