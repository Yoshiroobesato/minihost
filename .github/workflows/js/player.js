document.addEventListener('DOMContentLoaded', () => {
    const player = videojs('live-player', {
        controls: true,
        autoplay: true,
        preload: 'auto',
        fluid: true
    });

    // Disable right-click context menu to prevent downloads
    document.getElementById('live-player').addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Additional libraries initialization
    const additionalLibraries = [
        'https://cdnjs.cloudflare.com/ajax/libs/hls.js/latest/hls.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/dashjs/latest/dash.all.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/plyr/latest/plyr.polyfilled.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/videojs-youtube/latest/Youtube.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/videojs-vimeo/latest/videojs-vimeo.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/videojs-flash/latest/videojs-flash.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/latest/videojs-contrib-hls.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-dash/latest/videojs-dash.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/videojs-resolution-switcher/latest/videojs-resolution-switcher.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/videojs-thumbnails/latest/videojs-thumbnails.min.js'
    ];

    additionalLibraries.forEach(lib => {
        const script = document.createElement('script');
        script.src = lib;
        document.head.appendChild(script);
    });
});