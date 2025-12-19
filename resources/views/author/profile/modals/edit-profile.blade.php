<!-- Profile Edit Modal -->
<div class="modal fade" tabindex="-1" id="profile-edit" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-lg">
                <h5 class="title">Update Profile</h5>
                <ul class="nk-nav nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personal">Personal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#address">Address</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#social">Social Links</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="personal">
                        <form id="profile-form" class="form-validate is-alter">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="full-name">Full Name</label>
                                        <input type="text" class="form-control form-control-lg" id="full-name" name="name" value="{{ $user->name }}" placeholder="Enter Full name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="display-name">Display Name</label>
                                        <input type="text" class="form-control form-control-lg" id="display-name" name="display_name" value="{{ $user->profile_data['display_name'] ?? '' }}" placeholder="Enter display name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="phone-no">Phone Number</label>
                                        <input type="text" class="form-control form-control-lg" id="phone-no" name="phone" value="{{ $user->phone ?? '' }}" placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="birth-day">Date of Birth</label>
                                        <input type="date" class="form-control form-control-lg date-picker" id="birth-day" name="date_of_birth" value="{{ $user->profile_data['date_of_birth'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="bio">Bio</label>
                                        <textarea class="form-control form-control-lg" id="bio" name="bio" placeholder="Tell us about yourself" rows="4">{{ $user->profile_data['bio'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="website">Website</label>
                                        <input type="url" class="form-control form-control-lg" id="website" name="website" value="{{ $user->profile_data['website'] ?? '' }}" placeholder="https://example.com">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                        <li>
                                            <button type="submit" class="btn btn-lg btn-primary">Update Profile</button>
                                        </li>
                                        <li>
                                            <a href="#" data-bs-dismiss="modal" class="link link-light">Cancel</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="address">
                        <form id="address-form" class="form-validate is-alter">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="address-line-1">Address</label>
                                        <input type="text" class="form-control form-control-lg" id="address-line-1" name="address" value="{{ $user->profile_data['address'] ?? '' }}" placeholder="Enter your address">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                        <li>
                                            <button type="submit" class="btn btn-lg btn-primary">Update Address</button>
                                        </li>
                                        <li>
                                            <a href="#" data-bs-dismiss="modal" class="link link-light">Cancel</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="social">
                        <form id="social-form" class="form-validate is-alter">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="facebook">Facebook</label>
                                        <input type="url" class="form-control form-control-lg" id="facebook" name="social_links[facebook]" value="{{ $user->profile_data['social_links']['facebook'] ?? '' }}" placeholder="https://facebook.com/username">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="twitter">Twitter</label>
                                        <input type="url" class="form-control form-control-lg" id="twitter" name="social_links[twitter]" value="{{ $user->profile_data['social_links']['twitter'] ?? '' }}" placeholder="https://twitter.com/username">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="instagram">Instagram</label>
                                        <input type="url" class="form-control form-control-lg" id="instagram" name="social_links[instagram]" value="{{ $user->profile_data['social_links']['instagram'] ?? '' }}" placeholder="https://instagram.com/username">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="linkedin">LinkedIn</label>
                                        <input type="url" class="form-control form-control-lg" id="linkedin" name="social_links[linkedin]" value="{{ $user->profile_data['social_links']['linkedin'] ?? '' }}" placeholder="https://linkedin.com/in/username">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                        <li>
                                            <button type="submit" class="btn btn-lg btn-primary">Update Social Links</button>
                                        </li>
                                        <li>
                                            <a href="#" data-bs-dismiss="modal" class="link link-light">Cancel</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
