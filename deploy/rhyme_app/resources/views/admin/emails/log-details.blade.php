@extends('layouts.admin')

@section('title', 'Email Log Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Email Log Details #{{ $emailLog->id }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.emails.index') }}">Email Management</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.emails.logs') }}">Logs</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Email Content</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Subject:</label>
                        <h5>{{ $emailLog->subject }}</h5>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Message Body:</label>
                        <div class="border p-3 rounded" style="background-color: #f8f9fa; min-height: 200px;">
                            {!! $emailLog->content !!}
                        </div>
                    </div>

                    @if($emailLog->metadata)
                    <div class="mb-3">
                        <label class="form-label text-muted">Metadata:</label>
                        <pre class="bg-light p-3 rounded"><code>{{ json_encode($emailLog->metadata, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Details</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Type :</th>
                                    <td>
                                        <span class="badge 
                                            @if($emailLog->type == 'newsletter') badge-primary
                                            @elseif($emailLog->type == 'announcement') badge-info
                                            @elseif($emailLog->type == 'sales_report') badge-success
                                            @else badge-secondary
                                            @endif font-size-12">
                                            {{ ucfirst(str_replace('_', ' ', $emailLog->type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Sent By :</th>
                                    <td>{{ $emailLog->sender ? $emailLog->sender->name : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Template :</th>
                                    <td>{{ $emailLog->template ? $emailLog->template->name : 'None' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status :</th>
                                    <td>
                                        <span class="badge 
                                            @if($emailLog->status == 'completed') badge-success
                                            @elseif($emailLog->status == 'processing') badge-warning
                                            @elseif($emailLog->status == 'failed') badge-danger
                                            @else badge-secondary
                                            @endif font-size-12">
                                            {{ ucfirst($emailLog->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Recipients :</th>
                                    <td>{{ $emailLog->total_recipients }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Successful :</th>
                                    <td class="text-success">{{ $emailLog->sent_count }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Failed :</th>
                                    <td class="text-danger">{{ $emailLog->failed_count }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At :</th>
                                    <td>{{ $emailLog->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @if($emailLog->started_at)
                                <tr>
                                    <th scope="row">Started At :</th>
                                    <td>{{ $emailLog->started_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($emailLog->completed_at)
                                <tr>
                                    <th scope="row">Completed At :</th>
                                    <td>{{ $emailLog->completed_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
