<?php


use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('index');
// });

Route::get('/{route?}',function(){
    return view('index');
});


