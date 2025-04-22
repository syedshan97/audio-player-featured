<?php
/**
 * Featured‑Image Audio as 16:9 “Video” Player
 */


/**
 * 1) Wrap the default audio HTML in our custom markup
 */
add_filter( 'wp_audio_shortcode', 'tn_featured_audio_as_video', 10, 5 );
function tn_featured_audio_as_video( $html, $atts, $audio, $post_id, $library ) {
    if ( ! is_singular( 'post' ) ) {
        // only on single post pages
        return $html;
    }

    // get full‑size featured image (fallback to empty string)
    $feat_img = get_the_post_thumbnail_url( $post_id, 'full' ) ?: '';

    // build wrapper
    $output  = '<div class="tn-audio-video-player">';
    $output .= '  <div class="tn-preview">';
    $output .= '    <img class="tn-preview-img" src="' . esc_url( $feat_img ) . '" alt="' . esc_attr( get_the_title( $post_id ) ) . '">';
    $output .= '    <button class="tn-play-btn"></button>';
    $output .= '  </div>';
    // inject original audio; we'll hide native controls via CSS
    $output .= '  <div class="tn-audio-native">' . $html . '</div>';
    $output .= '</div>';

    return $output;
}


/**
 * 2) Inject CSS into the <head> on single posts
 */
add_action( 'wp_head', 'tn_featured_audio_video_css' );
function tn_featured_audio_video_css() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    ?>
    <style>
    /* overall wrapper */
    .tn-audio-video-player {
      width: 100%;
      margin: 1.5em 0;
    }

    /* 16:9 aspect ratio “video” box */
    .tn-preview {
      position: relative;
      width: 100%;
      padding-top: 56.25%;  /* 16:9 */
      background: #000;      /* black letterbox */
      overflow: hidden;
    }

    /* featured image centered and contained */
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

    /* play button centered over image */
    .tn-play-btn {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 64px; height: 64px;
      background: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<polygon points="30,20 80,50 30,80" fill="white"/></svg>')
        no-repeat center center;
      background-size: 64px 64px;
      border: none;
      cursor: pointer;
      opacity: 0.8;
      transition: opacity .2s;
      z-index: 2;
    }
    .tn-play-btn:hover { opacity: 1; }

    /* switch to pause icon when playing */
    .tn-audio-video-player.playing .tn-play-btn {
      background: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<rect x="30" y="20" width="12" height="60" fill="white"/>\
<rect x="58" y="20" width="12" height="60" fill="white"/>\
</svg>')
        no-repeat center center;
      background-size: 64px 64px;
    }

    /* hide the native audio controls */
    .tn-audio-native audio {
      display: none !important;
    }
    </style>
    <?php
}


/**
 * 3) Inject JS into the footer on single posts
 */
add_action( 'wp_footer', 'tn_featured_audio_video_js' );
function tn_featured_audio_video_js() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    ?>
    <script>
    jQuery(function($){
      $('.tn-audio-video-player').each(function(){
        var container = this,
            audioEl   = $(this).find('audio').get(0),
            btn       = $(this).find('.tn-play-btn');

        if (!audioEl) return;

        // play/pause toggle
        btn.on('click', function(e){
          e.preventDefault();
          if (audioEl.paused) {
            audioEl.play();
            $(container).addClass('playing');
          } else {
            audioEl.pause();
            $(container).removeClass('playing');
          }
        });

        // reset when ended
        audioEl.addEventListener('ended', function(){
          $(container).removeClass('playing');
        });
      });
    });
    </script>
    <?php
}
