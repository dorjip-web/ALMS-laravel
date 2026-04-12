@extends('admin_layout')

@section('content')
<style>
    :root{--accent:#f97316;--muted:#6b7280}
    .panel { padding:22px; max-width:720px; background:transparent }
    h2 { font-size:34px; margin:0 0 18px 0 }
    .form-label { display:block; margin-bottom:8px; font-weight:700; color:#061018 }
    .form-input { padding:10px 12px; width:100%; max-width:640px; border:1px solid #e6eef8; border-radius:6px; box-shadow:none }
    .form-actions { margin-top:18px }
    .btn { background:var(--accent); color:#fff; padding:10px 16px; border-radius:8px; border:none; font-weight:800; cursor:pointer }
    .btn-secondary { background:#fff; color:#333; border:1px solid #cfd8db; padding:10px 14px; border-radius:6px }
</style>

<div class="panel">
    <h2>Edit Tour Record</h2>

    <form method="POST" action="{{ route('ms.on_tour.update', $record['id'] ?? 0, false) }}">
        @csrf
        <label class="form-label">Place</label>
        <input class="form-input" type="text" name="place" value="{{ old('place', $record['place'] ?? '') }}">

        <label class="form-label" style="margin-top:12px">Start Date</label>
        <input class="form-input" type="date" name="start_date" value="{{ old('start_date', $record['start_date'] ?? '') }}">

        <label class="form-label" style="margin-top:12px">End Date</label>
        <input class="form-input" type="date" name="end_date" value="{{ old('end_date', $record['end_date'] ?? '') }}">

        <label class="form-label" style="margin-top:12px">Purpose</label>
        <textarea class="form-input" name="purpose" rows="4">{{ old('purpose', $record['purpose'] ?? '') }}</textarea>

        <div class="form-actions">
            <button class="btn" type="submit">Update</button>
            <a href="{{ route('ms.on_tour') }}" class="btn-secondary" style="margin-left:10px;display:inline-block;text-decoration:none;line-height:28px">Cancel</a>
        </div>
    </form>
</div>

@endsection
