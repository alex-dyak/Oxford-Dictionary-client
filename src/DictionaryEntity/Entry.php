<?php

namespace App\DictionaryEntity;

class Entry
{
    private array $definitions;
    private array $pronunciations;

    /**
     * @return array
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @param array $definitions
     */
    public function setDefinitions(array $definitions): void
    {
        $this->definitions = $definitions;
    }

    /**
     * @return array
     */
    public function getPronunciations(): array
    {
        return $this->pronunciations;
    }

    /**
     * @param array $pronunciations
     */
    public function setPronunciations(array $pronunciations): void
    {
        $this->pronunciations = $pronunciations;
    }

}
