@extends('layouts.app')

@section('title', 'Billboard')

@section('css')
<style>
    .bb-wrap { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 24px; align-items: start; }
    @media (max-width: 1100px) { .bb-wrap { grid-template-columns: 1fr; } .bb-preview-col { order: -1; } }

    .bb-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(15, 23, 42, .05); }
    .bb-head { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; padding: 18px 20px; border-bottom: 1px solid #f1f5f9; }
    .bb-head > div:first-child { flex: 1 1 360px; }
    .bb-head h3 { margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; }
    .bb-head p { margin: 2px 0 0; color: #64748b; font-size: 13px; }
    .btn-bb-new { background: #ea580c; color: #fff; border: 0; border-radius: 10px; font-weight: 700; padding: 10px 18px; display: inline-flex; gap: 8px; align-items: center; box-shadow: 0 6px 16px rgba(234, 88, 12, .25); }
    .btn-bb-new:hover, .btn-bb-new:focus { color: #fff; background: #c2410c; text-decoration: none; }

    .bb-list { list-style: none; margin: 0; padding: 8px 12px 12px; }
    .bb-item { display: flex; align-items: center; gap: 14px; padding: 10px; margin: 6px 0; border: 1px solid #eef2f7; border-radius: 14px; background: #fff; transition: box-shadow .15s, border-color .15s, opacity .15s; }
    .bb-item:hover { border-color: #cbd5e1; box-shadow: 0 6px 18px rgba(15, 23, 42, .06); }
    .bb-item.is-hidden { opacity: .55; background: #f8fafc; }
    .bb-item.ui-sortable-helper { box-shadow: 0 18px 40px rgba(15, 23, 42, .18); border-color: #ea580c; }
    .bb-placeholder { height: 92px; margin: 6px 0; border: 2px dashed #fdba74; border-radius: 14px; background: #fff7ed; }

    .bb-handle { cursor: grab; color: #94a3b8; font-size: 18px; padding: 8px 4px; }
    .bb-handle:active { cursor: grabbing; }
    .bb-pos { width: 30px; height: 30px; flex: none; border-radius: 50%; background: #0f172a; color: #fff; font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; }
    .bb-item.is-hidden .bb-pos { background: #cbd5e1; }
    .bb-thumb { position: relative; width: 132px; height: 70px; flex: none; border-radius: 10px; overflow: hidden; background: #e2e8f0; }
    .bb-thumb img, .bb-thumb video { width: 100%; height: 100%; object-fit: cover; display: block; }
    .bb-thumb .tag { position: absolute; left: 6px; bottom: 6px; background: rgba(15, 23, 42, .8); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 20px; letter-spacing: .03em; }
    .bb-info { flex: 1; min-width: 0; }
    .bb-info strong { display: block; color: #0f172a; font-size: 14.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bb-info small { color: #64748b; }
    .bb-first { display: inline-block; margin-left: 6px; font-size: 10px; font-weight: 800; color: #c2410c; background: #ffedd5; padding: 2px 8px; border-radius: 20px; vertical-align: middle; }

    .bb-actions { display: flex; align-items: center; gap: 6px; flex: none; }
    .bb-icon { width: 34px; height: 34px; border-radius: 10px; border: 0; background: #f1f5f9; color: #334155; display: inline-flex; align-items: center; justify-content: center; }
    .bb-icon:hover { background: #e2e8f0; color: #0f172a; text-decoration: none; }
    .bb-icon.danger { background: #fef2f2; color: #b91c1c; }
    .bb-icon.danger:hover { background: #b91c1c; color: #fff; }
    .bb-icon:disabled { opacity: .35; }

    .bb-switch { position: relative; width: 42px; height: 24px; flex: none; margin: 0 6px 0 0; }
    .bb-switch input { opacity: 0; width: 0; height: 0; }
    .bb-switch span { position: absolute; inset: 0; background: #cbd5e1; border-radius: 24px; cursor: pointer; transition: .2s; }
    .bb-switch span:before { content: ""; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
    .bb-switch input:checked + span { background: #16a34a; }
    .bb-switch input:checked + span:before { transform: translateX(18px); }

    .bb-saved { font-size: 12.5px; color: #16a34a; font-weight: 700; opacity: 0; transition: opacity .2s; }
    .bb-saved.show { opacity: 1; }
    .bb-empty { text-align: center; padding: 48px 20px; color: #64748b; }

    /* phone preview */
    .bb-preview-col { position: sticky; top: 70px; }
    .phone { width: 280px; margin: 0 auto; border-radius: 38px; background: #0f172a; padding: 12px; box-shadow: 0 24px 50px rgba(15, 23, 42, .25); }
    .phone-screen { border-radius: 28px; background: #f8fafc; overflow: hidden; height: 500px; padding: 18px 14px; }
    .phone-notch { width: 90px; height: 18px; background: #0f172a; border-radius: 0 0 12px 12px; margin: -18px auto 14px; }
    .phone-hi { font-weight: 800; font-size: 20px; color: #0f172a; margin-bottom: 12px; }
    .phone-slide { position: relative; height: 132px; border-radius: 14px; overflow: hidden; background: #e2e8f0; }
    .phone-slide img, .phone-slide video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity .5s; }
    .phone-slide .on { opacity: 1; }
    .phone-dots { text-align: center; margin: 8px 0 14px; }
    .phone-dots i { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #cbd5e1; margin: 0 3px; }
    .phone-dots i.on { background: #0f172a; }
    .phone-ph { height: 12px; border-radius: 6px; background: #e2e8f0; margin: 10px 0; }
    .phone-tiles { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 14px; }
    .phone-tiles div { height: 54px; border-radius: 12px; background: #e2e8f0; }
    .phone-caption { text-align: center; color: #64748b; font-size: 12.5px; margin-top: 12px; }
</style>
@endsection

@section('content')
<section class="content-header">
    <h1>Billboard <small>Slides at the top of the customer app's home screen</small></h1>
</section>

<section class="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" style="border-radius: 12px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bb-wrap">
        <div class="bb-card">
            <div class="bb-head">
                <div>
                    <h3><i class="fas fa-sort" style="color:#ea580c"></i> Slide order</h3>
                    <p>Drag <i class="fas fa-grip-vertical"></i> to choose which slide plays first. Switch a slide off to hide it without deleting it. The app shows the first {{ $appLimit }} visible slides.</p>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <span class="bb-saved" id="bb-saved"><i class="fas fa-check"></i> Saved</span>
                    <a href="{{ route('billboard.create') }}" class="btn-bb-new"><i class="fas fa-plus"></i> New slide</a>
                </div>
            </div>

            @if($schedules->isEmpty())
                <div class="bb-empty">
                    <i class="fas fa-images fa-3x" style="color:#cbd5e1"></i>
                    <h4 style="color:#0f172a; font-weight:700; margin-top:14px;">No slides yet</h4>
                    <p>Add an image, an animated GIF or a short video to show on the app's home screen.</p>
                </div>
            @else
                <ul class="bb-list" id="bb-list">
                    @foreach($schedules as $schedule)
                        <li class="bb-item {{ $schedule->visibility ? '' : 'is-hidden' }}" data-id="{{ $schedule->id }}"
                            data-src="{{ $schedule->media_url }}" data-video="{{ $schedule->isVideo() ? 1 : 0 }}">
                            <span class="bb-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
                            <span class="bb-pos"></span>
                            <div class="bb-thumb">
                                @if($schedule->media_url)
                                    @if($schedule->isVideo())
                                        <video src="{{ $schedule->media_url }}" muted loop playsinline preload="metadata"></video>
                                        <span class="tag"><i class="fas fa-play"></i> VIDEO</span>
                                    @else
                                        <img src="{{ $schedule->media_url }}" alt="" loading="lazy">
                                        @if(\Illuminate\Support\Str::endsWith(strtolower($schedule->image), '.gif'))<span class="tag">GIF</span>@endif
                                    @endif
                                @endif
                            </div>
                            <div class="bb-info">
                                <strong>{{ $schedule->title }} <span class="bb-first" style="display:none">PLAYS FIRST</span></strong>
                                <small><span class="bb-state">{{ $schedule->visibility ? 'Shown in app' : 'Hidden' }}</span> · added {{ optional($schedule->created_at)->diffForHumans() }}</small>
                            </div>
                            <div class="bb-actions">
                                <label class="bb-switch" title="Show in app">
                                    <input type="checkbox" class="bb-visible" {{ $schedule->visibility ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                                <button type="button" class="bb-icon bb-up" title="Move up"><i class="fas fa-arrow-up"></i></button>
                                <button type="button" class="bb-icon bb-down" title="Move down"><i class="fas fa-arrow-down"></i></button>
                                <a href="{{ route('billboard.edit', $schedule->id) }}" class="bb-icon" title="Edit"><i class="fas fa-pen"></i></a>
                                <form action="{{ route('billboard.destroy', $schedule->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this slide? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bb-icon danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bb-preview-col">
            <div class="phone">
                <div class="phone-screen">
                    <div class="phone-notch"></div>
                    <div class="phone-hi">Hi Customer,</div>
                    <div class="phone-slide" id="phone-slide"></div>
                    <div class="phone-dots" id="phone-dots"></div>
                    <div class="phone-ph" style="width:55%"></div>
                    <div class="phone-tiles"><div></div><div></div><div></div><div></div></div>
                    <div class="phone-ph" style="width:40%; margin-top:18px"></div>
                    <div class="phone-tiles"><div></div><div></div></div>
                </div>
            </div>
            <div class="phone-caption">Live preview: the order customers will see</div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    var $list = $('#bb-list');
    var appLimit = {{ (int) $appLimit }};
    var csrf = '{{ csrf_token() }}';
    var savedTimer, previewTimer, previewIndex = 0;

    function flashSaved() {
        $('#bb-saved').addClass('show');
        clearTimeout(savedTimer);
        savedTimer = setTimeout(function () { $('#bb-saved').removeClass('show'); }, 1600);
    }

    // numbers, "plays first" badge and arrow states follow the current order
    function renumber() {
        var shown = 0;
        $list.children('.bb-item').each(function (i) {
            var $it = $(this), visible = !$it.hasClass('is-hidden');
            $it.find('.bb-pos').text(i + 1);
            $it.find('.bb-first').toggle(visible && shown === 0);
            if (visible) shown++;
            $it.find('.bb-up').prop('disabled', i === 0);
            $it.find('.bb-down').prop('disabled', $it.is(':last-child'));
        });
        buildPreview();
    }

    function saveOrder() {
        var ids = $list.children('.bb-item').map(function () { return $(this).data('id'); }).get();
        $.post('{{ route('billboard.reorder') }}', { _token: csrf, ids: ids })
            .done(flashSaved)
            .fail(function () { alert('Could not save the order. Refresh the page and try again.'); });
    }

    function buildPreview() {
        var $slide = $('#phone-slide').empty(), $dots = $('#phone-dots').empty();
        var items = $list.children('.bb-item').not('.is-hidden').slice(0, appLimit);
        items.each(function (i) {
            var src = $(this).data('src'); if (!src) return;
            var $m = $(this).data('video') ? $('<video muted loop playsinline autoplay>').attr('src', src) : $('<img alt="">').attr('src', src);
            $slide.append($m); $dots.append('<i></i>');
        });
        previewIndex = 0;
        showPreview();
        clearInterval(previewTimer);
        if (items.length > 1) previewTimer = setInterval(function () { previewIndex++; showPreview(); }, 3000);
    }

    function showPreview() {
        var $m = $('#phone-slide').children(), n = $m.length;
        if (!n) return;
        var i = previewIndex % n;
        $m.removeClass('on').eq(i).addClass('on');
        $('#phone-dots i').removeClass('on').eq(i).addClass('on');
    }

    if ($list.length && $.fn.sortable) {
        $list.sortable({
            handle: '.bb-handle',
            placeholder: 'bb-placeholder',
            axis: 'y',
            tolerance: 'pointer',
            update: function () { renumber(); saveOrder(); }
        });
    }

    $list.on('click', '.bb-up, .bb-down', function () {
        var $it = $(this).closest('.bb-item');
        if ($(this).hasClass('bb-up')) $it.prev().before($it); else $it.next().after($it);
        renumber(); saveOrder();
    });

    $list.on('change', '.bb-visible', function () {
        var $box = $(this), $it = $box.closest('.bb-item'), visible = $box.is(':checked');
        $it.toggleClass('is-hidden', !visible);
        $it.find('.bb-state').text(visible ? 'Shown in app' : 'Hidden');
        renumber();
        $.post('{{ url('billboard') }}/' + $it.data('id') + '/visibility', { _token: csrf, visible: visible ? 1 : 0 })
            .done(flashSaved)
            .fail(function () {
                $box.prop('checked', !visible); $it.toggleClass('is-hidden', visible); renumber();
                alert('Could not change visibility. Try again.');
            });
    });

    // play video thumbnails on hover only
    $list.on('mouseenter', '.bb-thumb video', function () { this.play(); })
         .on('mouseleave', '.bb-thumb video', function () { this.pause(); });

    renumber();
});
</script>
@endsection
