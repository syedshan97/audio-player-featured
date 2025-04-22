# Transform Audio Player into Custom Video Player like look using Featured Images

This code transforms the default WordPress audio player on single post pages into a visually appealing, modern video-style player using the post's featured image as a preview background.

It mimics a 16:9 video layout with a play/pause button overlay, while still using the native HTML5 <audio> player behind the scenes for actual playback. This is a front-end enhancement only—no changes are made to post content or audio files.

🔍 Function-by-Function Description
1. tn_featured_audio_as_video()
Hooked to: wp_audio_shortcode

What it does:

Wraps the default audio player output with a custom HTML structure.

Adds a <div> that holds:

The featured image as a background.

A custom play/pause button.

The original WordPress audio player.

Applies only on single post pages (is_singular( 'post' )).

2. tn_featured_audio_video_css()
Hooked to: wp_head

What it does:

Injects inline CSS into the <head> of single post pages.

Styles the custom player to:

Have a fixed 16:9 aspect ratio.

Show the featured image properly letterboxed.

Use play/pause icons as background images (SVG).

Hide the native audio player controls (they're still there for playback, just not visible).

3. tn_featured_audio_video_js()
Hooked to: wp_footer

What it does:

Injects inline JavaScript at the bottom of the page (just before </body>).

Adds interaction:

Clicking the play button starts/stops the audio.

When audio plays, it replaces the play icon with a pause icon.

If multiple players exist, starting one will pause all others.

📐 Visual Behavior
The player looks like a video preview box with a play button in the middle.

When clicked, it plays audio and shows a pause icon.

Audio ends? The play icon returns.

On mobile or desktop, the design is responsive and sleek.

🧩 Performance & Standards
No external files are loaded—only inline CSS/JS, conditionally added to single posts.

No global scripts or styles are injected where they’re not needed.

Uses is_singular('post') to keep the impact narrow and efficient.

All code follows WordPress coding standards, using appropriate escaping (esc_url, esc_attr), conditional checks, and best practices.


