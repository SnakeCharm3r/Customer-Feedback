<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(): View
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $departments = Department::with(['createdBy', 'hod'])
            ->orderBy('name')
            ->paginate(25);

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $hods = \App\Models\Hod::active()->orderBy('name')->get();
        return view('departments.create', compact('hods'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:150', Rule::unique('departments', 'name')],
            'categories'   => ['nullable', 'array'],
            'categories.*' => ['in:opd,ipd,theatre,other'],
            'hod_id'       => ['nullable', 'integer', 'exists:hods,id'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        Department::create(array_merge($validated, [
            'created_by' => Auth::id(),
            'is_active'  => $request->boolean('is_active', true),
            'categories' => $validated['categories'] ?? [],
        ]));

        return redirect()->route('departments.index')
            ->with('toast', 'Department created successfully.')
            ->with('toast_type', 'success');
    }

    public function edit(Department $department): View
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $hods = \App\Models\Hod::active()->orderBy('name')->get();
        return view('departments.edit', compact('department', 'hods'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:150', Rule::unique('departments', 'name')->ignore($department->id)],
            'categories'   => ['nullable', 'array'],
            'categories.*' => ['in:opd,ipd,theatre,other'],
            'hod_id'       => ['nullable', 'integer', 'exists:hods,id'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $department->update(array_merge($validated, [
            'is_active'  => $request->boolean('is_active'),
            'categories' => $validated['categories'] ?? [],
        ]));

        return redirect()->route('departments.index')
            ->with('toast', 'Department updated.')
            ->with('toast_type', 'success');
    }

    public function destroy(Department $department): RedirectResponse
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $department->delete();

        return redirect()->route('departments.index')
            ->with('toast', 'Department deleted.')
            ->with('toast_type', 'success');
    }
}
