<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class TestController extends Controller
{
    public function first(Request $request)
    {
        var_dump('merge');
    }
}
