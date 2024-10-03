<?php

namespace DTApi\Http\Controllers;

use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use Exception;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Get all jobs allowed for user view
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getJobs(Request $request)
    {
        $user_id = $request->get('user_id');
        if (!empty($user_id)) {
            $response = $this->bookingRepository->getUsersJobs($user_id);
        } else {
            $user_type = $request->__authenticatedUser->user_type;
            $is_admin =  $user_type == config('app.role.adminID');
            $is_super_admin =  $user_type == config('app.role.superAdminID');

            if ($is_admin || $is_super_admin) {
                $response = $this->bookingRepository->getAllJobs($request);
            } else {
                throw new Exception('No jobs found');
            }
        }

        return response($response);
    }

    /**
     * Show job detail
     * @param $job_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getJobDetail($job_id)
    {
        $job = $this->bookingRepository->getJobDetail($job_id);

        if(empty($job)) {
            throw new Exception('No job found with job_id '.$job_id);
        }

        return response($job);
    }

    /**
     * Create a new job
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createJob(Request $request)
    {
        $validated_data = $request->validate([
           // data validations here
        ]);

        $response = $this->bookingRepository->createJob($request->__authenticatedUser, $validated_data);

        return response($response);
    }

    /**
     * Update job detail
     * @param $job_id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateJob($job_id, Request $request)
    {
        $validated_data = $request->validate([
            // data validations here
        ]);

        $authenticatedUser = $request->__authenticatedUser;
        $userData = array_except($validated_data, ['_token', 'submit']);
        $response = $this->bookingRepository->updateJob($job_id,$userData,$authenticatedUser);

        return response($response);
    }

    /**
     * Record job immediate email
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function immediateJobEmail(Request $request)
    {
        $validated_data = $request->validate([
            // data validations here
        ]);

        $response = $this->bookingRepository->storeJobEmail($validated_data);

        return response($response);
    }

    /**
     * Get all user job history
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getUserJobHistory(Request $request)
    {
        $validated_data = $request->validate([
            'user_id' => 'required'
            // other data validations here
        ]);
        
        $response = $this->bookingRepository->getUserJobHistory($validated_data['user_id'], $request);

        return response($response);
    }

    /**
     * Accept a job for authenticated user
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function acceptJob(Request $request)
    {
        $validated_data = $request->validate([
            // data validations here
        ]);

        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->acceptJob($validated_data, $user);

        return response($response);
    }

    /**
     * Cancel job post
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function cancelJob(Request $request)
    {
        $validated_data = $request->validate([
            // data validations here
        ]);

        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->cancelJob($validated_data, $user);

        return response($response);
    }

    /**
     * End job post
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function endJob(Request $request)
    {
        $validated_data = $request->validate([
            // data validations here
        ]);

        $response = $this->bookingRepository->endJob($validated_data);

        return response($response);

    }

    /**
     * Get all potential jobs or the current user
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function customerNotCall(Request $request)
    {
        $validated_data = $request->validate([
            // data validations here
        ]);

        $response = $this->bookingRepository->customerNotCall($validated_data);

        return response($response);

    }

    /**
     * Get all potential jobs or the current user
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getPotentialJobs(Request $request)
    {
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->getPotentialJobs($user);

        return response($response);
    }

    /**
     * Update job distance and admin comment
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
    */
    public function distanceFeed(Request $request)
    {
        $validated_data = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'distance' => 'nullable',
            'time' => 'nullable',
            'session_time' => 'nullable',
            'is_flagged' => 'required|boolean',
            'is_manually_handled' => 'required|boolean',
            'is_by_admin' => 'required|boolean',
            'admin_comments' => 'nullable|string',
        ]);
       
        $this->bookingRepository->updateJobDistance($validated_data['job_id'], [
            'distance' => $validated_data['distance'],
            'time' => $validated_data['time'],
        ]);

        $this->bookingRepository->updateAdminComment($validated_data['job_id'], [
            'admin_comments' => $validated_data['admin_comments'],
            'is_flagged' => $validated_data['is_flagged'] ? 'yes' : 'no',
            'session_time' => $validated_data['session_time'],
            'is_manually_handled' => $validated_data['is_manually_handled'] ? 'yes' : 'no',
            'is_by_admin' => $validated_data['is_by_admin'] ? 'yes' : 'no',
        ]);

        return response('Record updated!');
    }

    /**
     * Re-open a closed job 
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
    */
    public function reopenJob(Request $request)
    {
        $validated_data = $request->validate([
            // validate data here
        ]);
       
        $response = $this->bookingRepository->reopenJob($validated_data);

        return response($response);
    }

    /**
     * Resends job notification
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
    */
    public function resendNotifications(Request $request)
    {
        $validated_data = $request->validate([
            'job_id' => 'required'
        ]);

        $job = $this->bookingRepository->find($validated_data['job_id']);
        $job_data = $this->bookingRepository->jobToData($job);
        $this->bookingRepository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $validated_data = $request->validate([
            'job_id' => 'required'
        ]);
        
        $job = $this->bookingRepository->find($validated_data['job_id']);

        $this->bookingRepository->sendSMSNotificationToTranslator($job);

        return response(['success' => 'SMS sent']);
    }

}
