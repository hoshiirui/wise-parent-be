<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\kisahnesia;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class kisahnesiaController extends Controller
{
    public function newStory(Request $request){
        $user = Auth::user();

        if($user === null){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found!'
            ], 403);
        }  

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Validate the image (optional)
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,bmp,gif|max:2048', // Adjust validation rules as needed
            ]);

            $path = $file->store('public/storythumbnail'); // Stores file and returns path
            $filename = $file->hashName(); // Get the hashed file name

            // Create post attachment (assuming post_attachments model)
            
            $storage_path = "storythumbnail/" . $filename;
        }else{
            $storage_path = "storythumbnail/story.jpg";
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        $kisahnesia = kisahnesia::create([
            'title' => $request->title,
            'writer' => $request->writer, 
            'description' => $request->description,
            'content' => $request->content, 
            'slug' => Str::slug($request->title),
            'tags' => $request->tags,
            'thumbnail' => $storage_path
        ]);

        if($kisahnesia){
            return response()->json([
                'message' => 'Story successfully created!',
            ], 201);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create story!',
            ], 400);
        }
    }

    public function allStory(Request $request)
    {
        $tags = $request->get('tags'); //optional tags
        $search = $request->get('search'); // Optional search term
        $sortBy = $request->get('sort', 'title'); // Default sort by title
        $orderBy = $request->get('by', 'asc'); // Default order ascending
        $limit = $request->get('limit', 10); // Default limit of 10 stories
        $page = $request->get('page', 1); //get what page is that

        $storylists = kisahnesia::select('*'); // Select all columns

        if ($tags) {
            $tagsArray = explode(',', $tags); // Split comma-separated tags
            $storylists->where(function ($query) use ($tagsArray) {
                foreach ($tagsArray as $tag) {
                    $query->where('tags', 'like', "%{$tag}%"); // Filter by partial tag match
                }
            });
        }

        if ($search) {
            $storylists = $storylists->where('title', 'like', "%{$search}%")
                                ->orWhere('content', 'like', "%{$search}%"); // Search title and content
        }

        $storylists = $storylists->orderBy($sortBy, $orderBy);

        if ($limit) {
            $storylists = $storylists->paginate($limit, $page);
        } else {
            $storylists = $storylists->get(); // Retrieve all stories if no limit provided
        }

        return response()->json([
            'message' => 'Successfully retrieved story datas!',
            'story' => $storylists,
        ], 200);
    }

    public function aStory(string $slug)
    {
        try {
            $story = Kisahnesia::where('slug', $slug)->firstOrFail();

            return response()->json([
                'message' => 'Successfully retrieved story data!',
                'story' => $story,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Story not found.',
            ], 404);
        } catch (Exception $e) {
            // Log the unexpected exception for debugging purposes
            Log::error('Unexpected error retrieving story: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while retrieving the story.',
            ], 500);
        }
    }
}
