<?php

namespace App\Http\Controllers;

use App\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function get($id)
    {
        $urlImages = Test::find($id);
        dd(explode(" ",$urlImages->urlImages));
    }
}
