@props([
    'user',
    'departmentName' => null,
])

@php
    $initials = strtoupper(substr($user->fname ?: ($user->name ?: 'U'), 0, 1))
        . strtoupper(substr($user->lname ?: '', 0, 1));
@endphp

<aside {{ $attributes->class(['card user-identity-card h-100']) }}>
    <div class="user-identity-card__header">
        <div class="user-identity-avatar" aria-hidden="true">{{ $initials }}</div>
        <div class="user-identity-card__copy">
            <h2>{{ $user->getFullName() }}</h2>
            <p>{{ $user->email }}</p>
        </div>
        <div class="user-identity-card__badges">
            <span class="user-profile-badge user-profile-badge--role">{{ $user->getRoleLabel() }}</span>
            <span class="user-profile-badge {{ $user->is_active ? 'user-profile-badge--active' : 'user-profile-badge--pending' }}">
                <i class="bi {{ $user->is_active ? 'bi-check-circle' : 'bi-clock' }}" aria-hidden="true"></i>
                {{ $user->is_active ? 'Active' : 'Pending approval' }}
            </span>
            @if($user->is_first_user)
                <span class="user-profile-badge user-profile-badge--system">System administrator</span>
            @endif
        </div>
    </div>

    <div class="user-identity-card__summary">
        <div class="user-identity-summary-row">
            <span>Role</span>
            <strong>{{ $user->getRoleLabel() }}</strong>
        </div>
        <div class="user-identity-summary-row">
            <span>Department</span>
            <strong>{{ $departmentName ?: 'Not assigned' }}</strong>
        </div>
        <div class="user-identity-summary-row">
            <span>Member since</span>
            <strong>{{ $user->created_at?->format('d M Y') ?? 'Not available' }}</strong>
        </div>
    </div>

    @if(trim((string) $slot) !== '')
        <div class="user-identity-card__extra">{{ $slot }}</div>
    @endif
</aside>
