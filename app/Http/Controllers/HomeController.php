<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Registration;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\EventRegistrationRequest;
use App\Application\Queries\GetUserEventsQuery;
use App\Application\Handlers\GetUserEventsHandler;
use App\Infrastructure\Persistence\EloquentEventRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    private $eventRepository;
    private $getUserEventsHandler;

    public function __construct(
        EloquentEventRepository $eventRepository,
        GetUserEventsHandler $getUserEventsHandler
    ) {
        $this->eventRepository = $eventRepository;
        $this->getUserEventsHandler = $getUserEventsHandler;
    }

    function IndexPage(): View
    {
        try {
            // Get featured events using DDD repository
            $featurEvents = $this->eventRepository->getEventsByType('Feature', 4);
            
            // Get recent events using DDD repository with pagination
            $recentEvents = $this->eventRepository->getEventsByTypeWithPagination('Recent', 6);
            
            // Fetch user and category names for events
            $featurEvents = $this->enrichEventsWithNames($featurEvents);
            $recentEvents = $this->enrichEventsWithNames($recentEvents);
            
            return view('frontend.pages.index-page', compact('featurEvents', 'recentEvents'));
        } catch (\Exception $e) {
            Log::error('Error in IndexPage: ' . $e->getMessage());
            
            // Return view with empty arrays if database fails
            $featurEvents = [];
            $recentEvents = [];
            
            return view('frontend.pages.index-page', compact('featurEvents', 'recentEvents'))
                ->with('error', 'Some events may not be available at the moment. Please try again later.');
        }
    }

    public function PostPage($id)
    {
        try {
            // Load event using DDD repository
            $post = $this->eventRepository->findByIdWithRelations($id);
            
            if (!$post) {
                abort(404, 'Event not found');
            }
            
            // Enrich the post with user and category names
            $post = $this->enrichEventWithNames($post);

            // Note: Removed related events as per user request
            // $relatedEvents = $this->eventRepository->getRelatedEvents(
            //     $post->getCategoryId(), 
            //     $id, 
            //     3
            // );
                
            return view('frontend.pages.post-page', compact('post'));
        } catch (\Exception $e) {
            Log::error('Error in PostPage: ' . $e->getMessage());
            
            // If database error, show error page or redirect
            return redirect()->route('home')->with('error', 'Unable to load event details. Please try again later.');
        }
    }
    
    /**
     * NEW: AJAX endpoint to check email availability for an event
     */
    public function checkEmailAvailability(Request $request): JsonResponse
    {
        try {
            $email = $request->input('email');
            $eventId = $request->input('event_id');
            
            // Validate inputs
            if (empty($email) || empty($eventId)) {
                return response()->json([
                    'available' => true,
                    'message' => ''
                ]);
            }
            
            // Check if email is already registered for this event
            $isRegistered = $this->isEmailAlreadyRegistered($email, $eventId);
            
            if ($isRegistered) {
                return response()->json([
                    'available' => false,
                    'message' => 'This email has already been used, please use another email'
                ]);
            }
            
            return response()->json([
                'available' => true,
                'message' => ''
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking email availability: ' . $e->getMessage());
            
            // In case of error, allow registration to proceed
            return response()->json([
                'available' => true,
                'message' => ''
            ]);
        }
    }
    
    function EventRegistration(EventRegistrationRequest $request)
    {
        try {
            // The validation is now handled by EventRegistrationRequest
            // which includes the Rule::unique('registrations')->where('event_id', $eventId) validation
            
            // Create the registration - validation has already passed
            Registration::create([
                'date' => now()->toDateString(),
                'name' => $request->validated()['name'],
                'mobile' => $request->validated()['mobile'],
                'email' => $request->validated()['email'],
                'remark' => $request->validated()['remark'],
                'event_id' => $request->validated()['event_id'],
                'user_id' => $request->validated()['user_id']
            ]);

            return redirect()->back()->with('success', 'Your Registration Confirmed Successfully!');
        } catch (\Exception $e) {
            Log::error('Error in EventRegistration: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again later.');
        }
    }

    /**
     * Check if email is already registered for a specific event
     * 
     * @param string $email
     * @param int $eventId
     * @return bool
     */
    private function isEmailAlreadyRegistered(string $email, int $eventId): bool
    {
        try {
            // Check if the email already exists for this specific event
            $existingRegistration = DB::table('registrations')
                ->where('email', $email)
                ->where('event_id', $eventId)
                ->first();
            
            return $existingRegistration !== null;
        } catch (\Exception $e) {
            Log::error('Error checking email registration: ' . $e->getMessage());
            // In case of database error, allow registration to proceed
            return false;
        }
    }

    /**
     * Enrich events array with user and category names
     */
    private function enrichEventsWithNames(array $events): array
    {
        foreach ($events as $event) {
            $this->enrichEventWithNames($event);
        }
        return $events;
    }

    /**
     * Enrich single event with user and category names
     */
    private function enrichEventWithNames($event)
    {
        try {
            // Get user name
            $user = User::find($event->getUserId());
            if ($user) {
                $event->organizerName = trim($user->firstName . ' ' . $user->lastName);
                if (empty($event->organizerName)) {
                    $event->organizerName = $user->email; // Fallback to email if no name
                }
            } else {
                $event->organizerName = 'Unknown Organizer';
            }

            // Get category name
            $category = Category::find($event->getCategoryId());
            if ($category) {
                $event->categoryName = $category->name;
            } else {
                $event->categoryName = 'Uncategorized';
            }
        } catch (\Exception $e) {
            Log::error('Error enriching event with names: ' . $e->getMessage());
            $event->organizerName = 'Unknown Organizer';
            $event->categoryName = 'Uncategorized';
        }

        return $event;
    }
}
