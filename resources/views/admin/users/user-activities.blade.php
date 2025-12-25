@extends('layouts.admin')

@section('title', $user->name . ' - User Activities | Admin Panel')

@section('page-title', $user->name . ' - User Activities')

@section('page-description', 'Activity log for user: ' . $user->name . ' (' . $user->email . ')')

@section('content')
<!-- content @s -->
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Activities for {{ $user->name }}</h3>
                        <div class="nk-block-des text-soft">
                            <p>User ID: #{{ $user->id }} • Joined {{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <form method="GET" action="{{ route('admin.users.activities', $user) }}">
                                            <div class="form-control-wrap">
                                                <select name="period" class="form-select" onchange="this.form.submit()">
                                                    <option value="7" {{ request('period', 30) == 7 ? 'selected' : '' }}>Last 7 days</option>
                                                    <option value="30" {{ request('period', 30) == 30 ? 'selected' : '' }}>Last 30 days</option>
                                                    <option value="90" {{ request('period', 30) == 90 ? 'selected' : '' }}>Last 90 days</option>
                                                </select>
                                            </div>
                                        </form>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-white btn-dim btn-outline-light">
                                            <em class="icon ni ni-user"></em><span>User Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-white btn-dim btn-outline-light">
                                            <em class="icon ni ni-users"></em><span>All Users</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->

            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Activity Log</h6>
                                <p>Recent activities performed by this user</p>
                            </div>
                        </div>

                        @if($paginatedActivities->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-tranx table-striped" id="user-activities-table">
                                    <thead>
                                        <tr>
                                            <th class="tb-col-date">Date</th>
                                            <th class="tb-col-type">Activity Type</th>
                                            <th class="tb-col-desc">Description</th>
                                            <th class="tb-col-ip">IP Address</th>
                                            <th class="tb-col-agent">User Agent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paginatedActivities as $activity)
                                        <tr>
                                            <td class="tb-col-date">
                                                <span class="sub-text">{{ $activity->created_at->format('M d, Y H:i') }}</span>
                                                <br>
                                                <span class="sub-text sub-date">{{ $activity->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td class="tb-col-type">
                                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</span>
                                            </td>
                                            <td class="tb-col-desc">
                                                <span>{{ $activity->description }}</span>
                                                @if($activity->metadata)
                                                    <div class="mt-1">
                                                        <details>
                                                            <summary class="text-muted small">View Details</summary>
                                                            <pre class="mb-0" style="font-size: 0.75rem; background: #f8f9fa; padding: 8px; border-radius: 4px; max-height: 100px; overflow-y: auto;">{{ json_encode($activity->metadata, JSON_PRETTY_PRINT) }}</pre>
                                                        </details>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="tb-col-ip">
                                                <span class="sub-text">{{ $activity->ip_address ?? 'N/A' }}</span>
                                            </td>
                                            <td class="tb-col-agent">
                                                <span class="sub-text" style="font-size: 0.75rem;">{{ Illuminate\Support\Str::limit($activity->user_agent ?? 'N/A', 50) }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $paginatedActivities->appends(['period' => request('period')])->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <em class="icon ni ni-activity" style="font-size: 3rem; opacity: 0.3;"></em>
                                <p class="text-soft mt-2">No activities found for this user</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div><!-- .nk-block -->
        </div>
    </div>
</div>
<!-- content @e -->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#user-activities-table').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": true, "targets": [0, 1, 2, 3, 4] },
            { "orderable": false, "targets": [] }
        ],
        "responsive": true,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
});
</script>
@endpush