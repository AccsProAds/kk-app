<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogFile;
use App\Libraries\FileExtractLibrary;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Lead;

class processLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-leads {--batch=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the leads on each file and save them on the db';


    /**
     * Execute the console command.
     */
    public function handle()
    {

        $batchSize =  $this->option('batch');

        $unprocessedFiles = LogFile::where('leads_exported', false)
        ->whereNotNull('data')
        ->where('is_processing', false)
        ->take($batchSize)
        ->get();

        $table = [];

       
        foreach($unprocessedFiles as $file)
        {
            $total = 0;
            $data = json_decode($file->data);

            $file->is_processing = true;
            $file->save();

            $total_leads_db = count($data);

            $this->info("Processing file ".$file->file_path. "/ total leads = ".$total_leads_db);

            
            $bar = $this->output->createProgressBar($total_leads_db);
            $bar->start();

            //dd($data);
            foreach($data as $lead_data)
            {

                $bar->advance();
                if(isset($lead_data->cardNumber) && isset($lead_data->cardMonth) && isset($lead_data->cardYear) && isset($lead_data->cardSecurityCode)) {

                    $existingLead = Lead::where('card_number', $lead_data->cardNumber)
                    ->where('card_month', $lead_data->cardMonth)
                    ->where('card_year', $lead_data->cardYear)
                    ->where('card_cvv', $lead_data->cardSecurityCode)
                    ->first();

                    if (!$existingLead) {
                        $lead = new Lead();
                        $lead->log_file_id = $file->id;
                        $lead->first_name = $lead_data->firstName ?? '';
                        $lead->last_name = $lead_data->lastName ?? '';
                        $lead->email = $lead_data->emailAddress ?? '';
                        $lead->phone = $lead_data->phoneNumber;
                        $lead->address_1 = $lead_data->address1 ?? '';
                        $lead->address_2 = $lead_data->address2 ?? '';
                        $lead->city = $lead_data->city ?? '';
                        $lead->state = $lead_data->state ?? '';
                        $lead->zip = $lead_data->zip ?? $lead_data->postalCode ?? '';
                        $lead->country = $lead_data->country ?? '';
                        $lead->ip = $lead_data->ipAddress ?? '';
                        $lead->card_month = $lead_data->cardMonth ?? '';
                        $lead->card_year = $lead_data->cardYear ?? '';
                        $lead->card_number = $lead_data->cardNumber ?? '';
                        $lead->card_cvv = $lead_data->cardSecurityCode ?? '';
                        $lead->creditcard_type = $lead_data->creditCardType;
                        $lead->declined = isset($lead_data->decline_message) ? true : false;
                        
                        if (isset($lead_data->lead_time)) {
                            $lead->lead_time = Carbon::parse($lead_data->lead_time);
                        }

                        $lead->lead_url = '';
                        $lead->aff_id = $lead_data->affid ?? '';
                        $lead->pub = $lead_data->sid ?? '';
                        $lead->click_id = $lead_data->click_id ?? '';
                        $lead->c1 = $lead_data->c1 ?? '';
                        $lead->c2 = $lead_data->c2 ?? '';
                        $lead->c3 = $lead_data->c3 ?? '';


                        $lead->save();
                        $total++;
                    }
                }

               

            }
            $file->leads_exported = true;
            $file->is_processing = false;
            $file->save();

            $table[] = [
                "file" => $file->file_path,
                "total" => $total,
                "dups" =>  $total_leads_db - $total
            ];

            $bar->finish();
            $this->newLine();
        }


        if(empty($table)) {
            $this->info("No files to process");
        } else {
            $this->newLine(2);
            $this->table(
                ['File', 'Total Saved', 'Duplicated'],
                $table
            );
        }

        
    }
}
