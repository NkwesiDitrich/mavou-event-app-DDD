<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Category;
use App\Infrastructure\Persistence\EloquentEventRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AllEventsController extends Controller
{
    private $eventRepository;

    public function __construct(EloquentEventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display the all events page
     */
    public function index(): View
    {
        try {
            // Get all categories for the filter dropdown
            $categories = Category::orderBy('name', 'asc')->get();
            
            return view('frontend.pages.all-events-page', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Error in AllEventsController@index: ' . $e->getMessage());
            
            // Return view with empty categories if database fails
            $categories = [];
            return view('frontend.pages.all-events-page', compact('categories'))
                ->with('error', 'Some features may not be available at the moment. Please try again later.');
        }
    }

    /**
     * API endpoint to fetch events with filtering and pagination
     */
    public function getEvents(Request $request): JsonResponse
    {
        try {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 12);
            $search = $request->input('search', '');
            $categoryId = $request->input('category', '');
            $location = $request->input('location', '');
            $type = $request->input('type', '');

            // Validate pagination parameters
            if ($page < 1) $page = 1;
            if ($perPage < 1 || $perPage > 50) $perPage = 12;

            // Build query
            $query = DB::table('events');

            // Apply search filter (name/title)
            if (!empty($search)) {
                $searchTerm = '%' . trim($search) . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', $searchTerm)
                      ->orWhere('description', 'LIKE', $searchTerm)
                      ->orWhere('teaser', 'LIKE', $searchTerm);
                });
            }

            // Apply category filter
            if (!empty($categoryId)) {
                $query->where('categorie_id', $categoryId);
            }

            // Apply location filter
            if (!empty($location)) {
                $locationTerm = '%' . trim($location) . '%';
                $query->where('location', 'LIKE', $locationTerm);
            }

            // Apply type filter
            if (!empty($type)) {
                $query->where('type', $type);
            }

            // Get total count for pagination
            $totalCount = $query->count();

            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $events = $query->orderBy('date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->offset($offset)
                           ->limit($perPage)
                           ->get();

            // Transform events to array format
            $eventsArray = [];
            foreach ($events as $event) {
                // Get user name
                $user = User::find($event->user_id);
                $organizerName = 'Unknown Organizer';
                if ($user) {
                    $organizerName = trim($user->firstName . ' ' . $user->lastName);
                    if (empty($organizerName)) {
                        $organizerName = $user->email;
                    }
                }

                // Get category name
                $category = Category::find($event->categorie_id);
                $categoryName = $category ? $category->name : 'Uncategorized';

                // Format date
                $date = date('M d, Y', strtotime($event->date));

                $eventsArray[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'teaser' => $event->teaser ?? null,
                    'description' => $event->description,
                    'date' => $date,
                    'time' => $event->time ?? 'Time TBA',
                    'location' => $event->location,
                    'type' => $event->type,
                    'image' => asset($event->image),
                    'organizer' => $organizerName,
                    'category' => $categoryName,
                    'category_id' => $event->categorie_id,
                    'url' => url('/post/' . $event->id)
                ];
            }

            // Check if there are more events
            $hasMore = ($offset + $perPage) < $totalCount;

            return response()->json([
                'success' => true,
                'events' => $eventsArray,
                'has_more' => $hasMore,
                'total' => $totalCount,
                'current_page' => $page,
                'per_page' => $perPage
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AllEventsController@getEvents: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load events. Please try again later.',
                'events' => [],
                'has_more' => false,
                'total' => 0
            ], 500);
        }
    }
}
