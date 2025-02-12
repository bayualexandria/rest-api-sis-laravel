<?php


use Illuminate\Support\Facades\Route;



Route::get('/{route?}',function(){
    return view('index');
});

Route::get('{any}', function () {
    return view('index'); // or wherever your React app is bootstrapped.
})->where('any', '.*');

