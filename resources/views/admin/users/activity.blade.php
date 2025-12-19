@extends('layouts.admin')

@section('title', 'User Activity | Admin Panel')

@section('page-title', 'User Activity')

@section('page-description', 'Track user activities and platform events')

@section('content')
<!-- content @s -->
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">User Activity</h3>
                        <div class="nk-block-des text-soft">
                            <p>Track user activities and platform events</p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <form method="GET" action="{{ route('admin.users.activity') }}">
                                            <div class="form-control-wrap">
                                                <select name="period" class="form-select" onchange="this.form.submit()">
                                                    <option value="7" {{ request('period', 30) == 7 ? 'selected' : '' }}>Last 7 days</option>
                                                    <option value="30" {{ request('period', 30) == 30 ? 'selected' : '' }}>Last 30 days</option>
                                                    <option value="90" {{ request('period', 30) == 90 ? 'selected' : '' }}>Last 90 days</option>
                                                </select>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Recent Activity</h6>
                                <p>Latest platform events and user activities</p>
                            </div>
                        </div>

                        @if($paginatedActivities->count() > 0)
                            <ul class="nk-activity">
                                @foreach($paginatedActivities as $activity)
                                    <li class="nk-activity-item">
                                        <div class="nk-activity-media user-avatar bg-primary">
                                            <em class="icon ni ni-activity-round"></em>
                                        </div>
                                        <div class="nk-activity-data">
                                            <div class="label">{{ $activity->description }}</div>
                                            <span class="time">{{ $activity->created_at->diffForHumans() }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-4">
                                {{ $paginatedActivities->appends(['period' => request('period')])->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <em class="icon ni ni-activity" style="font-size: 3rem; opacity: 0.3;"></em>
                                <p class="text-soft mt-2">No user activities found</p>
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