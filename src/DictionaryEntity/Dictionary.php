<?php

namespace App\DictionaryEntity;

use App\Exceptions\DictionaryException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Dictionary
{
    public function entries(string $lang, string $word): array
    {
        $app_id  = $_ENV['DICTIONARY_APP_ID'];
        $app_key = $_ENV['DICTIONARY_APP_KEY'];
        $fields  = 'definitions,pronunciations';
        $url     = 'https://od-api.oxforddictionaries.com:443/api/v2/entries/'.$lang.'/'.$word.'?fields='.$fields;

        $client    = new Client();
        $exception = [];

        try {
            $response = $client->get($url, [
                'headers' => [
                    'app_id'  => $app_id,
                    'app_key' => $app_key,
                ],
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == '404') {
                    $exception = (string)$e->getResponse()->getBody();
                    $exception = json_decode($exception);

                    return array($exception);
                } else {
                    throw new DictionaryException('Thomething went wrong', $e->getResponse()->getStatusCode());
                }
            } else {
                throw new DictionaryException('Thomething went wrong', 503);
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
