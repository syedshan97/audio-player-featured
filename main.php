<?php
// 1) Wrap default audio in a video container
function vid_featured_audio_with_video( $html, $atts, $audio, $post_id, $library ) {
    if ( ! is_singular('post') ) return $html;
    $video_url = 'https://tinytech.ai/wp-content/uploads/2025/04/1_jV9en9jk1FQ9U9GThAza7w.mp4';
    return '
    <div class="vid-audio-video-player">
      <div class="vid-preview">
        <video class="vid-preview-vid" src="' . esc_url($video_url) . '" preload="metadata" loop muted playsinline></video>
        <button class="vid-play-btn" aria-label="Play audio/video"></button>
      </div>
      <div class="vid-audio-native">' . $html . '</div>
    </div>';
}
add_filter( 'wp_audio_shortcode', 'vid_featured_audio_with_video', 10, 5 );

// 2) Inline CSS for video player
function vid_audio_video_css() {
    if ( ! is_singular('post') ) return;
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
    }
    .vid-preview-vid {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      object-fit: cover;
    }
    .vid-play-btn {
      position: absolute;
      top:50%; left:50%;
      transform: translate(-50%,-50%);
      width:64px; height:64px;
      background: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<polygon points="30,20 80,50 30,80" fill="white"/>\
</svg>') no-repeat center center;
      background-size:64px 64px;
      border:none; cursor:pointer; opacity:.8;
      transition:opacity .2s; z-index:2;
    }
    .vid-play-btn:hover,
    .vid-play-btn:focus { opacity:1; outline:2px solid #fff; }
    .vid-audio-video-player.playing .vid-play-btn {
      background: url('data:image/svg+xml;utf8,\
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
<rect x="30" y="20" width="12" height="60" fill="white"/>\
<rect x="58" y="20" width="12" height="60" fill="white"/>\
</svg>') no-repeat center center;
      background-size:64px 64px;
    }
    .vid-audio-native audio { display:none !important; }
    </style>
    <?php
}
add_action( 'wp_head', 'vid_audio_video_css' );

// 3) Inline JS for video player
function vid_audio_video_js() {
    if ( ! is_singular('post') ) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      var audioEls = Array.from(document.querySelectorAll('.vid-audio-native audio'));
      document.querySelectorAll('.vid-audio-video-player').forEach(function(cont){
        var audio = cont.querySelector('audio'),
            video = cont.querySelector('video'),
            btn   = cont.querySelector('.vid-play-btn');
        if (!audio||!video||!btn) return;

        btn.addEventListener('click', function(e){
          e.preventDefault();
          if (audio.paused) {
            audioEls.forEach(function(a){
              if (a!==audio && !a.paused) {
                a.pause();
                var p = a.closest('.vid-audio-video-player');
                p.classList.remove('playing');
                p.querySelector('video').pause();
              }
            });
            audio.play();
            video.play();
            cont.classList.add('playing');
          } else {
            audio.pause();
            video.pause();
            cont.classList.remove('playing');
          }
        });

        // Sync on native events
        audio.addEventListener('play',  function(){ video.play();  cont.classList.add('playing'); });
        audio.addEventListener('pause', function(){ video.pause(); cont.classList.remove('playing'); });
        audio.addEventListener('ended', function(){
          video.pause();
          video.currentTime = 0;
          cont.classList.remove('playing');
        });
      });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'vid_audio_video_js' );
