<!-- Begin List Posts
	================================================== -->
	<section class="recent-posts">
        <div class="section-title">
            <h2><span>Recents</span></h2>
        </div>
        <div class="events-grid">
            @foreach ($recentEvents as $recent )
                 <!-- begin post -->
                <div class="event-card-container">
                    <div class="event-card-image">
                        <a href="{{ url('/post'.'/'.$recent->getId())}}">
                            <img src="{{ asset($recent->getImage())}}" alt="{{ $recent->getTitle()->getValue() }}">
                        </a>
                    </div>
                    <div class="event-card-details">
                        <h3 class="event-card-title">
                            <a href="{{ url('/post'.'/'.$recent->getId())}}">{{ $recent->getTitle()->getValue() }}</a>
                        </h3>
                        @if($recent->hasTeaser())
                            <div class="event-card-teaser">
                                {{ $recent->getTeaser()->getValue() }}
                            </div>
                        @endif
                        <div class="event-card-info">
                            <div class="event-info-item">
                                <i class="fa fa-calendar"></i>
                                <span>{{ $recent->getDate()->getHumanReadableDate() }}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fa fa-clock-o"></i>
                                <span>{{ $recent->getTime()->getValue() ?: 'Time TBA' }}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fa fa-map-marker"></i>
                                <span>{{ $recent->getLocation()->getValue() }}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fa fa-user"></i>
                                <span>{{ $recent->organizerName ?? 'Unknown Organizer' }}</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <a href="{{ url('/post'.'/'.$recent->getId())}}" class="event-card-read-more" title="View Event Details">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                <!-- end post -->
            @endforeach
           
        </div>
        
        <!-- See More Button -->
        <div class="see-more-container" style="text-align: center; margin-top: 30px; margin-bottom: 40px;">
            <a href="{{ url('/events/all') }}" class="btn-see-more">
                <span>See More Events</span>
                <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
        </div>
        
        </section>
        <!-- End List Posts
        ================================================== -->
