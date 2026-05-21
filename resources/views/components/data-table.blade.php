@props([
    'columns'     => [],   // array of ['label' => '...', 'width' => '...'] — optional width
    'rows'        => [],   // array of row data passed to the default slot via loop
    'emptyText'   => 'No records found.',
    'emptyIcon'   => 'bi-inbox',
    'id'          => null,
    'striped'     => false,
    'stickyHead'  => false,
])
<div {{ $attributes->merge(['class' => 'data-table-wrap']) }}
     @if($id) id="{{ $id }}" @endif>

    <style>
        .data-table-wrap {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #dce8e1;
            background: #fff;
        }
        .data-table-wrap .dt-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }
        .data-table-wrap .dt-thead th {
            background: #f6faf8;
            color: #374151;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #dce8e1;
            white-space: nowrap;
        }
        .data-table-wrap .dt-tbody tr {
            border-bottom: 1px solid #edf2ef;
            transition: background 0.15s;
        }
        .data-table-wrap .dt-tbody tr:last-child {
            border-bottom: none;
        }
        .data-table-wrap .dt-tbody tr:hover {
            background: #f4fbf7;
        }
        .data-table-wrap.dt-striped .dt-tbody tr:nth-child(even) {
            background: #f9fdfb;
        }
        .data-table-wrap.dt-striped .dt-tbody tr:nth-child(even):hover {
            background: #f0f8f4;
        }
        .data-table-wrap .dt-tbody td {
            padding: 0.8rem 1rem;
            vertical-align: top;
            color: #374151;
            line-height: 1.45;
        }
        .data-table-wrap .dt-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }
        .data-table-wrap .dt-empty i { font-size: 1.75rem; display: block; margin-bottom: 0.5rem; }

        /* Cell helpers — use these classes inside td content */
        .dt-ref {
            font-weight: 700;
            color: var(--ccbrt-teal, #2b7d6c);
            font-size: 0.82rem;
            white-space: nowrap;
            text-decoration: none;
        }
        .dt-ref:hover { text-decoration: underline; color: var(--ccbrt-navy, #065321); }
        .dt-sub {
            display: block;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.1rem;
            font-weight: 400;
        }
        .dt-sub .dot { margin: 0 0.25rem; opacity: 0.5; }
        .dt-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 5px;
            font-size: 0.72rem;
            font-weight: 600;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .dt-badge-portal   { background: #e8f4fd; color: #1d6fa4; border-color: #c3dff5; }
        .dt-badge-manual   { background: #fff8e1; color: #8a6200; border-color: #ffe082; }
        .dt-badge-new      { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
        .dt-badge-review   { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .dt-badge-responded{ background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
        .dt-badge-closed   { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
        .dt-truncate {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #4b5563;
            font-size: 0.82rem;
        }
        .dt-user-stack { line-height: 1.35; }
        .dt-user-stack strong { font-weight: 600; color: #111827; font-size: 0.83rem; }
        .dt-user-stack span  { display: block; font-size: 0.73rem; color: #9ca3af; }
        .dt-timeline { font-size: 0.74rem; color: #6b7280; line-height: 1.65; white-space: nowrap; }
        .dt-timeline strong { font-weight: 600; color: #374151; }
        .dt-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            padding: 0.3rem 0.8rem;
            border: 1.5px solid var(--ccbrt-teal, #2b7d6c);
            border-radius: 6px;
            color: var(--ccbrt-teal, #2b7d6c);
            font-size: 0.78rem;
            font-weight: 600;
            background: transparent;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
        }
        .dt-action-btn:hover {
            background: var(--ccbrt-teal, #2b7d6c);
            color: #fff;
        }
        .dt-action-link {
            color: var(--ccbrt-teal, #2b7d6c);
            font-weight: 600;
            font-size: 0.83rem;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        .dt-action-link:hover { text-decoration: underline; color: var(--ccbrt-navy, #065321); }

        /* Sticky header */
        .data-table-wrap.dt-sticky-head { overflow: visible; }
        .data-table-wrap.dt-sticky-head .dt-thead th { position: sticky; top: 0; z-index: 2; }

        @media (max-width: 767px) {
            .data-table-wrap .dt-truncate { max-width: 130px; }
            .data-table-wrap .dt-table { font-size: 0.78rem; }
            .data-table-wrap .dt-thead th,
            .data-table-wrap .dt-tbody td { padding: 0.6rem 0.65rem; }
        }
    </style>

    <div style="overflow-x:auto;">
        <table class="dt-table{{ $striped ? ' dt-striped' : '' }}">
            @if(count($columns))
            <thead class="dt-thead">
                <tr>
                    @foreach($columns as $col)
                        <th @if(!empty($col['width'])) style="width:{{ $col['width'] }}" @endif>
                            {{ $col['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            @endif
            <tbody class="dt-tbody">
                @if($slot->isEmpty())
                    <tr>
                        <td colspan="{{ max(count($columns), 1) }}" class="dt-empty">
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
