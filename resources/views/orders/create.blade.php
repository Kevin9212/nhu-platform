{{-- resources/views/orders/create.blade.php --}}
@extends('layouts.app')

@section('title', '交易地點')

@section('content')
@php
  $negotiationId = $negotiationId ?? null;
  $negotiationStatus = $negotiationStatus ?? null;
@endphp
<style>
  .order-section { max-width: 980px; margin: 0 auto; }
  .card { background: #fff; border: 1px solid #e6e6e6; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,.04); }
  .card-header { padding: 16px 20px; border-bottom: 1px solid #eee; font-weight: 600; font-size: 1.05rem; }
  .card-body { padding: 20px; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-row-1 { display: grid; grid-template-columns: 1fr; gap: 16px; }
  .form-group { display: flex; flex-direction: column; gap: 6px; }
  .form-group label { font-size: .92rem; color: #333; }
  .hint { font-size: .85rem; color: #6b7280; }
  .error { color: #b91c1c; font-size: .9rem; margin-top: 4px; display: none; }
  .btn-primary { background: #2f855a; color: #fff; border: 0; padding: 10px 16px; border-radius: 10px; cursor: pointer; }
  .btn-primary:disabled { opacity: .6; cursor: not-allowed; }
  #map { height: 420px; width: 100%; border-radius: 10px; border: 1px solid #e5e7eb; }
  .summary { background: #f9fafb; border: 1px dashed #d1d5db; padding: 12px; border-radius: 10px; font-size: .95rem; }
  @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
</style>
<script>
  // === 前端守門員：議價未被接受時直接返回議價總覽 ===
  (() => {
    const guard = {
      id: @json($negotiationId),
      status: @json($negotiationStatus),
      overviewUrl: "{{ route('member.index', ['tab' => 'negotiations']) }}#negotiations",
    };

    if (!guard.id) return; // 非議價流程直接設定交易地點

    const isAccepted = guard.status === 'accepted';

    if (isAccepted) return;

    const message = guard.status
      ? '此議價尚未由賣家接受，將為您返回議價總覽。'
      : '找不到相關議價，請重新從議價流程進入。';

    alert(message);
    window.location.href = guard.overviewUrl;
  })();
</script>
<div class="order-section">
  <form class="card" action="{{ route('orders.store') }}" method="POST" id="orderForm">
    @csrf
    <div class="card-header">設定交易地點</div>

    <div class="card-body">
      {{-- 從網址 / 上一次送出帶進來，給 OrderController@store 驗證用 --}}
      @php
        $idleItemId = old('idle_item_id', $idleItem->id ?? request('idle_item_id'));
        $priceValue = old('order_price', $orderPrice ?? request('order_price'));
        $negotiationId = old('negotiation_id', $negotiationId ?? request('negotiation_id'));
      @endphp
      <input type="hidden" name="idle_item_id" value="{{ $idleItemId }}">
      <input type="hidden" name="order_price"  value="{{ is_numeric($priceValue) ? (int) $priceValue : '' }}">
      <input type="hidden" name="negotiation_id" value="{{ $negotiationId }}">
      {{-- 顯示後端驗證錯誤（包含 idle_item_id / order_price） --}}
      @if ($errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- 1) 面交地點 --}}
      <div class="form-group">
        <label>面交地點（請在地圖上點選）</label>
        <div id="map"></div>
        <div class="hint">僅可於指定範圍內選點；點擊後會自動帶入地址、經緯度。</div>
        <div class="error" id="rangeError">⚠️ 超出可選範圍，請在範圍內重新點選。</div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="addressInput">地址</label>
          <input type="text" id="addressInput" name="meet_address" class="form-control"
                 placeholder="將自動帶入地址" readonly required>
        </div>
        <div class="form-row" style="gap:16px">
          <div class="form-group">
            <label for="latInput">緯度</label>
            <input type="text" id="latInput" name="meet_lat" class="form-control"
                   placeholder="請在地圖上點選" readonly required>
          </div>
          <div class="form-group">
            <label for="lngInput">經度</label>
            <input type="text" id="lngInput" name="meet_lng" class="form-control"
                   placeholder="請在地圖上點選" readonly required>
          </div>
        </div>
      </div>

      <hr style="margin:20px 0; border:none; border-top:1px solid #eee" />

      {{-- 2) 面交時間 --}}
      <div class="form-row">
        <div class="form-group">
          <label for="meetDate">面交日期</label>
          <input type="date" id="meetDate" name="meet_date" class="form-control" required>
          <div class="hint">不可選擇過去日期。</div>
        </div>
        <div class="form-group">
          <label for="meetTime">面交時間（24 小時制）</label>
          <select id="meetTime" name="meet_time" class="form-control" required></select>
          <div class="hint">以 15 分鐘為間隔（例如 13:00、13:15、13:30…）。</div>
        </div>
      </div>

      <div style="height:8px"></div>

      {{-- 3) 預覽摘要 --}}
      <div class="summary" id="summaryBox">
        <div><strong>面交地點：</strong><span id="sumAddr">尚未選擇</span></div>
        <div><strong>座標：</strong><span id="sumCoord">—</span></div>
        <div><strong>面交時間：</strong><span id="sumDT">尚未選擇</span></div>
      </div>

      <div style="height:16px"></div>

      <button type="submit" class="btn-primary" id="submitBtn" disabled>設定交易地點</button>
    </div>
  </form>
</div>

{{-- Leaflet CSS & JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
{{-- 點位落在多邊形內的判斷工具 --}}
<script src="https://unpkg.com/leaflet-pip/leaflet-pip.min.js"></script>

<script>
  // ====== 0) 工具：台灣常見格式地址組裝（Nominatim 備用） ======
  function composeAddress(addr) {
    if (!addr) return '';
    const parts = [
      addr.state || addr.region || '',
      addr.county || '',
      addr.city || addr.town || addr.village || '',
      addr.suburb || '',
      addr.road || addr.residential || '',
      addr.house_number || ''
    ].filter(Boolean);
    return parts.join('');
  }

  // ====== 1) 限制範圍多邊形（依序繞一圈） ======
  const latlngs = [
    [23.548500, 120.424228],
    [23.547801, 120.500401],
    [23.577708, 120.493863],
    [23.607352, 120.467786],
    [23.609312, 120.4441321]
  ];

  // 轉成 GeoJSON ring（lng, lat）並閉合
  const ring = latlngs.map(([lat, lng]) => [lng, lat]);
  if (ring[0][0] !== ring[ring.length - 1][0] || ring[0][1] !== ring[ring.length - 1][1]) {
    ring.push(ring[0]);
  }

  const boundaryGeoJSON = {
    type: "Feature",
    geometry: { type: "Polygon", coordinates: [ ring ] },
    properties: {}
  };

  const boundaryLayer = L.geoJSON(boundaryGeoJSON, {
    style: { opacity: 0, fillOpacity: 0 } // 不顯示，但可判斷 & 取 bounds
  });

  const bounds = boundaryLayer.getBounds();
  const center = bounds.getCenter();

  // ====== 2) 初始化地圖 ======
  const map = L.map('map').setView(center, 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map);
  boundaryLayer.addTo(map);

  map.setMaxBounds(bounds);
  map.on('drag', () => map.panInsideBounds(bounds));
  map.setMinZoom(13);
  map.setMaxZoom(16);

  // ====== 3) DOM 參照 ======
  const latInput = document.getElementById('latInput');
  const lngInput = document.getElementById('lngInput');
  const addrInput = document.getElementById('addressInput');
  const dateInput = document.getElementById('meetDate');
  const timeSelect = document.getElementById('meetTime');
  const rangeError = document.getElementById('rangeError');
  const submitBtn = document.getElementById('submitBtn');
  const sumAddr = document.getElementById('sumAddr');
  const sumCoord = document.getElementById('sumCoord');
  const sumDT = document.getElementById('sumDT');

  // ====== 4) 生成 24h 時間（每 15 分） ======
  (function buildTimeOptions() {
    const pad = (n) => n.toString().padStart(2, '0');
    const frags = [];
    for (let h = 0; h < 24; h++) {
      for (let m of [0, 15, 30, 45]) {
        const t = `${pad(h)}:${pad(m)}`;
        frags.push(`<option value="${t}">${t}</option>`);
      }
    }
    timeSelect.innerHTML = `<option value="" selected disabled>請選擇時間</option>` + frags.join('');
  })();

  // 日期最小值：今天
  (function setMinDateToday() {
    const tzOffsetMs = (new Date()).getTimezoneOffset() * 60 * 1000; // 以避免時差誤差
    const localISO = new Date(Date.now() - tzOffsetMs).toISOString().slice(0,10);
    dateInput.min = localISO;
  })();

  // ====== 5) 點擊地圖：選點 + 反向地理編碼 ======
  let marker;
  map.on('click', async (event) => {
    const lat = Number(event.latlng.lat.toFixed(6));
    const lng = Number(event.latlng.lng.toFixed(6));

    const inside = leafletPip.pointInLayer([lng, lat], boundaryLayer, true).length > 0;

    if (!inside) {
      // 超出範圍
      rangeError.style.display = 'block';
      latInput.value = '';
      lngInput.value = '';
      addrInput.value = '⚠️ 超出可選範圍';
      if (marker) map.removeLayer(marker);
      updateSummary();
      toggleSubmit();
      return;
    } else {
      rangeError.style.display = 'none';
    }

    // 在範圍內
    latInput.value = lat;
    lngInput.value = lng;

    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);

    addrInput.value = '地址解析中…';
    updateSummary();
    toggleSubmit();

    try {
      const controller = new AbortController();
      const id = setTimeout(() => controller.abort(), 8000);

      const url = new URL('https://nominatim.openstreetmap.org/reverse');
      url.searchParams.set('format', 'jsonv2');
      url.searchParams.set('lat', lat);
      url.searchParams.set('lon', lng);
      url.searchParams.set('zoom', '18');
      url.searchParams.set('addressdetails', '1');
      url.searchParams.set('accept-language', 'zh-TW');

      const res = await fetch(url.toString(), {
        method: 'GET',
        signal: controller.signal,
        headers: { 'Referer': window.location.origin }
      });
      clearTimeout(id);

      if (!res.ok) throw new Error('Geocoding failed: ' + res.status);
      const data = await res.json();

      const display = data?.display_name || composeAddress(data?.address);
      addrInput.value = display || '（找不到對應地址）';
    } catch (e) {
      console.error(e);
      addrInput.value = '地址解析失敗，請重試';
    } finally {
      updateSummary();
      toggleSubmit();
    }
  });

  // ====== 6) UI 輔助：摘要 + 按鈕狀態 ======
  function updateSummary() {
    const addr = addrInput.value?.trim() || '尚未選擇';
    const lat = latInput.value;
    const lng = lngInput.value;
    const d = dateInput.value;
    const t = timeSelect.value;

    sumAddr.textContent = addr;
    sumCoord.textContent = (lat && lng) ? `${lat}, ${lng}` : '—';
    sumDT.textContent = (d && t) ? `${d} ${t}` : '尚未選擇';
  }

  function toggleSubmit() {
    const ready = !!(
      latInput.value &&
      lngInput.value &&
      addrInput.value &&
      dateInput.value &&
      timeSelect.value &&
      rangeError.style.display !== 'block'
    );
    submitBtn.disabled = !ready;
  }

  dateInput.addEventListener('change', () => { updateSummary(); toggleSubmit(); });
  timeSelect.addEventListener('change', () => { updateSummary(); toggleSubmit(); });

  // ====== 7) 前端最小驗證（避免送出過去日期） ======
  document.getElementById('orderForm').addEventListener('submit', (e) => {
    // 驗證日期 >= 今日
    const today = new Date(); today.setHours(0,0,0,0);
    const picked = new Date(dateInput.value + 'T00:00:00');
    if (picked < today) {
      e.preventDefault();
      alert('面交日期不可早於今天，請重新選擇。');
      return false;
    }
  });
</script>
@endsection
