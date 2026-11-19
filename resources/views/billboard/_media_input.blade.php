{{--
    Billboard media picker: one standard size for every slide (App\Models\Schedule::STANDARD_*).
    Pictures are cropped here to exactly that size; GIFs and videos must already have it.
    Shows a live preview of the slide inside the app banner. The server checks everything again.
    @param \App\Models\Schedule|null $current  slide being edited (shows its media first)
    @param bool $required
--}}
@php
    $S = \App\Models\Schedule::class;
    $currentUrl = isset($current) && $current && $current->image ? asset('uploads/' . $current->image) : null;
    $currentIsVideo = isset($current) && $current && ($current->media_type ?? 'image') === 'video';
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    .bb-media { position:relative; --bb-accent:#2563eb; --bb-ok:#059669; --bb-bad:#dc2626; --bb-ink:#0f172a; --bb-muted:#64748b; --bb-line:#e2e8f0; }
    .bb-media .bb-head { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:10px; margin-bottom:14px; }
    .bb-media .bb-title { font-weight:700; color:var(--bb-ink); font-size:15px; text-transform:none; letter-spacing:0; margin:0; }
    .bb-media .bb-chips { display:flex; flex-wrap:wrap; gap:6px; }
    .bb-media .bb-chip { display:inline-flex; align-items:center; gap:6px; padding:5px 10px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:600; }
    .bb-media .bb-chip.gray { background:#f1f5f9; color:#475569; }
    .bb-media .bb-grid { display:grid; grid-template-columns: minmax(0,1.25fr) minmax(0,1fr); gap:18px; }
    @media (max-width: 991px) { .bb-media .bb-grid { grid-template-columns: 1fr; } }

    .bb-media .bb-drop { position:relative; border:2px dashed #cbd5e1; border-radius:16px; background:linear-gradient(180deg,#f8fafc,#fff);
        min-height:230px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:24px;
        cursor:pointer; transition:border-color .2s, background .2s, transform .2s; }
    .bb-media .bb-drop:hover, .bb-media .bb-drop.over { border-color:var(--bb-accent); background:#eff6ff; }
    .bb-media .bb-drop.over { transform:scale(1.01); }
    .bb-media .bb-drop .bb-icon { width:56px; height:56px; border-radius:16px; display:flex; align-items:center; justify-content:center;
        background:#dbeafe; color:var(--bb-accent); font-size:24px; margin-bottom:12px; }
    .bb-media .bb-drop strong { color:var(--bb-ink); font-size:15px; }
    .bb-media .bb-drop span { color:var(--bb-muted); font-size:13px; }
    .bb-media .bb-drop .bb-browse { color:var(--bb-accent); text-decoration:underline; }

    .bb-media .bb-crop { display:none; border:1px solid var(--bb-line); border-radius:16px; overflow:hidden; background:#0f172a; }
    .bb-media .bb-crop .bb-crop-area { height:340px; }
    .bb-media .bb-crop .bb-crop-area img { display:block; max-width:100%; }
    .bb-media .bb-crop .bb-crop-bar { display:flex; flex-wrap:wrap; align-items:center; gap:8px; padding:10px 12px; background:#111827; color:#e5e7eb; font-size:12px; }
    .bb-media .bb-crop .bb-crop-bar .bb-area { margin-right:auto; font-weight:600; }
    .bb-media .bb-btn { border:0; border-radius:10px; padding:8px 14px; font-size:13px; font-weight:600; cursor:pointer; transition:filter .15s, transform .15s; }
    .bb-media .bb-btn:hover { filter:brightness(1.08); } .bb-media .bb-btn:active { transform:scale(.97); }
    .bb-media .bb-btn.primary { background:var(--bb-accent); color:#fff; }
    .bb-media .bb-btn.ghost { background:rgba(255,255,255,.1); color:#e5e7eb; }
    .bb-media .bb-btn.light { background:#f1f5f9; color:#0f172a; }
    .bb-media .bb-btn:disabled { opacity:.45; cursor:not-allowed; }

    .bb-media .bb-phone { margin:0 auto; max-width:330px; border-radius:34px; padding:12px; background:#0b1220; box-shadow:0 18px 40px rgba(15,23,42,.25); }
    .bb-media .bb-screen { border-radius:24px; background:#f8fafc; padding:16px 12px 18px; }
    .bb-media .bb-hello { height:9px; width:46%; border-radius:6px; background:#cbd5e1; margin:2px 0 6px; }
    .bb-media .bb-hello.s { width:68%; height:7px; background:#e2e8f0; margin-bottom:12px; }
    .bb-media .bb-banner { position:relative; aspect-ratio: {{ $S::STANDARD_WIDTH }} / {{ $S::STANDARD_HEIGHT }}; border-radius:12px; overflow:hidden; background:#e2e8f0;
        display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px; }
    .bb-media .bb-banner img, .bb-media .bb-banner video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .bb-media .bb-dots { display:flex; justify-content:center; gap:4px; margin:8px 0 12px; }
    .bb-media .bb-dots i { width:6px; height:6px; border-radius:50%; background:#cbd5e1; } .bb-media .bb-dots i:first-child { width:16px; border-radius:6px; background:#0f172a; }
    .bb-media .bb-tiles { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; } .bb-media .bb-tiles i { height:44px; border-radius:10px; background:#e2e8f0; }
    .bb-media .bb-caption { text-align:center; color:var(--bb-muted); font-size:12px; margin-top:10px; }

    .bb-media .bb-status { display:none; align-items:flex-start; gap:10px; margin-top:14px; padding:12px 14px; border-radius:12px; font-size:13px; line-height:1.45; }
    .bb-media .bb-status.ok { display:flex; background:#ecfdf5; color:#065f46; }
    .bb-media .bb-status.bad { display:flex; background:#fef2f2; color:#991b1b; }
    .bb-media .bb-status.info { display:flex; background:#eff6ff; color:#1e40af; }
    .bb-media .bb-status .fa { margin-top:2px; }
    .bb-media .bb-actions { display:none; gap:8px; margin-top:10px; flex-wrap:wrap; }
    .bb-media.shake { animation: bb-shake .4s; }
    @keyframes bb-shake { 20%,60% { transform:translateX(-6px); } 40%,80% { transform:translateX(6px); } }
</style>

<div class="bb-media" id="bb-media" data-required="{{ !empty($required) ? 1 : 0 }}">
    <div class="bb-head">
        <p class="bb-title"><i class="fa fa-image"></i>&nbsp; Slide picture or video @if(!empty($required))<span class="text-danger">*</span>@endif</p>
        <div class="bb-chips">
            <span class="bb-chip"><i class="fa fa-crop-alt"></i> {{ $S::standardLabel() }}</span>
            <span class="bb-chip gray">Picture · GIF · Video ≤ {{ $S::MAX_VIDEO_SECONDS }}s</span>
            <span class="bb-chip gray">Max {{ (int) ($S::MAX_UPLOAD_KB / 1024) }} MB</span>
        </div>
    </div>

    <input type="file" id="image" name="image" style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden;pointer-events:none" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime">

    <div class="bb-grid">
        <div>
            <label class="bb-drop" id="bb-drop" for="image">
                <div class="bb-icon"><i class="fa fa-cloud-upload-alt"></i></div>
                <strong id="bb-drop-title">Drop a picture, GIF or video here</strong>
                <span>or <span class="bb-browse">browse your files</span></span>
                <span style="margin-top:8px">Pictures are cropped to the billboard size right here.<br>Videos and GIFs must already be {{ $S::standardLabel() }}.</span>
            </label>

            <div class="bb-crop" id="bb-crop">
                <div class="bb-crop-area"><img id="bb-crop-img" alt="Crop"></div>
                <div class="bb-crop-bar">
                    <span class="bb-area" id="bb-crop-area-text">Drag to choose the part to show</span>
                    <button type="button" class="bb-btn ghost" data-zoom="-0.1" title="Zoom out"><i class="fa fa-search-minus"></i></button>
                    <button type="button" class="bb-btn ghost" data-zoom="0.1" title="Zoom in"><i class="fa fa-search-plus"></i></button>
                    <button type="button" class="bb-btn ghost" id="bb-crop-cancel">Choose another</button>
                    <button type="button" class="bb-btn primary" id="bb-crop-apply"><i class="fa fa-check"></i> Use this crop</button>
                </div>
            </div>

            <div class="bb-status" id="bb-status"><i class="fa"></i><div id="bb-status-text"></div></div>
            <div class="bb-actions" id="bb-actions">
                <button type="button" class="bb-btn light" id="bb-recrop"><i class="fa fa-crop-alt"></i> Adjust crop</button>
                <label for="image" class="bb-btn light" style="margin:0"><i class="fa fa-exchange-alt"></i> Choose another file</label>
            </div>
            @error('image')
                <div class="bb-status bad" style="display:flex"><i class="fa fa-exclamation-circle"></i><div>{{ $message }}</div></div>
            @enderror
        </div>

        <div>
            <div class="bb-phone">
                <div class="bb-screen">
                    <div class="bb-hello"></div><div class="bb-hello s"></div>
                    <div class="bb-banner" id="bb-banner">
                        @if($currentUrl)
                            @if($currentIsVideo)
                                <video src="{{ $currentUrl }}" muted autoplay loop playsinline></video>
                            @else
                                <img src="{{ $currentUrl }}" alt="Current slide">
                            @endif
                        @else
                            <span>Your slide appears here</span>
                        @endif
                    </div>
                    <div class="bb-dots"><i></i><i></i><i></i></div>
                    <div class="bb-tiles"><i></i><i></i><i></i></div>
                </div>
            </div>
            <div class="bb-caption" id="bb-caption">{{ $currentUrl ? 'Current slide, as customers see it in the app' : 'Live preview of the app home screen' }}</div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var W = {{ $S::STANDARD_WIDTH }}, H = {{ $S::STANDARD_HEIGHT }}, RATIO = W / H;
    var TOL = {{ $S::RATIO_TOLERANCE }}, MIN_W = {{ $S::MIN_WIDTH }}, MAX_S = {{ $S::MAX_VIDEO_SECONDS }};
    var MAX_BYTES = {{ $S::MAX_UPLOAD_KB }} * 1024, LABEL = '{{ $S::standardLabel() }}';

    var root = document.getElementById('bb-media');
    var input = document.getElementById('image');
    var drop = document.getElementById('bb-drop');
    var cropBox = document.getElementById('bb-crop');
    var cropImg = document.getElementById('bb-crop-img');
    var areaText = document.getElementById('bb-crop-area-text');
    var applyBtn = document.getElementById('bb-crop-apply');
    var statusBox = document.getElementById('bb-status');
    var statusText = document.getElementById('bb-status-text');
    var actions = document.getElementById('bb-actions');
    var banner = document.getElementById('bb-banner');
    var caption = document.getElementById('bb-caption');
    var form = input.form;

    var cropper = null, source = null, sourceUrl = null, ready = !!banner.querySelector('img,video') && root.dataset.required !== '1';
    var problem = null, settingFile = false;

    function status(kind, html) {
        statusBox.className = 'bb-status ' + kind;
        statusBox.querySelector('.fa').className = 'fa ' + (kind === 'ok' ? 'fa-check-circle' : kind === 'bad' ? 'fa-exclamation-circle' : 'fa-info-circle');
        statusText.innerHTML = html;
    }
    function fits(w, h) { return w > 0 && h > 0 && Math.abs((w / h) / RATIO - 1) <= TOL; }
    function preview(url, isVideo, text) {
        banner.innerHTML = '';
        var el = document.createElement(isVideo ? 'video' : 'img');
        el.src = url;
        if (isVideo) { el.muted = true; el.autoplay = true; el.loop = true; el.playsInline = true; }
        banner.appendChild(el);
        caption.textContent = text || 'How it will look in the app';
    }
    function closeCropper() {
        if (cropper) { cropper.destroy(); cropper = null; }
        cropBox.style.display = 'none';
    }
    function showDrop(show) { drop.style.display = show ? 'flex' : 'none'; }
    function fail(html) {
        problem = html; ready = false;
        closeCropper(); showDrop(true); actions.style.display = 'none';
        status('bad', html);
    }
    function setFile(file) { // put the (cropped) file into the form field
        var dt = new DataTransfer();
        dt.items.add(file);
        settingFile = true; input.files = dt.files; settingFile = false;
    }

    function handle(file) {
        problem = null; ready = false; closeCropper(); actions.style.display = 'none';
        if (!file) return;
        if (file.size > MAX_BYTES) return fail('This file is <b>' + (file.size / 1048576).toFixed(1) + ' MB</b>. The limit is ' + Math.round(MAX_BYTES / 1048576) + ' MB.');
        var url = URL.createObjectURL(file);
        if (file.type.indexOf('video/') === 0) {
            status('info', 'Checking the video…');
            var v = document.createElement('video');
            v.preload = 'metadata';
            v.onloadedmetadata = function () {
                var w = v.videoWidth, h = v.videoHeight, s = v.duration;
                if (!fits(w, h)) return fail('This video is <b>' + w + ' × ' + h + ' px</b>. Billboard videos must be <b>' + LABEL + '</b> (9:4) so they fill the banner exactly. In your video editor, set the canvas / export size to ' + LABEL + ' and upload again.');
                if (w < MIN_W) return fail('This video is only <b>' + w + ' × ' + h + ' px</b> and would look blurry. Export it at <b>' + LABEL + '</b>.');
                if (s > MAX_S + 0.5) return fail('This video is <b>' + Math.round(s) + ' seconds</b>. Billboard videos can be up to ' + MAX_S + ' seconds - please trim it.');
                ready = true; showDrop(false); actions.style.display = 'flex'; document.getElementById('bb-recrop').style.display = 'none';
                preview(url, true);
                status('ok', '<b>Ready.</b> Video ' + w + ' × ' + h + ' px, ' + Math.round(s) + ' s - fits the billboard exactly.');
            };
            v.onerror = function () { // e.g. a MOV this browser can't preview: the server checks it
                ready = true; showDrop(false); actions.style.display = 'flex'; document.getElementById('bb-recrop').style.display = 'none';
                status('info', 'This browser can\'t preview this video. It will be checked when you save - it must be <b>' + LABEL + '</b> and up to ' + MAX_S + ' seconds.');
            };
            v.src = url;
            return;
        }
        var img = new Image();
        img.onload = function () {
            var w = img.naturalWidth, h = img.naturalHeight, gif = file.type === 'image/gif';
            if (gif) {
                if (!fits(w, h)) return fail('This GIF is <b>' + w + ' × ' + h + ' px</b>. GIFs can\'t be cropped without losing the animation, so they must be made at <b>' + LABEL + '</b> (9:4).');
                if (w < MIN_W) return fail('This GIF is only <b>' + w + ' × ' + h + ' px</b> and would look blurry. Make it <b>' + LABEL + '</b>.');
                ready = true; showDrop(false); actions.style.display = 'flex'; document.getElementById('bb-recrop').style.display = 'none';
                preview(url, false);
                return status('ok', '<b>Ready.</b> Animated GIF ' + w + ' × ' + h + ' px - fits the billboard exactly.');
            }
            if (w < MIN_W || h < MIN_W / RATIO) return fail('This picture is only <b>' + w + ' × ' + h + ' px</b> and would look blurry on the billboard. Use a picture at least ' + MIN_W + ' px wide - <b>' + LABEL + '</b> is ideal.');
            source = file; if (sourceUrl) URL.revokeObjectURL(sourceUrl); sourceUrl = url;
            openCropper();
        };
        img.onerror = function () { fail('This file can\'t be opened as a picture. Use JPG, PNG, WEBP or GIF.'); };
        img.src = url;
    }

    function openCropper() {
        ready = false; problem = 'crop';
        showDrop(false); actions.style.display = 'none';
        cropBox.style.display = 'block';
        status('info', 'Drag and zoom to choose what shows on the billboard, then press <b>Use this crop</b>. It will be saved at exactly <b>' + LABEL + '</b>.');
        cropImg.src = sourceUrl;
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImg, {
            aspectRatio: RATIO, viewMode: 1, autoCropArea: 1, dragMode: 'move', background: false,
            guides: true, center: true, toggleDragModeOnDblclick: false, responsive: true,
            crop: function (e) {
                var cw = Math.round(e.detail.width), ch = Math.round(e.detail.height), sharp = cw >= MIN_W;
                areaText.innerHTML = (sharp ? '<i class="fa fa-check" style="color:#34d399"></i> ' : '<i class="fa fa-exclamation-triangle" style="color:#fbbf24"></i> ')
                    + 'Area ' + cw + ' × ' + ch + ' px' + (sharp ? '' : ' - zoom out, too small to stay sharp');
                applyBtn.disabled = !sharp;
            }
        });
    }

    applyBtn.addEventListener('click', function () {
        if (!cropper) return;
        var canvas = cropper.getCroppedCanvas({ width: W, height: H, fillColor: '#fff', imageSmoothingEnabled: true, imageSmoothingQuality: 'high' });
        canvas.toBlob(function (blob) {
            var name = (source.name || 'billboard').replace(/\.[^.]+$/, '') + '-billboard.jpg';
            setFile(new File([blob], name, { type: 'image/jpeg' }));
            var url = URL.createObjectURL(blob);
            closeCropper(); ready = true; problem = null;
            actions.style.display = 'flex'; document.getElementById('bb-recrop').style.display = '';
            preview(url, false);
            status('ok', '<b>Ready.</b> Picture cropped to exactly ' + LABEL + ' (' + Math.round(blob.size / 1024) + ' KB).');
        }, 'image/jpeg', 0.9);
    });
    document.getElementById('bb-crop-cancel').addEventListener('click', function () {
        closeCropper(); showDrop(true); problem = null; ready = false; statusBox.className = 'bb-status';
        input.value = '';
    });
    document.getElementById('bb-recrop').addEventListener('click', function () { if (source) openCropper(); });
    cropBox.querySelectorAll('[data-zoom]').forEach(function (b) {
        b.addEventListener('click', function () { if (cropper) cropper.zoom(parseFloat(b.dataset.zoom)); });
    });

    input.addEventListener('change', function () { if (!settingFile) handle(input.files && input.files[0]); });
    ['dragenter', 'dragover'].forEach(function (t) { drop.addEventListener(t, function (e) { e.preventDefault(); drop.classList.add('over'); }); });
    ['dragleave', 'drop'].forEach(function (t) { drop.addEventListener(t, function (e) { e.preventDefault(); drop.classList.remove('over'); }); });
    drop.addEventListener('drop', function (e) {
        var f = e.dataTransfer.files && e.dataTransfer.files[0];
        if (!f) return;
        setFile(f); handle(f);
    });

    if (form) form.addEventListener('submit', function (e) {
        var hasFile = input.files && input.files.length > 0;
        var message = null;
        if (problem === 'crop') message = 'Press <b>Use this crop</b> first (or choose another file).';
        else if (problem) message = problem;
        else if (!hasFile && root.dataset.required === '1') message = 'Add a picture, GIF or video for this slide.';
        if (message) {
            e.preventDefault();
            status(problem === 'crop' ? 'info' : 'bad', message);
            root.scrollIntoView({ behavior: 'smooth', block: 'center' });
            root.classList.remove('shake'); void root.offsetWidth; root.classList.add('shake');
        }
    });
});
</script>
