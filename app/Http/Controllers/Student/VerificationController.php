<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\LookupRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Services\ActivityLogService;
use App\Services\StudentVerificationService;
use DomainException;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(private StudentVerificationService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->session()->has('graduand_id')) {
            return redirect()->route('student.details');
        }

        return view('student.lookup');
    }

    public function lookup(LookupRequest $request)
    {
        $graduand = $this->service->find($request->registration_number, $request->first_name);

        if (! $graduand) {
            ActivityLogService::log('student.lookup_failed', null, null, [
                'registration_number' => $request->registration_number,
            ]);

            return back()
                ->withInput($request->only('registration_number'))
                ->withErrors(['registration_number' => 'We could not find a record matching those details. Please check them against your student portal.']);
        }

        $request->session()->regenerate();
        $request->session()->put('graduand_id', $graduand->id);

        ActivityLogService::log('student.lookup_success', $graduand);

        return redirect()->route('student.details');
    }

    public function show(Request $request)
    {
        $graduand = $request->attributes->get('graduand');

        return view('student.details', [
            'graduand' => $graduand,
            'windowOpen' => $graduand->cohort->isOpen(),
        ]);
    }

    public function update(StudentUpdateRequest $request)
    {
        try {
            $this->service->submit($request->attributes->get('graduand'), $request->validated(), $request->ip());
        } catch (DomainException $e) {
            return redirect()->route('student.details')->with('error', $e->getMessage());
        }

        return redirect()->route('student.details')
            ->with('success', 'Thank you. Your details have been submitted and your record is now locked.');
    }

    public function exit(Request $request)
    {
        $request->session()->forget('graduand_id');

        return redirect()->route('student.lookup');
    }
}
