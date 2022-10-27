<?php

namespace App\DictionaryEntity;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Dictionary
{
    private const APP_ID = "6a21e184";
    private const APP_KEY = "1f19c11bda4354a2f9e8d0bda25aae89";

    public function entries(string $lang, string $word): array
    {
        $fields = 'definitions,pronunciations';
        $url    = 'https://od-api.oxforddictionaries.com:443/api/v2/entries/'.$lang.'/'.$word.'?fields='.$fields;

        $client    = new Client();
        $exception = [];

        try {
            $response = $client->get($url, [
                'headers' => [
                    'app_id'  => self::APP_ID,
                    'app_key' => self::APP_KEY,
                ],
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $exception = (string)$e->getResponse()->getBody();
                $exception = json_decode($exception);

                return array($exception);
            } else {
                return array($e->getMessage(), 503);
            }
        }

        if ( ! $exception) {
            $content = json_decode($response->getBody());
            $entries = [];
            foreach ($content->results as $result) {
                foreach ($result->lexicalEntries as $lexicalEntry) {
                    $entry = new Entry();

                    $audio_files = [];
                    $senses      = [];
                    foreach ($lexicalEntry->entries as $item) {
                        if ( ! empty($item->pronunciations)) {
                            foreach ($item->pronunciations as $pronunciation) {
                                $audio_files[] = $pronunciation->audioFile;
                            }
                        }
                        if ( ! empty($item->pronunciations)) {
                            foreach ($item->senses as $sens) {
                                $senses[] = $sens->definitions[0];
                            }
                        }
                    }
                    $entry->setPronunciations($audio_files);
                    $entry->setDefinitions($senses);

                    $entries[] = $entry;
                }
            }

            return $entries;
        } else {
            return $exception;
        }
    }
}
