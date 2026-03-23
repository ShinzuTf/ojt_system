<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TemplateAssignmentService;

class TemplateController extends Controller
{
    /**
     * Display the template management page (view-only)
     * Templates are now automatically assigned to students upon OJT profile completion
     */
    public function index()
    {
        $requiredTemplates = TemplateAssignmentService::getRequiredTemplates();
        return view('admin.templates', ['templates' => $requiredTemplates]);
    }
}
