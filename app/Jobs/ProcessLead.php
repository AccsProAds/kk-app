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

class ProcessLead implements ShouldQueue
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

        switch($service) {
            case 'usadc':
                $endpoint = "https://api.usadc-core.com/";
                $offer_url = "https://superdealentry.com/cc-22sg/checkout";
                $campaign_id = 831;
                $product_id = 840;
                $user = "azure_usadc_api";
                $pass = "y58tUqFFef3sAj";
            break;
            default:
                $endpoint = "https://api.fxr-core.com/";
                $offer_url = "https://exquisitejackpotnexus.com/becc-1u-ia/checkout";
                $campaign_id = 830;
                $product_id = 841;
                $user = "azure_fxr_api";
                $pass = "KTMBBtsBCVm9n";
        }
        // Prepare the data to be sent to the external API
        $data = [
            "billingFirstName" => $lead->first_name,
            "billingLastName" => $lead->last_name,
            "billingAddress1" => $lead->address_1,
            "billingAddress2" => $lead->address_2,
            "billingCity" => $lead->city,
            "billingState" => $lead->state,
            "billingZip" => $lead->zip,
            "billingCountry" => $lead->country,
            "phone" => $lead->phone,
            "email" => $lead->email,
            "creditCardType" => strtoupper($lead->creditcard_type),
            "creditCardNumber" => $lead->card_number,
            "expirationDate" => $lead->card_month . $lead->card_year,
            "CVV" => $lead->card_cvv,
            "ipAddress" => $lead->ip,
            "campaignId" => $campaign_id, // Hardcoded as per requirements
            "product_id" => $product_id, // Hardcoded as per requirements
            "notes" => $offer_url."?AFFID=" . $lead->aff_id . "&C1=" . $lead->c1 . "&C2=" . $lead->c2 . "&C3=" . $lead->c3 ."&click_id=" . $lead->click_id,
            "AFFID" => $lead->aff_id,
            "C1" => $lead->c1,
            "C2" => $lead->c2,
            "C3" => $lead->c3,
            "click_id" => $lead->click_id
        ];

        // Send the data to the external API
        $client = new Client([
            'base_uri' => $endpoint,
            'timeout'  => 15,
        ]);

        try {
            $response = $client->post('api/v1/new_order', [
                'auth' => [$user, $pass],
                'json' => $data
            ]);

            $responseBody = json_decode($response->getBody(), true);

            $this->lead2External->request = $data;
            $this->lead2External->response = ["endpoint" => $endpoint, 'response' => $responseBody];
            $this->lead2External->save();

            // Log::info('Lead processed successfully', ['lead_id' => $this->lead2External->lead_id, 'request' => $data, 'response' => $responseBody]);
        } catch (\Exception $e) {
            Log::error('Error processing lead', ['lead_id' => $this->lead2External->lead_id, 'error' => $e->getMessage()]);
        }
    }
}
