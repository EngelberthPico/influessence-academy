<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
    </url>
    <url>
        <loc>{{ route('courses.index') }}</loc>
    </url>
@foreach ($courses as $course)
    <url>
        <loc>{{ route('courses.show', $course) }}</loc>
        <lastmod>{{ $course->updated_at->toDateString() }}</lastmod>
    </url>
@endforeach
    <url>
        <loc>{{ route('legal.privacy') }}</loc>
    </url>
    <url>
        <loc>{{ route('legal.terms') }}</loc>
    </url>
    <url>
        <loc>{{ route('legal.refunds') }}</loc>
    </url>
</urlset>
