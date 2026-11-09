<tk:html :lang="$locale" :title="$code.' — '.$title">
    <tk:layout.error>
        <tk:page.error :$code :$title :$description :home-url="false">
            @if ($action)
                <tk:button :href="$action['url']" :label="$action['label']" variant="accent" />
            @endif
        </tk:page.error>
    </tk:layout.error>
</tk:html>
