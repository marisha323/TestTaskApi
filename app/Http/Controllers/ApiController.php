<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function test(Request $request)
    {
        $name = $request->input('name');
        $gender = $request->input('gender');
        $age = $request->input('age');
        return response()->json(['message' => 'Request processed successfully','name'=>$name,'gender'=>$gender,'age'=>$age]);
    }
}
