<?php

namespace App\Jobs;

use App\Models\Lead2External;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLeadUpRev implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lead2External;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lead2External $lead2External)
    {
        $this->lead2External = $lead2External;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lead = $this->lead2External->lead;
        $service = $this->lead2External->external_service;


        $endpoint = "https://api.uprev.io/";
        $offer_url = "https://exquisitejackpotnexus.com/becc-1u-ia/checkout";

        $api_key = "9419f06a-57d1-4a71-b185-1075158cf646";
        
        // Prepare the data to be sent to the external API
        $data = [
            "first_name" => $lead->first_name,
            "last_name" => $lead->last_name,
            "address_1" => $lead->address_1,
            "address_2" => $lead->address_2,
            "city" => $lead->city,
            "state" => $lead->state,
            "postal_code" => $lead->zip,
            "country" => $lead->country,
            "phone_number" => $lead->phone,
            "email" => $lead->email,
            "card_number" => $lead->card_number,
            "card_expiry_month" => $lead->card_month,
            "card_expiry_year" => $lead->card_year,
            "card_cvv" => $lead->card_cvv,
            "utm_source" => $lead->aff_id
        ];

        // Send the data to the external API
        $client = new Client([
            'base_uri' => $endpoint,
            'timeout'  => 15,
        ]);

        try {
            $response = $client->post('subscriber/new', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data
            ]);

            $responseBody = json_decode($response->getBody(), true);

            $this->lead2External->request = $data;
            $this->lead2External->response = ["endpoint" => $endpoint, 'response' => $responseBody];
            $this->lead2External->save();

            //Log::info('Lead processed successfully', ['lead_id' => $this->lead2External->lead_id, 'request' => $data, 'response' => $responseBody]);
        } catch (\Exception $e) {
            Log::error('Error processing lead', ['lead_id' => $this->lead2External->lead_id, 'error' => $e->getMessage()]);
        }
    }
}
