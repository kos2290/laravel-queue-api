<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessQueueJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;

class QueueController extends Controller
{
    /**
     * Add a new item to the queue
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

    /**
     * Deletes and returns the first element from the queue (if found)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dequeue()
    {
        $connection = config('queue.default');
        $firstJob   = Queue::connection($connection)->pop();

        if ($firstJob) {
            $payload  = json_decode($firstJob->getRawBody(), true);
            $jobClass = $payload['job'] ?? null;
            $data     = $payload['data'] ?? [];

            // Remove item from queue
            $firstJob->delete();

            return response()->json([
                'message' => 'Item is removed from the queue',
                'job'     => $jobClass,
                'data'    => $data,
            ], 200);
        } else {
            return response()->json(['message' => 'The queue is empty'], 204);
        }
    }
}
