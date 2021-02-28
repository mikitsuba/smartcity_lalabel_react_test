<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PrintService;

class PrintController extends Controller
{
    protected $printservice;

    public function __construct(PrintService $printservice)
    {
        $this->printservice = $printservice;
    }

    public function execute_print(Request $request) {
        $auth_result = $this->printservice->authenticate($request->client_id, $request->secret, $request->device);

        $subject_id = $auth_result['Response']['Body']['subject_id'];
        $access_token = $auth_result['Response']['Body']['access_token'];
        $job_result = $this->printservice->createJob($subject_id, $access_token);

        $upload_uri = $job_result['Response']['Body']['upload_uri'];
        $this->printservice->uploadFile($upload_uri);

        $job_id = $job_result['Response']['Body']['id'];
        $result = $this->printservice->print($subject_id, $job_id, $access_token);
    }
}
