{{-- Reference photos / videos of a service record (also added from the mobile app). --}}
<style>
    .att-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; margin-top: 14px; }
    .att-item { position: relative; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #f8fafc; }
    .att-item a.att-open { display: block; aspect-ratio: 4 / 3; background: #0f172a; }
    .att-item img, .att-item video { width: 100%; height: 100%; object-fit: cover; display: block; }
    .att-meta { padding: 6px 8px; font-size: 11.5px; color: #64748b; line-height: 1.35; }
    .att-meta b { color: #334155; font-weight: 600; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .att-del { position: absolute; top: 6px; right: 6px; margin: 0; }
    .att-del button { border: 0; border-radius: 50%; width: 28px; height: 28px; background: rgba(15, 23, 42, .75); color: #fff; }
    .att-badge { position: absolute; top: 6px; left: 6px; background: rgba(15, 23, 42, .75); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 6px; }
    .att-upload { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .att-upload input[type=text] { flex: 1 1 220px; }
</style>

<div class="maintenance-card" style="margin-top: 20px;" id="attachments">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fas fa-photo-video"></i> Photos &amp; videos ({{ $attachments->count() }})</h3>
    </div>
    <div class="modal-body">
        <p class="text-muted" style="margin-top: 0;">Keep pictures or short videos of the work for future reference (before / after, worn parts, odometer). Max {{ (int) (\App\Models\MediaAttachment::MAX_UPLOAD_KB / 1024) }} MB per file.</p>

        <form action="{{ route('car-maintenance.attachments.store', $maintenance->id) }}" method="POST" enctype="multipart/form-data" class="att-upload">
            @csrf
            <input type="file" name="file" accept="image/*,video/*" required class="form-control" style="flex: 1 1 220px;">
            <input type="text" name="caption" maxlength="255" placeholder="Caption (optional), e.g. Old brake pads" class="form-control">
            <button type="submit" class="btn-submit" style="margin: 0;"><i class="fa fas fa-upload"></i> Upload</button>
        </form>

        @if($attachments->isEmpty())
            <p class="text-muted" style="margin: 16px 0 0;">No photos or videos yet.</p>
        @else
            <div class="att-grid">
                @foreach($attachments as $a)
                    <div class="att-item">
                        <a class="att-open" href="{{ $a->url }}" target="_blank" rel="noopener">
                            @if($a->media_type === 'video')
                                <video src="{{ $a->url }}#t=0.5" preload="metadata" muted></video>
                            @else
                                <img src="{{ $a->url }}" alt="{{ $a->caption }}" loading="lazy">
                            @endif
                        </a>
                        @if($a->media_type === 'video')<span class="att-badge">VIDEO</span>@endif
                        <form class="att-del" action="{{ route('car-maintenance.attachments.destroy', [$maintenance->id, $a->id]) }}" method="POST"
                              onsubmit="return confirm('Delete this file?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete"><i class="fa fa-trash"></i></button>
                        </form>
                        <div class="att-meta">
                            <b>{{ $a->caption ?: ($a->media_type === 'video' ? 'Video' : 'Photo') }}</b>
                            {{ optional($a->created_at)->format('d M Y H:i') }}
                            @if($a->uploader) · {{ trim($a->uploader->first_name.' '.$a->uploader->last_name) ?: $a->uploader->username }} @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
