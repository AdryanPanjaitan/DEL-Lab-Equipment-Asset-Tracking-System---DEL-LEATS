<?php

use Illuminate\Support\Route;
use app\http\controller\EquipmentController;
use app\http\controller\LoanController;

Route::get('/', fn () => redirect()->route('equipments.index'));

Route::