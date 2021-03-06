<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Repositories\BusinessHourRepository;
use App\Http\Repositories\MerchantRepository;
use App\Http\Repositories\QueueRepository;
use App\Http\Repositories\ServiceRepository;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QueueController extends Controller
{
    private ServiceRepository $serviceRepository;
    private QueueRepository $queueRepository;
    private MerchantRepository $merchantRepository;
    private BusinessHourRepository $businessHourRepository;
    private $currentTime;

    public function __construct()
    {
        $this->serviceRepository = new ServiceRepository();
        $this->queueRepository = new QueueRepository();
        $this->merchantRepository = new MerchantRepository();
        $this->businessHourRepository = new BusinessHourRepository();
        $this->currentTime = Carbon::now();
    }

    public function isAlreadyOpen($openTime)
    {
        if ($this->currentTime->greaterThanOrEqualTo($openTime))
            return true;
        else
            return false;
    }

    public function getQueueNumber($serviceId, $date)
    {
        return $this->queueRepository->countByDate($serviceId, $date);
    }

    public function isQueueAvailable($serviceId, $date) {
        $serviceQuota = $this->serviceRepository->findQuotaById($serviceId);
        if ($this->queueRepository->countByDate($serviceId, $date) < $serviceQuota['quota'])
            return true;
        else
            return false;
    }

    public function calculateEstimatedTimeServe($serviceId, $date)
    {
        $service = $this->serviceRepository->findById($serviceId);
        $queueNumber = $this->getQueueNumber($service->id, $date);
        $selectedDate = Carbon::createFromFormat('Y-m-d', $date);
        $selectedDay = $selectedDate->englishDayOfWeek;
        $openTime = Carbon::createFromFormat('H:i:s', $this->businessHourRepository->findByDay($service->merchant_id, $selectedDay)->open_time);
        if ($selectedDate->isToday() && $this->isAlreadyOpen($openTime)) {
            if ($queueNumber == 0) {
                return $this->currentTime->format('H:i');
            } else {
                $lastQueue = Carbon::createFromFormat('H:i:s', $this->queueRepository->findLastQueue($service->id, $date)->estimated_time_serve);
                return $lastQueue->addMinute($service->interval)->format('H:i');
            }
        } else {
            return $openTime->addMinute($queueNumber * $service->interval)->format('H:i');
        }
    }

    public function findByDate(Request $request)
    {
        $serviceId = $request->service_id;
        $selectedDate = $request->date;
        if ($this->isQueueAvailable($serviceId, $selectedDate)) {
            return response()->json([
                'error' => false,
                'message' => 'Success, queue detail retrieved!',
                'data' => [
                    'queue_number' => $this->getQueueNumber($serviceId, $selectedDate),
                    'estimated_time_serve' => $this->calculateEstimatedTimeServe($serviceId, $selectedDate)
                ]
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, queue schedule is full!',
                'data' => [
                    'queue_number' => $this->getQueueNumber($serviceId, $selectedDate)
                ]
            ]);
        }
    }

    public function store(Request $request) {
        $serviceId = $request->service_id;
        $selectedDate = $request->schedule;
        if ($this->isQueueAvailable($serviceId, $selectedDate)) {
            $queueNumber = $this->getQueueNumber($serviceId, $selectedDate);
            $request->request->add([
                'estimated_time_serve' => $this->calculateEstimatedTimeServe($serviceId, $selectedDate),
                'queue_number' => ++$queueNumber
            ]);
            return response()->json([
                'error' => false,
                'message' => 'Success, queue created!',
                'data' => $this->queueRepository->store($request)
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, queue schedule is full!',
                'data' => null
            ]);
        }
    }

    public function findActiveByUser() {
        $data = $this->queueRepository->findActiveByUser();
        if ($data->isNotEmpty()) {
            return response()->json([
                'error' => false,
                'message' => 'Success, active queue retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, active queue empty!',
                'data' => $data
            ]);
        }
    }

    public function findHistoryByUser() {
        $data = $this->queueRepository->findHistoryByUser();
        if ($data->isNotEmpty()) {
            return response()->json([
                'error' => false,
                'message' => 'Success, history queue retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, history queue empty!',
                'data' => $data
            ]);
        }
    }

    public function countWaitingByMerchant() {
        $serviceId = auth()->user()->merchant->services->pluck('id');
        $data = $this->queueRepository->countWaitingByMerchant($serviceId);
        if ($data > 0) {
            return response()->json([
                'error' => false,
                'message' => 'Success, count waiting queue retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, waiting queue empty!',
                'data' => $data
            ]);
        }
    }

    public function findWaitingByMerchant() {
        $serviceId = auth()->user()->merchant->services->pluck('id');
        $data = $this->queueRepository->findWaitingByMerchant($serviceId);
        if ($data->isNotEmpty()) {
            return response()->json([
                'error' => false,
                'message' => 'Success, waiting queue retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, waiting queue empty!',
                'data' => $data
            ]);
        }
    }

    public function findHistoryByMerchant() {
        $serviceId = auth()->user()->merchant->services->pluck('id');
        $data = $this->queueRepository->findHistoryByMerchant($serviceId);
        if ($data->isNotEmpty()) {
            return response()->json([
                'error' => false,
                'message' => 'Success, history queue retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, history queue empty!',
                'data' => $data
            ]);
        }
    }

    public function updateStatus(Request $request) {
        $queue = $this->queueRepository->findById($request->queue_id);
        if (!empty($queue)) {
            if ($this->queueRepository->updateStatus($queue, $request->status)) {
                $merchantName = $queue->service->merchant->name;
                $serviceName = $queue->service->name;
                $token = $queue->user->firebase_token;

                if ($request->status == 'active') {
                    $this->queueRepository->updateStartTime($queue);
                    $this->sendNotification($token, "Notifikasi Pelayanan", "Pelayanan $serviceName di $merchantName sedang dimulai.");
                } elseif ($request->status == 'finish') {
                    $this->queueRepository->updateFinishTime($queue);
                    $this->sendNotification($token, "Notifikasi Pelayanan", "Pelayanan $serviceName di $merchantName selesai, semoga anda puas dengan pelayanan yang kami berikan.");
                }

                return response()->json([
                    'error' => false,
                    'message' => 'Success, queue status updated!',
                    'data' => $queue
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Failed, unable to update queue status!',
                    'data' => $queue
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, queue not found!',
                'data' => $queue
            ]);
        }
    }

    public function findByQRCode(Request $request) {
        $queue = $this->queueRepository->findById($request->id);
        $service = $this->serviceRepository->findById($queue->service_id);
        if ($queue != null) {
            if ($this->isValidMerchant($service->merchant_id)) {
                if ($queue->schedule != Carbon::now()->toDateString()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Gagal, antrean bukan untuk hari ini!',
                        'data' => null
                    ]);
                } else {
                    return response()->json([
                        'error' => false,
                        'message' => 'Success, queue retrieved!',
                        'data' => $queue
                    ]);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Gagal, antrean bukan milik anda!',
                    'data' => null
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Gagal, antrean tidak ditemukan!',
                'data' => null
            ]);
        }
    }

    public function isValidMerchant($id) {
        if ($id != auth()->user()->merchant->id) {
            return false;
        } else {
            return true;
        }
    }

    public function sendNotification($to, $title, $body) {
        return Http::asJson()->withToken('AAAAYHljUqM:APA91bGtyyK8yynvt6pyKqWKDnDBeIHB1KMZUao2fr7Xm_yIViGEK3I7iC9hu_p4A7RUJoJNZhvXLLMnDRsEjneQPuX9P3469O2P3SwnJiKuvod250a1BzfXwrocJhSwV_ftYXjnZbiB')
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $to,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ]
            ]);
    }
}
