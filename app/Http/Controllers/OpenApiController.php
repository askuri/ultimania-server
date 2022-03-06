<?php

namespace App\Http\Controllers;

class OpenApiController extends Controller
{
    public function show() {
        return response()->file(resource_path('openapi/contract.yaml'));
    }
}
