<?php

namespace App\DictionaryEntity;

use App\Exceptions\DictionaryException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Dictionary
{
    private $app_id;
    private $app_key;

    public function __construct(string $app_id, string $app_key)
    {
        $this->app_id  = $app_id;
        $this->app_key = $app_key;
    }

    public function entries(string $lang, string $word): array
    {
        $fields  = 'definitions,pronunciations';
        $url     = 'https://od-api.oxforddictionaries.com:443/api/v2/entries/'.$lang.'/'.$word.'?fields='.$fields;

        $client    = new Client();

        try {
            $response = $client->get($url, [
                'headers' => [
                    'app_id'  => $this->app_id,
                    'app_key' => $this->app_key,
                ],
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                throw new DictionaryException('Dictionary API returned error: ' . $e->getMessage(), $e->getResponse()->getStatusCode());
            } else {
                throw new DictionaryException('Dictionary API returned empty response: ' . $e->getMessage(), 503);
            }
        }

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
    }
}
