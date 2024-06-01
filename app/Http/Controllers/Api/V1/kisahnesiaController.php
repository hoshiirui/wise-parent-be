<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\kisahnesia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function allStory(){
        $storylists = kisahnesia::all();

        return response()->json([
            'message' => 'Succesfully retreived story datas!',
            'story' => $storylists
        ], 200);
    }

    public function aStory(string $slug){
        $storylists = kisahnesia::where('slug', $slug)->first();

        return response()->json([
            'message' => 'Succesfully retreived story datas!',
            'story' => $storylists
        ], 200);
    }
}