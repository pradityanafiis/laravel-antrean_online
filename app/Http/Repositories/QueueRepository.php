<?php

namespace App\Http\Repositories;

use App\Queue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QueueRepository
{
    private Queue $queue;

    public function __construct()
    {
        $this->queue = new Queue();
    }

    public function findByDate($serviceId, $date)
    {
        return $this->queue
            ->where('service_id', $serviceId)
            ->whereDate('schedule', $date)
            ->whereNotIn('status', ['expired', 'canceled'])
            ->count();
    }

    public function store(Request $request)
    {
        return $this->queue->create([
            'queue_number' => $request->queue_number,
            'user_id' => auth()->user()->id,
            'service_id' => $request->service_id,
            'schedule' => $request->schedule,
            'estimated_time_serve' => $request->estimated_time_serve,
            'status' => 'waiting'
        ]);
    }

    public function findActiveByUser() {
        return $this->queue
            ->with('service', 'service.merchant')
            ->where('user_id', auth()->user()->id)
            ->whereIn('status', ['waiting', 'active', 'hold'])
            ->oldest()
            ->get();
    }

    public function findHistoryByUser() {
        return $this->queue
            ->with('service', 'service.merchant')
            ->where('user_id', auth()->user()->id)
            ->whereIn('status', ['finish', 'expired', 'canceled'])
            ->oldest()
            ->get();
    }

    public function countWaitingByMerchant($serviceId) {
        return $this->queue
            ->whereIn('status', ['waiting', 'active'])
            ->whereIn('service_id', $serviceId)
            ->whereDate('schedule', Carbon::now()->toDateString())
            ->count();
    }

    public function findWaitingByMerchant($serviceId) {
        return $this->queue
            ->with(['service', 'user' => function ($query) {
                $query->select(['id', 'name', 'identity_number']);
            }])
            ->whereIn('status', ['waiting', 'active'])
            ->whereIn('service_id', $serviceId)
            ->whereDate('schedule', Carbon::now()->toDateString())
            ->oldest()
            ->get();
    }

    public function findHistoryByMerchant($serviceId) {
        return $this->queue
            ->with(['service', 'user' => function ($query) {
                $query->select(['id', 'name', 'identity_number']);
            }])
            ->whereIn('service_id', $serviceId)
            ->whereIn('status', ['finish', 'expired', 'canceled'])
            ->oldest()
            ->get();
    }

    public function updateStatus(Queue $queue, $status) {
        return $queue->update([
            'status' => $status
        ]);
    }

    public function updateStartTime(Queue $queue) {
        return $queue->update([
            'start_time_serve' => Carbon::now()->toTimeString()
        ]);
    }

    public function updateFinishTime(Queue $queue) {
        return $queue->update([
            'finish_time_serve' => Carbon::now()->toTimeString()
        ]);
    }

    public function findById($id) {
        return $this->queue
            ->with([
                'service',
                'service.merchant' => function ($query) {
                    $query->select(['id', 'name']);
                },
                'user' => function ($query) {
                    $query->select(['id', 'name', 'identity_number', 'firebase_token']);
                }
            ])->find($id);
    }
}
