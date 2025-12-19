@extends('layouts.app')

@section('title', 'User Dashboard | Rhymes Platform')

@section('content')
<!-- content @s -->
<div class="nk-block">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">User Dashboard</h3>
                <div class="nk-block-des text-soft">
                    <p>Welcome back, {{ Auth::user()->name }}!</p>
                </div>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    
    <div class="nk-block">
        <!-- Welcome Section -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="nk-wg7">
                    <div class="nk-wg7-title">Your Journey to Becoming an Author</div>
                    <div class="nk-wg7-text">
                        <p>You're logged in as a user. To become an author and start selling your books, submit your first book for review.</p>
                    </div>
                    
                    <!-- Progress Indicator -->
                    <div class="nk-wg7-stats mb-4">
                        @php
                            // Calculate overall progress based on book submissions
                            $overallProgress = 33; // Default: User step
                            $progressLabel = 'User';
                            $progressPercent = '33%';
                            $progressClass = 'bg-primary';
                            $statusMessage = 'Submit your first book for review. Once approved, you\'ll become an author.';
                            
                            if(isset($books) && $books->count() > 0) {
                                // Check if user has any accepted books
                                $acceptedBooks = $books->where('status', 'accepted')->count();
                                $stockedBooks = $books->where('status', 'stocked')->count();
                                $publishedBooks = $acceptedBooks + $stockedBooks;
                                
                                // Check if user has any pending books
                                $pendingBooks = $books->where('status', 'pending')->count();
                                
                                // Check if user has any rejected books
                                $rejectedBooks = $books->where('status', 'rejected')->count();
                                
                                if($publishedBooks > 0) {
                                    // User has published books - they are an author
                                    $overallProgress = 100;
                                    $progressLabel = 'Author';
                                    $progressPercent = '100%';
                                    $progressClass = 'bg-success';
                                    $statusMessage = 'Congratulations! You are now an author. Continue adding books to grow your library.';
                                } elseif($pendingBooks > 0) {
                                    // User has books under review
                                    $overallProgress = 66;
                                    $progressLabel = 'Review in Progress';
                                    $progressPercent = '66%';
                                    $progressClass = 'bg-warning';
                                    $statusMessage = 'Your book is under review. You\'ll become an author once it\'s approved.';
                                } elseif($rejectedBooks > 0 && $books->count() == $rejectedBooks) {
                                    // All books rejected
                                    $overallProgress = 33;
                                    $progressLabel = 'Resubmit Required';
                                    $progressPercent = '33%';
                                    $progressClass = 'bg-danger';
                                    $statusMessage = 'Your book submission was not approved. Please review feedback and resubmit.';
                                } else {
                                    // Mixed status or other cases
                                    $overallProgress = 66;
                                    $progressLabel = 'In Progress';
                                    $progressPercent = '66%';
                                    $progressClass = 'bg-warning';
                                    $statusMessage = 'You have book submissions in various stages. Continue the process to become an author.';
                                }
                            }
                        @endphp
                        
                        <div class="progress progress-lg">
                            <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $overallProgress }}%"></div>
                        </div>
                        <div class="progress-step mt-2">
                            <div class="progress-info">
                                <span class="progress-label">{{ $progressLabel }}</span>
                                <span class="progress-percent">{{ $progressPercent }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-primary">
                        <div class="alert-cta">
                            <div class="alert-text">
                                <h6>Current Status:</h6>
                                <p>{{ $statusMessage }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Status Details (if user has submitted books) -->
                    @if(isset($books) && $books->count() > 0)
                    <div class="nk-wg7-stats mt-4">
                        <h6 class="title mb-3">Your Book Submissions</h6>
                        @foreach($books as $book)
                        <div class="card card-full mb-3">
                            <div class="card-inner p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="title mb-0">{{ Str::limit($book->title, 40) }}</h6>
                                    <span class="badge bg-{{ $book->status === 'accepted' ? 'success' : ($book->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($book->status) }}
                                    </span>
                                </div>
                                
                                @if($book->admin_notes)
                                <div class="alert alert-light mt-2 mb-0 p-2">
                                    <small class="text-muted"><strong>Notes:</strong> {{ Str::limit($book->admin_notes, 80) }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="nk-wg7">
                    <div class="nk-wg7-title">How It Works</div>
                    <div class="row g-gs">
                        <div class="col-md-4">
                            <div class="card card-full">
                                <div class="card-inner text-center">
                                    <div class="icon icon-circle icon-lg bg-primary-dim mb-3">
                                        <em class="icon ni ni-edit"></em>
                                    </div>
                                    <h6 class="title">1. Submit Your Book</h6>
                                    <p class="text-sm">Fill out the book submission form with all required details.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card card-full">
                                <div class="card-inner text-center">
                                    <div class="icon icon-circle icon-lg bg-warning-dim mb-3">
                                        <em class="icon ni ni-eye"></em>
                                    </div>
                                    <h6 class="title">2. Review Process</h6>
                                    <p class="text-sm">Our team will review your submission for quality and compliance.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card card-full">
                                <div class="card-inner text-center">
                                    <div class="icon icon-circle icon-lg bg-success-dim mb-3">
                                        <em class="icon ni ni-user"></em>
                                    </div>
                                    <h6 class="title">3. Become an Author</h6>
                                    <p class="text-sm">Once approved, you'll gain author access and can start selling!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Section -->
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="between-center flex-wrap flex-md-nowrap g-3">
                    <div class="nk-block-text">
                        <h6>Ready to Submit Your First Book?</h6>
                        <p class="text-soft">Click below to start the submission process</p>
                    </div>
                    <div class="nk-block-actions flex-shrink-sm-0">
                        <div class="justify-center">
                            <a href="{{ route('user.books.create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em>
                                <span>Add Book</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="card card-bordered mt-4">
            <div class="card-inner">
                <div class="nk-wg7">
                    <div class="nk-wg7-title">Frequently Asked Questions</div>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    What happens after I submit my book?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Our review team will examine your submission for quality, content appropriateness, and completeness. You'll receive a notification once the review is complete.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How long does the review process take?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Reviews typically take 3-5 business days. During peak periods, it may take slightly longer.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    What if my book is rejected?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>If your book doesn't meet our guidelines, you'll receive feedback on what needs to be improved. You can resubmit after making the necessary changes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .nk-block -->
</div>
@endsection