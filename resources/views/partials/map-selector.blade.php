{{-- resources/views/partials/map-selector.blade.php --}}
<div class="map-container mb-3">
  <h5>選擇交貨地點</h5>
  <div id="map" style="height: 500px; width: 100%;"></div>

  <div class="row mt-3 g-3">
    <div class="col-md-4">
      <label for="latInput" class="form-label">緯度</label>
      <input type="text" id="latInput" name="lat" class="form-control" placeholder="請在地圖上點選" readonly>
    </div>
    <div class="col-md-4">
      <label for="lngInput" class="form-label">經度</label>
      <input type="text" id="lngInput" name="lng" class="form-control" placeholder="請在地圖上點選" readonly>
    </div>
    <div class="col-md-12">
      <label for="addressInput" class="form-label">地址</label>
      <input type="text" id="addressInput" name="address" class="form-control" placeholder="將自動帶入地址" readonly>
      <div id="addrHint" class="form-text">點地圖後會自動解析地址；超出範圍將顯示警示。</div>
    </div>
  </div>
</div>

{{-- Leaflet CSS & JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

{{-- 點位落在多邊形內的判斷工具 --}}
<script src="https://unpkg.com/leaflet-pip/leaflet-pip.min.js"></script>

<script>
  // === 1) 定義限制範圍的多邊形頂點（依序繞一圈） ===
  const latlngs = [
    [23.548500, 120.424228],
    [23.547801, 120.500401],
    [23.577708, 120.493863],
    [23.607352, 120.467786],
    [23.609312, 120.4441321]
  ];

  // 轉成 GeoJSON 坐標（[lng, lat]），並確保首尾閉合
  const ring = latlngs.map(([lat, lng]) => [lng, lat]);
  if (ring[0][0] !== ring[ring.length - 1][0] || ring[0][1] !== ring[ring.length - 1][1]) {
    ring.push(ring[0]);
  }

  const boundaryGeoJSON = {
    type: "Feature",
    geometry: {
      type: "Polygon",
      coordinates: [ ring ]
    },
    properties: {}
  };

  // 先建立圖層以便取得 bounds（此時尚未加到地圖）
  const boundaryLayer = L.geoJSON(boundaryGeoJSON, {
    style: { opacity: 0, fillOpacity: 0 } // 完全不可見，但仍可用來限制與判斷
  });

  const bounds = boundaryLayer.getBounds();
  const center = bounds.getCenter();

  // === 2) 初始化地圖並設定初始視角在多邊形中心 ===
  const map = L.map('map').setView(center, 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map);

  // 把不可見的邊界層加入地圖（不顯示線條）
  boundaryLayer.addTo(map);

  // === 3) 限制地圖在邊界內移動與縮放 ===
  map.setMaxBounds(bounds);
  map.on('drag', () => map.panInsideBounds(bounds));
  map.setMinZoom(13);
  map.setMaxZoom(16);

  // === 4) 點擊邏輯：範圍判斷 + 反向地理編碼 ===
  let marker;

  map.on('click', async (event) => {
    const lat = Number(event.latlng.lat.toFixed(6));
    const lng = Number(event.latlng.lng.toFixed(6));

    const latInput = document.getElementById('latInput');
    const lngInput = document.getElementById('lngInput');
    const addrInput = document.getElementById('addressInput');

    // 判斷是否在多邊形範圍內（leaflet-pip 使用 [lng, lat]）
    const inside = leafletPip.pointInLayer([lng, lat], boundaryLayer, true).length > 0;

    if (!inside) {
      // 超出範圍：清空座標、移除標記、顯示警示
      latInput.value = '';
      lngInput.value = '';
      addrInput.value = '⚠️ 超出可選範圍';
      if (marker) map.removeLayer(marker);
      return;
    }

    // 在範圍內：更新座標與標記
    latInput.value = lat;
    lngInput.value = lng;

    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);

    // 顯示查詢中文字
    addrInput.value = '地址解析中…';

    try {
      const controller = new AbortController();
      const id = setTimeout(() => controller.abort(), 8000); // 8 秒逾時

      // 使用 OpenStreetMap 的 Nominatim 反向地理編碼
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
        headers: {
          // 前端不可自訂 User-Agent；生產環境建議走後端代理加上識別
          'Referer': window.location.origin
        }
      });
      clearTimeout(id);

      if (!res.ok) throw new Error('Geocoding failed: ' + res.status);
      const data = await res.json();

      const display = data?.display_name || composeAddress(data?.address);
      addrInput.value = display || '（找不到對應地址）';
    } catch (err) {
      console.error(err);
      addrInput.value = '地址解析失敗，請重試';
    }
  });

  // === 5) 台灣常見格式地址組裝（備用） ===
  function composeAddress(addr) {
    if (!addr) return '';
    const parts = [
      addr.state || addr.region || '',           // 例：嘉義縣
      addr.county || '',
      addr.city || addr.town || addr.village || '', // 例：民雄鄉 / 大林鎮
      addr.suburb || '',
      addr.road || addr.residential || '',       // 路名
      addr.house_number || ''                    // 號
    ].filter(Boolean);
    return parts.join('');
  }
</script>
