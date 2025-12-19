@extends('layouts.admin')

@section('title', 'Create User | Admin Panel')

@section('page-title', 'Create User')

@section('page-description', 'Add a new user to the platform')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Create New User</h3>
                        <div class="nk-block-des text-soft">
                            <p>Add a new user to the platform with specified role and permissions.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light">
                            <em class="icon ni ni-arrow-left"></em><span>Back to Users</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <form action="{{ route('admin.users.store') }}" method="POST" class="form-validate">
                            @csrf
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control @error('name') error @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <span class="form-note-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="email" class="form-control @error('email') error @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <span class="form-note-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control @error('password') error @enderror" id="password" name="password" required>
                                            @error('password')
                                                <span class="form-note-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-note">Password must be at least 8 characters long.</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password_confirmation">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="role">User Role <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <select class="form-select @error('role') error @enderror" id="role" name="role" required>
                                                <option value="">Select Role</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <span class="form-note-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="phone">Phone Number</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control @error('phone') error @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                            @error('phone')
                                                <span class="form-note-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="bio">Bio/Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control @error('bio') error @enderror" id="bio" name="bio" rows="4" placeholder="Brief description about the user">{{ old('bio') }}</textarea>
                                            @error('bio')
                                                <span class="form-note-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="website">Website URL</label>
                                        <div class="form-control-wrap">
                                            <input type="url" class="form-control @error('website') error @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                                            @error('website')
                                                <span class="form-note-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary">Create User</button>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-lg btn-outline-light">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
