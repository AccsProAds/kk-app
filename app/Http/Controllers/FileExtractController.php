<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileExtractController extends Controller
{
    public function extractAndParseFile($filePath)
    {
        // Function to extract .tsv.gz files
        function extractTsvGz($filePath)
        {
            $bufferSize = 4096; // Read buffer size
            $outFileName = str_replace('.gz', '', $filePath);

            $file = gzopen(Storage::path($filePath), 'rb');
            $outFile = fopen(Storage::path($outFileName), 'wb');

            while (!gzeof($file)) {
                fwrite($outFile, gzread($file, $bufferSize));
            }

            fclose($outFile);
            gzclose($file);

            return $outFileName;
        }

        // Function to parse the extracted files
        function parseFile($filePath)
        {
            $lines = file(Storage::path($filePath));
            $extracting = false;
            $parsedData = [];
            $currentBlock = [];
            $skipFields = ['custom1', 'custom2', 'custom3', 'product1_id', 'affId', 'c1', 'c2', 'c3', 'sid', 'click_id'];

            foreach ($lines as $line) {
                $columns = explode("\t", $line);
                if (count($columns) > 1) {
                    $logMessage = trim(end($columns));
                    if ($logMessage == 'Incoming KK  {}') {
                        $extracting = true;
                        $currentBlock = [];
                        continue;
                    }

                    if ($extracting) {
                        if ($logMessage == '}') {
                            $extracting = false;
                            $parsedData[] = $currentBlock;
                        } else {
                            // Skip lines containing unwanted fields
                            $skip = false;
                            foreach ($skipFields as $field) {
                                if (strpos($logMessage, $field) !== false) {
                                    $skip = true;
                                    break;
                                }
                            }
                            if (!$skip) {
                                // Handle decline_message field separately
                                if (strpos($logMessage, 'decline_message') !== false) {
                                    if (strpos($logMessage, 'undefined') !== false) {
                                        $currentBlock['decline_message'] = null;
                                    } else {
                                        $currentBlock['decline_message'] = 'error';
                                    }
                                } else {
                                    // Split the key and value
                                    $pos = strpos($logMessage, ':');
                                    if ($pos !== false) {
                                        $key = trim(substr($logMessage, 0, $pos));
                                        $value = stripslashes(trim(substr($logMessage, $pos + 3, -2)));

                                        // Remove single quotes around the value
                                        if (strlen($value) > 1 && $value[0] === '\'' && $value[strlen($value) - 1] === '\'') {
                                            $value = substr($value, 1, -1);
                                        }

                                        // Add to current block
                                        $currentBlock[$key] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $parsedData;
        }

        // Extract the file
        $extractedFile = extractTsvGz($filePath);

        // Parse the extracted file
        return parseFile($extractedFile);
    }
}
