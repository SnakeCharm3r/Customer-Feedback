@props([
    'title'       => null,
    'description' => null,
    'columns'     => [],
    'emptyText'   => 'No records found.',
    'emptyIcon'   => 'bi-inbox',
    'id'          => null,
])

{{--
    Dark-card list table component.
    Matches the "Users" table design with:
      - Dark navy card wrapper
      - Bold name / muted subtitle cell pattern
      - Inline action links (Edit etc.)

    Usage:
        <x-list-table
            title="Users"
            description="A list of all users in your account."
            :columns="[['label'=>'Name'],['label'=>'Title'],['label'=>'Email'],['label'=>'Role'],['label'=>'']]">
            <tr>
                <td>
                    <span class="lt-name">Lindsay Walton</span>
                    <span class="lt-sub">Front-end Developer</span>
                </td>
                <td class="lt-muted">Front-end Developer</td>
                <td class="lt-muted">lindsay.walton@example.com</td>
                <td class="lt-plain">Member</td>
                <td class="text-end"><a href="#" class="lt-action">Edit</a></td>
            </tr>
        </x-list-table>
--}}

<div {{ $attributes->merge(['class' => 'lt-card-wrap']) }}
     @if($id) id="{{ $id }}" @endif>

    <style>
        .lt-card-wrap {
            background: #1e2433;
            border-radius: 12px;
            overflow: hidden;
            color: #e5e7eb;
        }
        .lt-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.5rem 1.1rem;
        }
        .lt-card-header-text {}
        .lt-card-title {
            font-weight: 700;
            font-size: 1rem;
            color: #f9fafb;
            margin: 0 0 0.25rem;
        }
        .lt-card-desc {
            font-size: 0.83rem;
            color: #9ca3af;
            margin: 0;
        }
        .lt-card-action {
            flex-shrink: 0;
        }
        .lt-table-wrap { overflow-x: auto; }
        .lt-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }
        .lt-thead th {
            background: #252c3d;
            color: #9ca3af;
            font-weight: 600;
            font-size: 0.78rem;
            letter-spacing: 0.025em;
            padding: 0.65rem 1.5rem;
            border-top: 1px solid #2e3650;
            border-bottom: 1px solid #2e3650;
            white-space: nowrap;
        }
        .lt-tbody tr {
            border-bottom: 1px solid #2e3650;
            transition: background 0.12s;
        }
        .lt-tbody tr:last-child { border-bottom: none; }
        .lt-tbody tr:hover { background: #252c3d; }
        .lt-tbody td {
            padding: 0.9rem 1.5rem;
            vertical-align: middle;
            color: #d1d5db;
            line-height: 1.4;
        }

        /* Cell content helpers */
        .lt-name {
            display: block;
            font-weight: 700;
            color: #f9fafb;
            font-size: 0.875rem;
        }
        .lt-sub {
            display: block;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.1rem;
        }
        .lt-muted {
            color: #6b7280 !important;
            font-size: 0.83rem;
        }
        .lt-plain {
            color: #d1d5db;
            font-size: 0.83rem;
        }
        .lt-action {
            color: #818cf8;
            font-weight: 600;
            font-size: 0.83rem;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        .lt-action:hover { color: #a5b4fc; text-decoration: underline; }
        .lt-action-danger { color: #f87171; }
        .lt-action-danger:hover { color: #fca5a5; }

        /* Add user / header CTA button */
        .lt-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 1rem;
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 0.83rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .lt-cta-btn:hover { background: #4f46e5; color: #fff; }
        .lt-cta-btn-teal {
            background: var(--ccbrt-teal, #2b7d6c);
        }
        .lt-cta-btn-teal:hover { background: var(--ccbrt-navy, #065321); }

        /* Empty state */
        .lt-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }
        .lt-empty i { font-size: 1.75rem; display: block; margin-bottom: 0.5rem; }

        @media (max-width: 767px) {
            .lt-card-header { flex-direction: column; }
            .lt-card-action { align-self: flex-end; }
            .lt-thead th,
            .lt-tbody td { padding: 0.65rem 0.9rem; }
        }
    </style>

    @if($title || $description || isset($action))
    <div class="lt-card-header">
        <div class="lt-card-header-text">
            @if($title)    <p class="lt-card-title">{{ $title }}</p> @endif
            @if($description) <p class="lt-card-desc">{{ $description }}</p> @endif
        </div>
        @if(isset($action))
        <div class="lt-card-action">{{ $action }}</div>
        @endif
    </div>
    @endif

    <div class="lt-table-wrap">
        <table class="lt-table">
            @if(count($columns))
            <thead class="lt-thead">
                <tr>
                    @foreach($columns as $col)
                        <th @if(!empty($col['width'])) style="width:{{ $col['width'] }}" @endif
                            @if(!empty($col['class'])) class="{{ $col['class'] }}" @endif>
                            {{ $col['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            @endif
            <tbody class="lt-tbody">
                @if($slot->isEmpty())
                    <tr>
                        <td colspan="{{ max(count($columns), 1) }}" class="lt-empty">
                            <i class="bi {{ $emptyIcon }}"></i>
                            {{ $emptyText }}
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>
</div>
