<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessQueueJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QueueController extends Controller
{
    /**
     * Add new element in queue
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enqueue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'x' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '"x" parameter is required'], 422);
        }

        // Send job in queue
        ProcessQueueJob::dispatch($request->x);

        return response()->json(['message' => 'Item "x" added in queue'], 202);
    }
}
