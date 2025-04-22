<?php
/**
 * Wrap default audio shortcode in a featured‑image “video” container.
 *
 * @param string $html     Original audio player HTML.
 * @param array  $atts     Shortcode attributes.
 * @param string $audio    Raw audio data.
 * @param int    $post_id  Post ID.
 * @param string $library  Media library context.
 * @return string          Modified HTML.
 */
function tn_featured_audio_as_video( $html, $atts, $audio, $post_id, $library ) {
    if ( ! is_singular( 'post' ) ) {
        return $html;
    }

    // Cache featured image URL (fall back to empty if none)
    $feat_img = get_the_post_thumbnail_url( $post_id, 'full' ) ?: '';

    // Build markup
    $output  = '<div class="tn-audio-video-player">';
    $output .= '  <div class="tn-preview">';
    $output .= '    <img class="tn-preview-img" src="' . esc_url( $feat_img ) . '" alt="' . esc_attr( get_the_title( $post_id ) ) . '">';
    $output .= '    <button class="tn-play-btn" aria-label="Play audio"></button>';
    $output .= '  </div>';
    $output .= '  <div class="tn-audio-native">' . $html . '</div>';
    $output .= '</div>';

    return $output;
}
add_filter( 'wp_audio_shortcode', 'tn_featured_audio_as_video', 10, 5 );


/**
 * Output inline CSS in <head> for our player (only on single posts).
 */
function tn_featured_audio_video_css() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    ?>
    <style>
    /* Wrapper */
    .tn-audio-video-player {
      width: 100%;
      margin: 1.5em 0;
    }

    /* 16:9 container */
    .tn-preview {
      position: relative;
      width: 100%;
      padding-top: 56.25%; /* 16:9 */
      background: #000;
      overflow: hidden;
    }

    /* Featured image letter‑boxed */
    .tn-preview-img {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      max-width: 100%;
      max-height: 100%;
      width: auto;
      height: auto;
      display: block;
    }

    /* Play/Pause button */
    .tn-play-btn {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 64px;
      height: 64px;
      background: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<polygon points="30,20 80,50 30,80" fill="white"/>\
</svg>') no-repeat center center;
      background-size: 64px 64px;
      border: none;
      cursor: pointer;
      opacity: 0.8;
      transition: opacity .2s;
      z-index: 2;
    }
    .tn-play-btn:hover,
    .tn-play-btn:focus {
      opacity: 1;
      outline: 2px solid #fff;
    }

    /* Pause icon when playing */
    .tn-audio-video-player.playing .tn-play-btn {
      background: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<rect x="30" y="20" width="12" height="60" fill="white"/>\
<rect x="58" y="20" width="12" height="60" fill="white"/>\
</svg>') no-repeat center center;
      background-size: 64px 64px;
    }

    /* Hide native controls */
    .tn-audio-native audio {
      display: none !important;
    }
    </style>
    <?php
}
add_action( 'wp_head', 'tn_featured_audio_video_css' );


/**
 * Output inline JS in footer for player behavior (only on single posts).
 */
function tn_featured_audio_video_js() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Collect all audio elements to allow global pause
      var audioEls = Array.prototype.slice.call( document.querySelectorAll('.tn-audio-native audio') );

      document.querySelectorAll('.tn-audio-video-player').forEach(function(container){
        var audioEl = container.querySelector('audio'),
            btn     = container.querySelector('.tn-play-btn');

        if (!audioEl || !btn) return;

        // Play/Pause toggle
        btn.addEventListener('click', function(e){
          e.preventDefault();

          if (audioEl.paused) {
            // Pause others
            audioEls.forEach(function(other){
              if (other !== audioEl && !other.paused) {
                other.pause();
                other.closest('.tn-audio-video-player').classList.remove('playing');
              }
            });

            audioEl.play();
            container.classList.add('playing');
          } else {
            audioEl.pause();
            container.classList.remove('playing');
          }
        });

        // Sync state on native events
        audioEl.addEventListener('play',  function(){ container.classList.add('playing'); });
        audioEl.addEventListener('pause', function(){ container.classList.remove('playing'); });
        audioEl.addEventListener('ended', function(){ container.classList.remove('playing'); });
      });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'tn_featured_audio_video_js' );
