<?php

namespace App\Http\Controllers;

use App\Models\Hod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(): View
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);
        $hods = Hod::orderBy('department')->orderBy('name')->paginate(25);
        return view('hods.index', compact('hods'));
    }

    public function create(): View
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);
        return view('hods.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'notes'      => ['nullable', 'string', 'max:1000'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        Hod::create([...$validated, 'is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('hods.index')
            ->with('toast', 'HOD / Incharge officer added successfully.')
            ->with('toast_type', 'success');
    }

    public function edit(Hod $hod): View
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);
        return view('hods.edit', compact('hod'));
    }

    public function update(Request $request, Hod $hod): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'notes'      => ['nullable', 'string', 'max:1000'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $hod->update([...$validated, 'is_active' => $request->boolean('is_active')]);

        return redirect()->route('hods.index')
            ->with('toast', 'HOD record updated.')
            ->with('toast_type', 'success');
    }

    public function destroy(Hod $hod): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);
        $hod->delete();
        return redirect()->route('hods.index')
            ->with('toast', 'HOD record deleted.')
            ->with('toast_type', 'warning');
    }
}
