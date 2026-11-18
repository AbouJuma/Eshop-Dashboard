{{-- Billboard upload standard + a quick check in the browser before the form is sent (the server checks again). --}}
<div class="alert alert-light border mt-3 mb-0 small" style="line-height:1.6">
    <strong><i class="fas fa-ruler-combined"></i> Billboard standard</strong> (the app banner is 2:1)
    <ul class="mb-0 pl-3">
        <li><strong>Pictures:</strong> best <strong>1600 x 800 px</strong> (2:1); at least {{ \App\Models\Schedule::MIN_IMAGE_LONG_SIDE }} px on the longest side. Big pictures are reduced automatically so the app loads them fast.</li>
        <li><strong>Videos:</strong> best <strong>1920 x 1080</strong> (landscape), up to {{ \App\Models\Schedule::MAX_VIDEO_SECONDS }} seconds, max {{ (int) (\App\Models\Schedule::MAX_UPLOAD_KB / 1024) }} MB.</li>
        <li>Portrait or square files still work: the app shows them whole over a blurred background.</li>
    </ul>
</div>
<div id="billboard-media-check" class="small mt-2" style="display:none"></div>

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('image');
        var box = document.getElementById('billboard-media-check');
        if (!input || !box) return;
        var MAX_BYTES = {{ \App\Models\Schedule::MAX_UPLOAD_KB }} * 1024;
        var MIN_LONG = {{ \App\Models\Schedule::MIN_IMAGE_LONG_SIDE }};
        var MAX_SECONDS = {{ \App\Models\Schedule::MAX_VIDEO_SECONDS }};

        function show(message, ok) {
            input.setCustomValidity(ok ? '' : message);
            box.style.display = message ? 'block' : 'none';
            box.className = 'small mt-2 ' + (ok ? 'text-success' : 'text-danger font-weight-bold');
            box.textContent = message;
        }

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            show('', true);
            if (!file) return;
            if (file.size > MAX_BYTES) {
                show('This file is ' + (file.size / 1048576).toFixed(1) + ' MB. The limit is ' + Math.round(MAX_BYTES / 1048576) + ' MB.', false);
                return;
            }
            var url = URL.createObjectURL(file);
            if (file.type.indexOf('video/') === 0) {
                var video = document.createElement('video');
                video.preload = 'metadata';
                video.onloadedmetadata = function () {
                    URL.revokeObjectURL(url);
                    if (video.duration > MAX_SECONDS + 0.5) {
                        show('This video is ' + Math.round(video.duration) + ' seconds long. Billboard videos can be up to ' + MAX_SECONDS + ' seconds - please trim it.', false);
                    } else {
                        show('Video ' + video.videoWidth + ' x ' + video.videoHeight + ', ' + Math.round(video.duration) + ' s - looks good.', true);
                    }
                };
                video.onerror = function () { URL.revokeObjectURL(url); }; // e.g. MOV the browser can't preview: the server decides
                video.src = url;
            } else if (file.type.indexOf('image/') === 0) {
                var img = new Image();
                img.onload = function () {
                    URL.revokeObjectURL(url);
                    var w = img.naturalWidth, h = img.naturalHeight;
                    if (Math.max(w, h) < MIN_LONG) {
                        show('This picture is too small (' + w + ' x ' + h + ' px) and would look blurry. Use at least ' + MIN_LONG + ' px on the longest side - 1600 x 800 px is best.', false);
                    } else {
                        show('Picture ' + w + ' x ' + h + ' px - looks good.', true);
                    }
                };
                img.onerror = function () { URL.revokeObjectURL(url); };
                img.src = url;
            }
        });
    })();
</script>
@endpush
