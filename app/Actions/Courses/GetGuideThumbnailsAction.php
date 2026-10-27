<?php

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class GetGuideThumbnailsAction
{
    private const WIDTH = 160;

    private const QUALITY = 60;

    /**
     * @return Collection<int, ?string>
     */
    public function handle(Course $course): Collection
    {
        $disk = Storage::disk('local');

        return collect($course->guide_pages ?? [])
            ->values()
            ->map(fn (string $path): ?string => $this->dataUriFor($disk, $path));
    }

    private function dataUriFor(FilesystemAdapter $disk, string $path): ?string
    {
        if (! $disk->exists($path)) {
            return null;
        }

        $thumbPath = $this->thumbnailPath($path, $disk->lastModified($path));

        if ($disk->exists($thumbPath)) {
            return 'data:image/jpeg;base64,'.base64_encode($disk->get($thumbPath));
        }

        $thumbnail = $this->generate($disk->path($path));

        if ($thumbnail === null) {
            return null;
        }

        $disk->put($thumbPath, $thumbnail);

        return 'data:image/jpeg;base64,'.base64_encode($thumbnail);
    }

    private function thumbnailPath(string $path, int $lastModifiedAt): string
    {
        return 'guides/thumbs/'.sha1($path.'|'.$lastModifiedAt).'.jpg';
    }

    private function generate(string $absolutePath): ?string
    {
        $source = @imagecreatefromstring((string) file_get_contents($absolutePath));

        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);

            return null;
        }

        $targetWidth = self::WIDTH;
        $targetHeight = (int) round($height * ($targetWidth / $width));

        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($source);

        ob_start();
        imagejpeg($thumbnail, null, self::QUALITY);
        $bytes = ob_get_clean();
        imagedestroy($thumbnail);

        return $bytes === false ? null : $bytes;
    }
}
