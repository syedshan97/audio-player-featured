<?php
// 1) Wrap default audio in a video container
add_filter('wp_audio_shortcode', 'vid_featured_audio_with_video', 10, 5);
function vid_featured_audio_with_video( $html, $atts, $audio, $post_id, $library ) {
    if ( ! is_singular('post') ) {
        return $html;
    }
    $video_url = 'https://tinytech.ai/wp-content/uploads/2025/04/1_jV9en9jk1FQ9U9GThAza7w.mp4';

    // Build the container
    return '
    <div class="vid-audio-video-player">
      <div class="vid-preview" tabindex="0">
        <video class="vid-preview-vid" src="' . esc_url($video_url) . '" preload="metadata" loop muted playsinline></video>
        <button class="vid-play-btn" aria-label="Play audio/video"></button>
      </div>
      <div class="vid-audio-native">' . $html . '</div>
    </div>';
}


// 2) Inject inline CSS into <head>
add_action('wp_head','vid_audio_video_css');
function vid_audio_video_css() {
    if ( ! is_singular('post') ) {
        return;
    }
    ?>
    <style>
    .vid-audio-video-player {
      width: 100%;
      margin: 1.5em 0;
    }
    .vid-preview {
      position: relative;
      width: 100%;
      padding-top: 56.25%; /* 16:9 */
      background: #000;
      overflow: hidden;
      cursor: pointer;
    }
    .vid-preview-vid {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      object-fit: cover;
    }

    /* play/pause button */
    .vid-play-btn {
      --btn-size: 64px;
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: var(--btn-size);
      height: var(--btn-size);
      border: none;
      border-radius: 50%;
      background-color: var(--vid-btn-bg, rgba(0,0,0,0.7));
      background-image: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<polygon points="30,20 80,50 30,80" fill="white"/>\
</svg>');
      background-repeat: no-repeat;
      background-position: center;
      background-size: var(--btn-size) var(--btn-size);
      opacity: 0;
      visibility: hidden;
      transition: opacity .2s, background-color .2s;
      z-index: 2;
    }
    /* show on hover or focus */
    .vid-preview:hover .vid-play-btn,
    .vid-preview:focus-within .vid-play-btn {
      opacity: 1;
      visibility: visible;
    }
    /* hover/focus state */
    .vid-play-btn:hover,
    .vid-play-btn:focus {
      background-color: var(--vid-btn-bg-hover, rgba(0,0,0,0.9));
      outline: none;
    }
    /* pause icon */
    .vid-audio-video-player.playing .vid-play-btn {
      background-image: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<rect x="30" y="20" width="12" height="60" fill="white"/>\
<rect x="58" y="20" width="12" height="60" fill="white"/>\
</svg>');
    }

    /* mobile tweak */
    @media (max-width: 600px) {
      .vid-play-btn {
        --btn-size: 40px;
        background-size: var(--btn-size) var(--btn-size);
      }
    }

    /* hide native controls */
    .vid-audio-native audio {
      display: none !important;
    }
    </style>
    <?php
}


// 3) Inject inline JS into footer
add_action('wp_footer','vid_audio_video_js');
function vid_audio_video_js() {
    if ( ! is_singular('post') ) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Gather all audio elements so we can pause them globally
      const audios = Array.from(document.querySelectorAll('.vid-audio-native audio'));

      document.querySelectorAll('.vid-audio-video-player').forEach(function(player){
        const preview = player.querySelector('.vid-preview'),
              audio   = player.querySelector('audio'),
              video   = player.querySelector('video');

        if (!preview || !audio || !video) return;

        // Toggle play/pause on any click in the preview area
        preview.addEventListener('click', function(e){
          e.preventDefault();

          if (audio.paused) {
            // Pause any other playing audio + video
            audios.forEach(function(a){
              if (a !== audio && !a.paused) {
                a.pause();
                const other = a.closest('.vid-audio-video-player');
                other.classList.remove('playing');
                other.querySelector('video').pause();
              }
            });

            audio.play();
            video.play();
            player.classList.add('playing');
          } else {
            audio.pause();
            video.pause();
            player.classList.remove('playing');
          }
        });

        // Keep state in sync if audio is controlled by other means
        audio.addEventListener('play',  function(){ video.play();  player.classList.add('playing'); });
        audio.addEventListener('pause', function(){ video.pause(); player.classList.remove('playing'); });
        audio.addEventListener('ended', function(){
          video.pause();
          video.currentTime = 0;
          player.classList.remove('playing');
        });
      });
    });
    </script>
    <?php
}
