<?php

namespace Craft\Data\Processor;

/**
 * Class Files
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class FileProcessor
{
    /**
     * Transform csv file into array data
     * @param string $filename
     * @param string $delimiter
     * @return array
     */
    public static function csvFileToArray(string $filename, string $delimiter = ','): array
    {
        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Read a csv using a generator in order to avoid memory issues
     * @param string $filename
     * @param string $delimiter
     * @return \Generator
     */
    public static function csvFileToArrayGenerator(string $filename = '', string $delimiter = ','): \Generator
    {
        $complete = false;
        try {
            $header = null;
            if (($handle = fopen($filename, 'r')) !== false) {
                while (($row = fgetcsv($handle, 4096, $delimiter)) !== false) {
                    if (!$header) {
                        $header = $row;
                    } else {
                        yield array_combine($header, $row);
                    }
                }
            }
            $complete = true;
        } finally {
            if (!$complete) {
                // cleanup when loop breaks
            } else {
                // cleanup when loop completes
            }
        }
        // Do something only after loop completes
    }

    /**
     * Transform array data to csv file
     * @param string $filename
     * @param array $data
     * @return bool
     */
    public static function arrayToCsvFile(string $filename, array $data): bool
    {
        $fp = fopen($filename, 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        return fclose($fp);
    }

}
