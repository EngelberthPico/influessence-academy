<?php

namespace App\Actions\Courses;

class ResolveVimeoEmbedUrlAction
{
    /**
     * @var list<string>
     */
    private const ALLOWED_HOSTS = ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'];

    public function handle(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d+$/', $value) === 1) {
            return $this->buildUrl($value);
        }

        $parts = parse_url($value);

        if (! is_array($parts) || ! isset($parts['host'])) {
            return null;
        }

        if (! in_array(strtolower($parts['host']), self::ALLOWED_HOSTS, true)) {
            return null;
        }

        $segments = array_values(array_filter(
            explode('/', $parts['path'] ?? ''),
            fn (string $segment): bool => $segment !== ''
        ));

        [$id, $hash] = $this->extractIdAndHash($segments);

        if ($id === null) {
            return null;
        }

        if ($hash === null) {
            $hash = $this->extractHashFromQuery($parts['query'] ?? null);
        }

        return $this->buildUrl($id, $hash);
    }

    /**
     * @param  list<string>  $segments
     * @return array{0: ?string, 1: ?string}
     */
    private function extractIdAndHash(array $segments): array
    {
        foreach ($segments as $index => $segment) {
            if (preg_match('/^\d+$/', $segment) !== 1) {
                continue;
            }

            $next = $segments[$index + 1] ?? null;
            $hash = ($next !== null && preg_match('/^[A-Za-z0-9]+$/', $next) === 1) ? $next : null;

            return [$segment, $hash];
        }

        return [null, null];
    }

    private function extractHashFromQuery(?string $query): ?string
    {
        if ($query === null) {
            return null;
        }

        parse_str($query, $params);

        $hash = $params['h'] ?? null;

        return (is_string($hash) && preg_match('/^[A-Za-z0-9]+$/', $hash) === 1) ? $hash : null;
    }

    private function buildUrl(string $id, ?string $hash = null): string
    {
        $url = "https://player.vimeo.com/video/{$id}?dnt=1";

        return $hash !== null ? "{$url}&h={$hash}" : $url;
    }
}
