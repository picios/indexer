<?php

namespace Picios\Indexer;

class Indexer {

    private $stopwords = [];

    public function setStopwords(?array $stopwords = []): self
    {
        $this->stopwords = $stopwords;

        return $this;
    }

    public function loadStopwordsFromFile(string $filename)
    {
        if (!file_exists($filename)) {
            // or throw exception
            return null;
        }

        $words = file($filename);
        $stopwords = [];
        foreach ($words as $word) {
            $trimmed = trim($word);
            if (substr($trimmed, 0, 1) == '#') { // skip comments
                continue;
            }
            $stopword = trim($trimmed, '.-!@#$%^&_+=?(){}[]–»"');
            if (!$stopword) { // skip empties
                continue;
            }
            $stopwords[] = $stopword;
        }
        if ($stopwords) {
            $this->setStopwords($stopwords);
        }
    }

    public function getStrongIndex($string, $limit = 20): ?string
    {
        $words = $this->getWords($string);
        $filtered = $this->getWithoutStopwords($words);
        $counted = $this->getCounted($filtered);
        $slice = array_slice($counted, 0, $limit);
        return implode(' ', array_keys($slice));
    }

    public function getFullIndex($string): ?string
    {
        $words = $this->getWords($string);
        $filtered = $this->getWithoutStopwords($words);
        $uniques = $this->getUniqe($filtered);
        return implode(' ', $uniques);
    }

    private function getWords($string): ?array
    {
        $words = preg_split('/([\s\,]+|\.\s+)/u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if (!$words) {
            return null;
        }

        $result = [];
        foreach ($words as $word) {
            $newWord = trim($word, ' .-!@#$%^&_+=?(){}[]–»"');
            if ($newWord && strlen($newWord) > 2) {
                $result[] = $newWord;
            }
        }

        return $result ? $result : null;
    }

    private function getWithoutStopwords(?array $words): ?array
    {
        if ($words === null || !is_array($words) || count($words) === 0) {
            return null;
        }

        return array_udiff($words, $this->stopwords, 'strcasecmp');
    }

    private function getUniqe(?array $words): ?array
    {
        if ($words === null || !is_array($words) || count($words) === 0) {
            return null;
        }

        return array_unique($words);
    }

    private function getCounted(?array $words): ?array
    {
        $counted = array_count_values($words);
        arsort($counted, SORT_NUMERIC);
        return $counted;
    }

}