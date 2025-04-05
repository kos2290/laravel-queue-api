<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessQueueJob;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $validator = Validator::make($request->all(), ['x' => 'required']);

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function dequeue()
    {
        if ($firstJob = $this->getFirstJob(true)) {
            $payload  = $this->getJobPayload($firstJob);

            // Remove item from queue
            $firstJob->delete();

            return response()->json([
                'message' => 'Item is removed from the queue',
                'job'     => $payload['job'],
                'data'    => $payload['data'],
            ], 200);
        } else {
            return response()->json(['message' => 'The queue is empty'], 204);
        }
    }

    /**
     * Get the first element from the queue (if found)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function front()
    {
        if ($firstJob = $this->getFirstJob()) {
            $payload  = $this->getJobPayload($firstJob);

            return response()->json([
                'message' => 'Item is retrieved from the queue',
                'job'     => $payload['job'],
                'data'    => $payload['data'],
            ], 200);
        } else {
            return response()->json(['message' => 'The queue is empty'], 204);
        }
    }

    /**
     * Get the first element from the queue (if found)
     * Alias of "front"
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function peek()
    {
        return $this->front();
    }

    /**
     * Retrieves the first job from the queue
     *
     * @return \Illuminate\Contracts\Queue\Job|null|array
     */
    private function getFirstJob(bool $retrieveAndRemove = false)
    {
        $connection = config('queue.default');

        if ($retrieveAndRemove) {
            $firstJob = Queue::connection($connection)->pop();
        } else {
            $queueName = config('queue.connections.' . $connection . '.queue', 'default');
            $table     = config('queue.connections.' . $connection . '.table', 'jobs');

            $firstJob = (array) DB::table($table)
                ->where('queue', $queueName)
                ->orderBy('id', 'asc')
                ->lockForUpdate() // Prevent another worker from processing while we're watching
                ->first();
        }

        return $firstJob;
    }

    /**
     * Retrieves the payload of a job
     *
     * @param \Illuminate\Contracts\Queue\Job|array $job
     * @return array<string, mixed>
     */
    private function getJobPayload($job)
    {
        if (!$job) {
            return [];
        }

        $payload  = json_decode($job instanceof Job ? $job->getRawBody() : $job['payload'], true);

        $payload['job']  = $payload['job'] ?? null;
        $payload['data'] = $payload['data'] ?? [];

        return $payload;
    }
}
