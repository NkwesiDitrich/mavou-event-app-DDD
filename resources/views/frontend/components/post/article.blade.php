<!-- Begin Article
================================================== -->
<div class="container">
	<div class="row">

		<!-- Begin Post -->
		<div class="col-md-10 col-md-offset-1 col-xs-12">
			<div class="mainheading">

				<!-- Removed Top Meta section as per user request -->
				
				@if (\Session::has('success'))
					<div class="alert alert-success">
						<ul>
							<li>{!! \Session::get('success') !!}</li>
						</ul>
					</div>
				@endif
				
				<h1 class="posttitle">{{ $post->getTitle()->getValue() }}</h1>

			</div>

			<!-- Begin Featured Image - Reduced Size -->
			<img class="post-featured-image" src="{{ asset($post->getImage()) }}" alt="{{ $post->getTitle()->getValue() }}">
			<!-- End Featured Image -->

			<!-- Begin Event Details Section - Improved Layout -->
			<div class="event-details-section">
				<h3>
					<i class="fa fa-calendar-alt"></i>Event Details
				</h3>
				
				<div class="row">
					<div class="col-md-6">
						<div class="event-detail-item">
							<h5>
								<i class="fa fa-calendar"></i>Date
							</h5>
							<p>{{ $post->getDate()->getHumanReadableDate() }}</p>
						</div>
						
						@if($post->getTime()->getValue())
						<div class="event-detail-item">
							<h5>
								<i class="fa fa-clock-o"></i>Time
							</h5>
							<p>{{ $post->getTime()->getValue() }}</p>
						</div>
						@endif
						
						<div class="event-detail-item">
							<h5>
								<i class="fa fa-map-marker"></i>Location
							</h5>
							<p>{{ $post->getLocation()->getValue() }}</p>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="event-detail-item">
							<h5>
								<i class="fa fa-tag"></i>Category
							</h5>
							<p>{{ $post->categoryName ?? 'Uncategorized' }}</p>
						</div>
						
						<div class="event-detail-item">
							<h5>
								<i class="fa fa-user"></i>Organized By
							</h5>
							<p>{{ $post->organizerName ?? 'Unknown Organizer' }}</p>
						</div>
						
						<div class="event-detail-item">
							<h5>
								<i class="fa fa-info-circle"></i>Event Type
							</h5>
							<p>
								<span class="badge event-type-badge" style="background: {{ $post->getType()->getValue() == 'Feature' ? '#3b82f6' : '#1e3a8a' }};">
									{{ $post->getType()->getValue() }} Event
								</span>
							</p>
						</div>
					</div>
				</div>
			</div>
			<!-- End Event Details Section -->

			<!-- Begin Post Content -->
			<div class="article-post">
				<h3>About This Event</h3>
				<p>
					{{ $post->getDescription()->getValue() }}
				</p>
			</div>
			<!-- End Post Content -->

			<!-- Begin Event Registration Section -->
			<div class="event-registration-section">
				<div class="registration-call-to-action">
					<h4><i class="fa fa-ticket"></i> Register for This Event</h4>
					<p>Don't miss out on this amazing event! Register now to secure your spot.</p>
					<button type="button" class="btn btn-primary btn-lg register-btn" data-toggle="modal" data-target="#registrationModal">
						<i class="fa fa-user-plus"></i> Register Now
					</button>
				</div>
			</div>
			<!-- End Event Registration Section -->

		</div>
		<!-- End Post -->

	</div>
</div>
<!-- End Article
================================================== -->

<!-- Begin Registration Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="registrationModalLabel">
					<i class="fa fa-ticket"></i> Join this Event: {{ $post->getTitle()->getValue() }}
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Display validation errors -->
				@if ($errors->any())
					<div class="alert alert-danger">
						<ul style="margin-bottom: 0;">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				
				<!-- Display general error message -->
				@if (\Session::has('error'))
					<div class="alert alert-danger">
						{{ \Session::get('error') }}
					</div>
				@endif
				
				<form action="{{ url('/event-registration') }}" method="POST">
					@csrf
					<div class="form-group">
						<label class="form-label" for="name">Full Name *</label>
						<input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
							   placeholder="Enter your full name" value="{{ old('name') }}" required/>
						<input type="hidden" id="event_id" name="event_id" value="{{ $post->getId() }}"/>
						<input type="hidden" id="user_id" name="user_id" value="{{ $post->getUserId() }}"/>
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					
					<div class="form-group">
						<label class="form-label" for="mobile">Mobile Number</label>
						<input type="text" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror" 
							   placeholder="Enter your mobile number" value="{{ old('mobile') }}" />
						@error('mobile')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					
					<div class="form-group">
						<label class="form-label" for="email">Email Address *</label>
						<input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
							   placeholder="Enter your email address" value="{{ old('email') }}" required/>
						@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					
					<div class="form-group">
						<label class="form-label" for="remark">Additional Comments</label>
						<textarea id="remark" name="remark" rows="3" class="form-control @error('remark') is-invalid @enderror" 
								  placeholder="Any special requirements or comments...">{{ old('remark') }}</textarea>
						@error('remark')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					
					<button type="submit" class="btn btn-primary btn-block submit-registration-btn">
						<i class="fa fa-check"></i> Complete Registration
					</button>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<i class="fa fa-times"></i> Cancel
				</button>
			</div>
		</div>
	</div>
</div>
<!-- End Registration Modal -->

<style>
/* Post Page Enhancements with Royal Blue Theme */

.posttitle {
    color: #1e3a8a;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 30px;
    line-height: 1.3;
}

.post-featured-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 15px;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.event-details-section {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    padding: 35px;
    border-radius: 15px;
    margin-bottom: 40px;
    border-left: 5px solid #3b82f6;
    box-shadow: 0 2px 15px rgba(59, 130, 246, 0.1);
}

.event-details-section h3 {
    color: #1e3a8a;
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.event-details-section h3 i {
    color: #3b82f6;
}

.event-detail-item {
    margin-bottom: 20px;
}

.event-detail-item h5 {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #1e3a8a;
    margin-bottom: 8px;
}

.event-detail-item h5 i {
    color: #3b82f6;
    width: 18px;
    text-align: center;
    font-size: 1rem;
}

.event-detail-item p {
    margin-left: 26px;
    font-size: 1rem;
    color: #374151;
    font-weight: 500;
}

.event-type-badge {
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    padding: 8px 16px !important;
    border-radius: 20px !important;
    color: white !important;
    display: inline-block;
}

.article-post {
    background: white;
    padding: 35px;
    border-radius: 15px;
    margin-bottom: 40px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.article-post h3 {
    color: #1e3a8a;
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.article-post p {
    color: #374151;
    font-size: 1.1rem;
    line-height: 1.8;
}

/* Event Registration Section Styling */
.event-registration-section {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: white;
    padding: 40px;
    margin: 40px 0;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(30, 58, 138, 0.3);
}

.registration-call-to-action h4 {
    margin-bottom: 15px;
    font-size: 1.8rem;
    font-weight: 700;
    color: white;
}

.registration-call-to-action h4 i {
    margin-right: 10px;
    color: #fbbf24;
}

.registration-call-to-action p {
    font-size: 1.1rem;
    margin-bottom: 30px;
    opacity: 0.95;
}

.register-btn {
    padding: 15px 40px !important;
    font-size: 1.1rem !important;
    font-weight: 700 !important;
    border-radius: 50px !important;
    transition: all 0.3s ease !important;
    border: 3px solid white !important;
    background: white !important;
    color: #1e3a8a !important;
}

.register-btn:hover {
    background: transparent !important;
    color: white !important;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 255, 255, 0.3);
}

/* Modal Styling */
.modal-header {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: white;
    border-bottom: none;
    border-radius: 10px 10px 0 0;
    padding: 25px 30px;
}

.modal-header .modal-title {
    font-weight: 700;
    font-size: 1.3rem;
}

.modal-header .modal-title i {
    color: #fbbf24;
    margin-right: 10px;
}

.modal-header .close {
    color: white;
    opacity: 0.9;
    text-shadow: none;
    font-size: 2rem;
}

.modal-header .close:hover {
    opacity: 1;
}

.modal-body {
    padding: 35px;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #1e3a8a;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    padding: 12px 18px;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.submit-registration-btn {
    padding: 14px !important;
    font-weight: 700 !important;
    border-radius: 8px !important;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;
    border: none !important;
    font-size: 1.05rem !important;
    transition: all 0.3s ease !important;
}

.submit-registration-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 58, 138, 0.3);
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 20px 30px;
}

.modal-footer .btn-secondary {
    background: #6b7280;
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 600;
}

.modal-footer .btn-secondary:hover {
    background: #4b5563;
}

/* Form validation styling */
.is-invalid {
    border-color: #ef4444 !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #ef4444;
    font-weight: 500;
}

.alert {
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid transparent;
    border-radius: 8px;
}

.alert-danger {
    color: #991b1b;
    background-color: #fee2e2;
    border-color: #fecaca;
}

.alert-success {
    color: #065f46;
    background-color: #d1fae5;
    border-color: #a7f3d0;
}

.alert ul {
    padding-left: 1.2rem;
    margin-bottom: 0;
}

.alert li {
    margin-bottom: 0.25rem;
}

@media (max-width: 768px) {
    .posttitle {
        font-size: 1.8rem;
    }
    
    .event-details-section {
        padding: 25px;
    }
    
    .event-details-section h3 {
        font-size: 1.3rem;
    }
    
    .event-detail-item p {
        margin-left: 0;
        margin-top: 5px;
    }
    
    .event-detail-item h5 {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .event-registration-section {
        padding: 30px 20px;
        margin: 30px 0;
    }
    
    .registration-call-to-action h4 {
        font-size: 1.4rem;
    }
    
    .register-btn {
        padding: 12px 30px !important;
        font-size: 1rem !important;
    }
    
    .modal-body {
        padding: 25px;
    }
    
    .modal-footer {
        padding: 15px 25px;
    }
    
    .article-post {
        padding: 25px;
    }
    
    .article-post h3 {
        font-size: 1.3rem;
    }
    
    .article-post p {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .posttitle {
        font-size: 1.5rem;
    }
    
    .post-featured-image {
        max-height: 300px;
    }
    
    .event-details-section {
        padding: 20px;
    }
    
    .article-post {
        padding: 20px;
    }
}
</style>
