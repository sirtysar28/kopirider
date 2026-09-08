<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Package;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'heroMedia' => Media::active()->where('type', 'hero')->first(),
            'packages' => Package::active()->get(),
        ]);
    }
}
