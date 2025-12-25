@extends('layouts.admin')

@section('title', 'Email Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Email Logs</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.emails.index') }}">Email Management</a></li>
                        <li class="breadcrumb-item active">Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="email-logs-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Sender</th>
                                    <th>Recipients</th>
                                    <th>Status</th>
                                    <th>Sent / Failed</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emailLogs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($log->type == 'newsletter') badge-primary
                                            @elseif($log->type == 'announcement') badge-info
                                            @elseif($log->type == 'sales_report') badge-success
                                            @else badge-secondary
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $log->type)) }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($log->subject, 40) }}</td>
                                    <td>{{ $log->sender ? $log->sender->name : 'System' }}</td>
                                    <td>{{ $log->total_recipients }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($log->status == 'completed') badge-success
                                            @elseif($log->status == 'processing') badge-warning
                                            @elseif($log->status == 'failed') badge-danger
                                            @else badge-secondary
                                            @endif">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success">{{ $log->sent_count }}</span> / 
                                        <span class="text-danger">{{ $log->failed_count }}</span>
                                    </td>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.emails.logs.show', $log->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No email logs found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $emailLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
