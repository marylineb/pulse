<?php

namespace App\Service;

use App\Entity\Profile;

class CompatibilityService
{
    public function calculate(Profile $a, Profile $b): int
    {
        $score = 0;

        $interestsA = $this->normalizeInterests($a->getInterests());
        $interestsB = $this->normalizeInterests($b->getInterests());

        $common = array_intersect($interestsA, $interestsB);
        $score += min(count($common) * 10, 50);

        if ($a->getVibe() && $b->getVibe()) {
            if (mb_strtolower(trim($a->getVibe())) === mb_strtolower(trim($b->getVibe()))) {
                $score += 20;
            }
        }

        if ($a->getAge() && $b->getAge()) {
            $diff = abs($a->getAge() - $b->getAge());

            if ($diff <= 2) {
                $score += 20;
            } elseif ($diff <= 5) {
                $score += 10;
            }
        }

        if ($a->getBio() && $b->getBio()) {
            similar_text(
                mb_strtolower($a->getBio()),
                mb_strtolower($b->getBio()),
                $percent
            );

            if ($percent >= 40) {
                $score += 10;
            }
        }

        return min($score, 100);
    }

    private function normalizeInterests(?array $interests): array
    {
        if (!$interests) {
            return [];
        }

        $normalized = array_map(
            static fn ($interest) => mb_strtolower(trim((string) $interest)),
            $interests
        );

        $normalized = array_filter($normalized, static fn ($interest) => $interest !== '');

        return array_values(array_unique($normalized));
    }
}