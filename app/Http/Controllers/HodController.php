<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Models\Hod;
use App\Models\User;
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

    public function index(Request $request): View
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);

        $query = Hod::withCount([
            'escalations',
            'escalations as pending_escalations_count' => fn ($q) => $q->where('status', 'pending'),
        ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('department', 'like', "%{$s}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('role')) {
            $query->where('notes', 'like', "%{$request->role}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $hods = $query->orderBy('department')->orderBy('name')
            ->paginate(20)->withQueryString();

        $departments = Hod::distinct()->orderBy('department')
            ->pluck('department');

        $stats = [
            'total'               => Hod::count(),
            'active'              => Hod::where('is_active', true)->count(),
            'inactive'            => Hod::where('is_active', false)->count(),
            'pending_escalations' => Escalation::where('status', 'pending')->count(),
        ];

        return view('hods.index', compact('hods', 'stats', 'departments'));
    }

    public function importCandidates(): \Illuminate\Http\JsonResponse
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);

        $existingEmails = Hod::pluck('email')->map(fn($e) => strtolower($e))->toArray();

        $candidates = User::whereIn('role', [User::ROLE_QA_HOD, User::ROLE_LINE_MANAGER])
            ->where('is_active', true)
            ->orderBy('fname')->orderBy('lname')
            ->get()
            ->filter(fn($u) => !in_array(strtolower($u->email), $existingEmails))
            ->values()
            ->map(fn($u) => [
                'id'        => $u->id,
                'name'      => $u->getFullName(),
                'email'     => $u->email,
                'phone'     => null,
                'role'      => $u->role,
                'role_label'=> $u->getRoleLabel(),
            ]);

        return response()->json($candidates);
    }

    public function import(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);

        $validated = $request->validate([
            'users'              => ['required', 'array', 'min:1'],
            'users.*.id'         => ['required', 'exists:users,id'],
            'users.*.department' => ['required', 'string', 'max:255'],
            'users.*.phone'      => ['nullable', 'string', 'max:30'],
        ]);

        $existingEmails = Hod::pluck('email')->map(fn($e) => strtolower($e))->toArray();
        $imported = 0;

        foreach ($validated['users'] as $row) {
            $user = User::whereKey($row['id'])
                ->whereIn('role', [User::ROLE_QA_HOD, User::ROLE_LINE_MANAGER])
                ->where('is_active', true)
                ->first();

            if (!$user || in_array(strtolower($user->email), $existingEmails)) {
                continue;
            }

            Hod::create([
                'name'       => $user->getFullName(),
                'department' => $row['department'],
                'email'      => $user->email,
                'phone'      => $row['phone'] ?? null,
                'notes'      => $user->getRoleLabel(),
                'is_active'  => true,
            ]);

            $existingEmails[] = strtolower($user->email);
            $imported++;
        }

        return redirect()->route('hods.index')
            ->with('toast', $imported > 0
                ? "{$imported} officer(s) imported successfully."
                : 'No new officers were imported (already exist or invalid).')
            ->with('toast_type', $imported > 0 ? 'success' : 'warning');
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
