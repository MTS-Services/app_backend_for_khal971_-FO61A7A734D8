<?php

namespace App\Http\Services;

use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SubjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getSubjects($orderBy = 'order_index', $order = 'asc')
    {
        return Subject::orderBy($orderBy, $order)->latest();
    }
    public function getSubject($encriptedId)
    {
        return Subject::findOrFail(decrypt($encriptedId));
    }
    public function createSubject($data)
    {
        $data['created_by'] = Auth::user()->id;
        return Subject::create($data);
    }
}
