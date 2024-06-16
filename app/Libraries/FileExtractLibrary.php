<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;

class FileExtractLibrary
{
    public function extractTsvGz($filePath)
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

    public function parseFile($filePath)
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
                                    $currentBlock['decline_message'] = 'declined';
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

    public function extractAndParseFile($filePath)
    {
        // Extract the file
        $extractedFile = $this->extractTsvGz($filePath);

        // Parse the extracted file
        return $this->parseFile($extractedFile);
    }
}
