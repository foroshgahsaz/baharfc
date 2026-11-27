{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0">
    <channel>
        <title>{{ $channelTitle }}</title>
        <link>{{ $channelUrl }}</link>
        <description>آخرین مقالات بلاگ</description>
        <language>fa-ir</language>
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('blog.show', $post->slug) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
                <pubDate>{{ $post->published_at?->toRfc2822String() }}</pubDate>
                @if ($post->author)
                    <author>{{ $post->author->email }} ({{ $post->author->name }})</author>
                @endif
                <description><![CDATA[{!! $post->excerpt ?: mb_strimwidth(strip_tags($post->body ?? ''), 0, 280, '…') !!}]]></description>
            </item>
        @endforeach
    </channel>
</rss>