<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\juridicos;

class LegalController extends Controller
{
    public function getLegal(){
        $legal = juridicos::get();

        return response()->json($legal);
    }
}
