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
      <div id="addrHint" class="form-text">點地圖後會自動解析地址。</div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  const map = L.map('map').setView([23.4921, 120.4597], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map);

  // 可選區域（民雄、 大林）示意
  const latlngs = [
    [23.50, 120.45],
    [23.55, 120.45],
    [23.55, 120.58],
    [23.50, 120.58]
  ];
  const polygon = L.polygon(latlngs, { color: 'blue' }).addTo(map);
  const bounds = polygon.getBounds();
  map.setMaxBounds(bounds);
  map.on('drag', () => map.panInsideBounds(bounds));
  map.setMinZoom(13);
  map.setMaxZoom(16);

  let marker;

  map.on('click', async (event) => {
    const lat = Number(event.latlng.lat.toFixed(6));
    const lng = Number(event.latlng.lng.toFixed(6));

    // 更新座標欄位
    document.getElementById('latInput').value = lat;
    document.getElementById('lngInput').value = lng;

    // 標記點
    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);

    // 顯示「查詢中」
    const addrInput = document.getElementById('addressInput');
    addrInput.value = '地址解析中…';

    try {
      const controller = new AbortController();
      const id = setTimeout(() => controller.abort(), 8000); // 8s timeout

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
          // 瀏覽器不允許自訂 User-Agent；這裡先省略。
          // 生產環境建議改走後端代理，見「方案 B」
          'Referer': window.location.origin
        }
      });
      clearTimeout(id);

      if (!res.ok) throw new Error('Geocoding failed: ' + res.status);
      const data = await res.json();

      // 取用 display_name 或自行組裝門牌
      const display = data?.display_name || composeAddress(data?.address);
      addrInput.value = display || '（找不到對應地址）';
    } catch (err) {
      console.error(err);
      addrInput.value = '地址解析失敗，請重試';
    }
  });

  function composeAddress(addr) {
    if (!addr) return '';
    // 優先組合台灣常見格式：縣市 + 區 + 路名 + 號
    const parts = [
      addr.state || addr.region || '',
      addr.county || '',
      addr.city || addr.town || addr.village || '',
      addr.suburb || '',
      addr.road || addr.residential || '',
      addr.house_number || '',
    ].filter(Boolean);
    return parts.join('');
  }
</script>
