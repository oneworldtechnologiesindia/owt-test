<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Log;
use Illuminate\Support\Facades\Http;

/**
 * trait ZoomMeetingTrait
 */
trait ZoomMeetingTrait
{
    // private function getHeaders()
    // {
    //     $jwt = $this->generateZoomToken();

    //     return [
    //         'Authorization' => 'Bearer ' . $jwt,
    //         'Content-Type'  => 'application/json',
    //         'Accept'        => 'application/json',
    //     ];
    // }

    // public function generateZoomToken()
    // {
    //     $key = env('ZOOM_API_KEY', '');
    //     $secret = env('ZOOM_API_SECRET', '');
    //     $payload = [
    //         'iss' => $key,
    //         'exp' => strtotime('+1 minute'),
    //     ];

    //     return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    // }

    public function getZoomAccessToken()
    {
        $clientId = env('ZOOM_CLIENT_ID');
        $clientSecret = env('ZOOM_CLIENT_SECRET');
        $accountId = env('ZOOM_ACCOUNT_ID');

        $url = "https://zoom.us/oauth/token?grant_type=account_credentials&account_id=$accountId";

        // Request headers for basic auth
        $headers = [
            'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret"),
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        // Make the request to Zoom API to get the access token
        $response = Http::withHeaders($headers)->post($url);

        if ($response->successful()) {
            return $response->json()['access_token'];
        } else {
            return response()->json(['error' => 'Failed to get access token', 'message' => $response->body()], 500);
        }
    }
    private function retrieveZoomUrl()
    {
        return env('ZOOM_API_URL', '');
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        try {
            $date = new \DateTime($dateTime);

            return $date->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());

            return '';
        }
    }

    // public function createZoomMeeting($data)
    // {
    //     $path = 'users/me/meetings';

    //     $client = new Client();
    //     $headers = $this->getHeaders();
    //     $url = $this->retrieveZoomUrl();

    //     $body = [
    //         'headers' => $headers,
    //         'body'    => json_encode([
    //             'topic'      => $data['topic'],
    //             'type'       => self::MEETING_TYPE_SCHEDULE,
    //             'start_time' => $this->toZoomTimeFormat($data['start_time']),
    //             'duration'   => (isset($data['duration']))? $data['duration']:30,
    //             'agenda'     => (! empty($data['agenda'])) ? $data['agenda'] : null,
    //             'timezone'   => env('APP_TIMEZONE', 'Asia/Kolkata'),
    //             'settings'   => [
    //                 'host_video'        => 1,
    //                 'participant_video' => 1,
    //                 'waiting_room'      => 1,
    //                 'jbh_time'          => 0, //Allow participant to join anytime.
    //                 'auto_recording'    => "local" //Automatically record meeting on the local computer
    //             ],
    //         ]),
    //     ];

    //     $response =  $client->post($url.$path, $body);

    //     return [
    //         'success' => $response->getStatusCode() === 201,
    //         'data'    => json_decode($response->getBody(), true),
    //     ];
    // }

    public function createZoomMeeting($data)
    {
        // Step 1: Get access token
        $accessToken = $this->getZoomAccessToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Unable to get Zoom access token'], 500);
        }
        $path = 'users/me/meetings';

        $url = $this->retrieveZoomUrl();
        // Step 2: Define the meeting data
        $meetingData = [
            'topic'      => $data['topic'],
            'type'       => self::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($data['start_time']),
            'duration'   => (isset($data['duration'])) ? $data['duration'] : 30,
            'agenda'     => (! empty($data['agenda'])) ? $data['agenda'] : null,
            'timezone'   => env('APP_TIMEZONE', 'Asia/Kolkata'),
            'settings'   => [
                'host_video'        => 1,
                'participant_video' => 1,
                'waiting_room'      => 1,
                'jbh_time'          => 0, //Allow participant to join anytime.
                'auto_recording'    => "local" //Automatically record meeting on the local computer
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($url . $path, $meetingData);

        if ($response->successful()) {
            return
                [
                    'success' => $response->getStatusCode() === 201,
                    'data'    => json_decode($response->getBody(), true),
                ];
        } else {
            return response()->json(['error' => 'Failed to create meeting', 'message' => $response->body()], 500);
        }
    }

    // public function updateZoomMeeting($id, $data)
    // {
    //     $path = 'meetings/' . $id;

    //     $client = new Client();
    //     $headers = $this->getHeaders();
    //     $url = $this->retrieveZoomUrl();

    //     $body = [
    //         'headers' => $headers,
    //         'body'    => json_encode([
    //             'topic'      => $data['topic'],
    //             'type'       => self::MEETING_TYPE_SCHEDULE,
    //             'start_time' => $this->toZoomTimeFormat($data['start_time']),
    //             'duration'   => (isset($data['duration'])) ? $data['duration'] : 30,
    //             'agenda'     => (! empty($data['agenda'])) ? $data['agenda'] : null,
    //             'timezone'   => env('APP_TIMEZONE', 'Asia/Kolkata'),
    //             'settings'   => [
    //                 'host_video'        => 1,
    //                 'participant_video' => 1,
    //                 'waiting_room'      => 1,
    //                 'jbh_time'          => 0, //Allow participant to join anytime.
    //                 'auto_recording'    => "local" //Automatically record meeting on the local computer
    //             ],
    //         ]),
    //     ];

    //     $response =  $client->patch($url . $path, $body);

    //     return [
    //         'success' => $response->getStatusCode() === 204,
    //         'data'    => json_decode($response->getBody(), true),
    //     ];
    // }

    public function updateZoomMeeting($id, $data)
    {
        $accessToken = $this->getZoomAccessToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Unable to get Zoom access token'], 500);
        }
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();

        // Define the updated meeting data
        $updatedMeetingData = [
            'topic'      => $data['topic'],
            'type'       => self::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($data['start_time']),
            'duration'   => (isset($data['duration'])) ? $data['duration'] : 30,
            'agenda'     => (! empty($data['agenda'])) ? $data['agenda'] : null,
            'timezone'   => env('APP_TIMEZONE', 'Asia/Kolkata'),
            'settings'   => [
                'host_video'        => 1,
                'participant_video' => 1,
                'waiting_room'      => 1,
                'jbh_time'          => 0, //Allow participant to join anytime.
                'auto_recording'    => "local" //Automatically record meeting on the local computer
            ],
        ];

        $response = Http::withToken($accessToken)->patch($url . $path, $updatedMeetingData);
        if ($response->successful()) {
            return
            [
                'success' => $response->getStatusCode() === 204,
                'data'    => json_decode($response->getBody(), true),
            ];
        } else {
            return response()->json(['error' => 'Failed to update meeting', 'message' => $response->body()], 500);
        }
    }

    public function getZoomMeeting($id)
    {
        $client = new Client();
        $headers = $this->getHeaders();
        $url = $this->retrieveZoomUrl();

        $path = 'meetings/' . $id;

        $body = [
            'headers' => $headers,
            'body'    => json_encode([]),
        ];

        $response =  $client->get($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
            'data'    => json_decode($response->getBody(), true),
        ];
    }

    /**
     * @param string $id
     *
     * @return bool[]
     */
    // public function deleteZoomMeeting($id)
    // {
    //     $path = 'meetings/' . $id;

    //     $client = new Client();
    //     $headers = $this->getHeaders();
    //     $url = $this->retrieveZoomUrl();

    //     $body = [
    //         'headers' => $headers,
    //         'body'    => json_encode([]),
    //     ];

    //     $response =  $this->client->delete($url . $path, $body);

    //     return [
    //         'success' => $response->getStatusCode() === 204,
    //     ];
    // }

    public function deleteZoomMeeting($id)
    {
        $accessToken = $this->getZoomAccessToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Unable to get Zoom access token'], 500);
        }
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();

        $response = Http::withToken($accessToken)->delete($url . $path);

        return [
            'success' => $response->getStatusCode() === 204,
        ];
    }
}
