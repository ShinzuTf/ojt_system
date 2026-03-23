<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class ProgressController extends Controller
{
    public function index()
    {
        return view('student.progress');
    }
}
