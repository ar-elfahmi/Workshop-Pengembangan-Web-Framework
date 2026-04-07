<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Label TnJ 108</title>
  <style>
    @page {
      margin: 0;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'DejaVu Sans', sans-serif;
      color: #111;
    }

    .paper {
      position: relative;
      overflow: hidden;
    }

    .label-item {
      position: absolute;
      box-sizing: border-box;
      padding: 1.2mm;
      overflow: hidden;
    }

    .title {
      font-weight: bold;
      margin-bottom: 0.6mm;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .meta,
    .meta2 {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      line-height: 1.15;
    }

    .note {
      position: absolute;
      left: 2mm;
      bottom: 1.5mm;
      font-size: 6pt;
      color: #666;
    }
  </style>
</head>
<body style="font-size: {{ $config['font_size_pt'] }}pt;">
  <div class="paper" style="width: {{ $config['paper_width_mm'] }}mm; height: {{ $config['paper_height_mm'] }}mm;">
    @foreach ($placements as $placement)
      <div class="label-item" style="left: {{ $placement['x'] }}mm; top: {{ $placement['y'] }}mm; width: {{ $config['cell_width_mm'] }}mm; height: {{ $config['cell_height_mm'] }}mm;">
        <div class="title">{{ $placement['book']->judul ?? '-' }}</div>
        <div class="meta">{{ $placement['book']->kategori->nama_kategori ?? '-' }}</div>
        <div class="meta2">{{ $placement['book']->kode ?? '-' }} | {{ $placement['book']->pengarang ?? '-' }}</div>
      </div>
    @endforeach

    @if ($overflowCount > 0)
      <div class="note">
        {{ $overflowCount }} item tidak tercetak karena slot habis (dipilih {{ $totalSelected }} item).
      </div>
    @endif
  </div>
</body>
</html>
