<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\tag;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class tagController extends Controller
{
    //

    public function allTags(Request $request){
        $type = $request->get('type', 'all'); 
              
        try {
            if($type == 'all'){
                $tag = tag::all();
            }else{
                $tag = tag::where('type', $type)->get();
            }
            
            return response()->json([
                'message' => 'Successfully retrieved tags data!',
                'tags' => $tag,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No tag found.',
            ], 404);
        } catch (Exception $e) {
            // Log the unexpected exception for debugging purposes
            Log::error('Unexpected error retrieving tags: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while retrieving the tag.',
            ], 500);
        } 
    }
}
