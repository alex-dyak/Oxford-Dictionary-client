<?php

namespace App\Controller;

use App\DictionaryEntity\Dictionary;
use App\Entity\Searches;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function search(ManagerRegistry $doctrine, Request $request): Response
    {
        $word = strtolower($request->get('word'));

        $dictionary = new Dictionary($_ENV['DICTIONARY_APP_ID'], $_ENV['DICTIONARY_APP_KEY']);
        $lang       = $request->get('lang') ?: 'en-gb';
        $entries    = $dictionary->entries($lang, $word);

        $entityManager = $doctrine->getManager();
        $repository    = $entityManager->getRepository(Searches::class);

        $searched_word = $repository->findOneBy(array('word' => $word));

        if ( ! $searched_word) {
            $searched_word = new Searches();
            $searched_word->setWord($word);
            $searched_word->setCnt(1);
        } else {
            $num_searches = $searched_word->getCnt();
            $searched_word->setCnt($num_searches + 1);
        }

        $entityManager->persist($searched_word);
        $entityManager->flush();

        return $this->render('search.html.twig', [
            'word'    => $word,
            'entries' => $entries,
        ]);
    }
}
