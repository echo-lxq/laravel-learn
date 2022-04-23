<?php

namespace App\Http\Controllers;

class OneController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return '单行为控制器1';
    }
}
