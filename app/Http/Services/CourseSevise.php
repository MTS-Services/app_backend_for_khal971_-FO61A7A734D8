<?php

namespace App\Http\Services;

use App\Models\Course;

class CourseSevise
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getCourses($orderBy = 'order_index', $order = 'asc')
    {
        return Course::orderBy($orderBy, $order)->latest();
    }
}
