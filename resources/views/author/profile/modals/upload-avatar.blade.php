<!-- Avatar Upload Modal -->
<div class="modal fade" tabindex="-1" id="avatar-upload" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-lg">
                <h5 class="title">Change Profile Photo</h5>
                <p class="text-soft">Upload a new profile photo. Recommended size is 400x400 pixels.</p>
                <form id="avatar-form" class="form-validate is-alter" enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-4">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="user-avatar user-avatar-xl mx-auto mb-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/images/avatar/' . $user->avatar) }}" alt="{{ $user->name }}" id="avatar-preview">
                                    @else
                                        <span id="avatar-initials">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="avatar-file">Choose Photo</label>
                                <div class="form-control-wrap">
                                    <input type="file" class="form-control form-control-lg" id="avatar-file" name="avatar" accept="image/*" required>
                                </div>
                                <div class="form-note">
                                    Accepted formats: JPG, PNG, GIF. Maximum size: 2MB.
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                <li>
                                    <button type="submit" class="btn btn-lg btn-primary">Upload Photo</button>
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

<script>
// Preview avatar before upload
document.getElementById('avatar-file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            
            if (preview) {
                preview.src = e.target.result;
            } else if (initials) {
                // Replace initials with image
                initials.parentElement.innerHTML = '<img src="' + e.target.result + '" alt="Preview" id="avatar-preview">';
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
