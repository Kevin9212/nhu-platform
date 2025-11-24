<?
import React, { useEffect, useMemo, useState } from "react";

// --- Types ---
type Product = {
  id: string;
  title: string;
  price: number;
  image: string;
  rating?: number;
  tags?: string[];
};

// --- Utils ---
const currency = (n: number) => n.toLocaleString(undefined, { style: "currency", currency: "TWD", maximumFractionDigits: 0 });

const FAVORITES_KEY = "favorites:v1";

function loadFavorites(): Product[] {
  try {
    const raw = localStorage.getItem(FAVORITES_KEY);
    if (!raw) return [];
    return JSON.parse(raw) as Product[];
  } catch {
    return [];
  }
}

function saveFavorites(list: Product[]) {
  localStorage.setItem(FAVORITES_KEY, JSON.stringify(list));
}

// Mock: seed some favorites on first visit for demo
const DEMO_SEED: Product[] = [
  { id: "p-1001", title: "超柔棉 短袖T恤", price: 399, image: "https://images.unsplash.com/photo-1520975922329-7da8b2a98e46?q=80&w=1200&auto=format&fit=crop", rating: 4.5, tags: ["夏季", "熱銷"] },
  { id: "p-1002", title: "輕量運動鞋", price: 1690, image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop", rating: 4.2, tags: ["限時折扣"] },
  { id: "p-1003", title: "極簡帆布托特包", price: 890, image: "https://images.unsplash.com/photo-1520975929543-6e0aeb5b7b72?q=80&w=1200&auto=format&fit=crop", rating: 4.8, tags: ["新品"] },
  { id: "p-1004", title: "不鏽鋼保溫瓶 600ml", price: 620, image: "https://images.unsplash.com/photo-1526401281623-2b3c20e3e3f6?q=80&w=1200&auto=format&fit=crop", rating: 4.3 },
];

// --- Main Component ---
export default function FavoritesPage() {
  const [favorites, setFavorites] = useState<Product[]>([]);
  const [selected, setSelected] = useState<Record<string, boolean>>({});
  const [q, setQ] = useState("");
  const [sort, setSort] = useState<"new" | "priceAsc" | "priceDesc" | "rating">("new");
  const [view, setView] = useState<"grid" | "list">("grid");

  // init
  useEffect(() => {
    const first = loadFavorites();
    if (first.length === 0) {
      saveFavorites(DEMO_SEED);
      setFavorites(DEMO_SEED);
    } else {
      setFavorites(first);
    }
  }, []);

  // derived
  const filtered = useMemo(() => {
    const kw = q.trim().toLowerCase();
    let data = favorites.filter((p) => (kw ? p.title.toLowerCase().includes(kw) : true));
    switch (sort) {
      case "priceAsc":
        data = [...data].sort((a, b) => a.price - b.price);
        break;
      case "priceDesc":
        data = [...data].sort((a, b) => b.price - a.price);
        break;
      case "rating":
        data = [...data].sort((a, b) => (b.rating ?? 0) - (a.rating ?? 0));
        break;
      default:
        data = [...data]; // new: keep storage order
    }
    return data;
  }, [favorites, q, sort]);

  const allSelected = filtered.length > 0 && filtered.every((p) => selected[p.id]);
  const selectedIds = Object.keys(selected).filter((k) => selected[k]);

  // actions
  function toggleSelectAll() {
    if (allSelected) {
      const copy = { ...selected };
      filtered.forEach((p) => (copy[p.id] = false));
      setSelected(copy);
    } else {
      const copy = { ...selected };
      filtered.forEach((p) => (copy[p.id] = true));
      setSelected(copy);
    }
  }

  function removeOne(id: string) {
    const next = favorites.filter((p) => p.id !== id);
    setFavorites(next);
    saveFavorites(next);
    setSelected((s) => ({ ...s, [id]: false }));
  }

  function removeSelected() {
    if (selectedIds.length === 0) return;
    const setIds = new Set(selectedIds);
    const next = favorites.filter((p) => !setIds.has(p.id));
    setFavorites(next);
    saveFavorites(next);
    setSelected({});
  }

  function clearAll() {
    setFavorites([]);
    saveFavorites([]);
    setSelected({});
  }

  function addToCart(id: string) {
    // Replace with real cart API call
    alert(`已加入購物車：${id}`);
  }

  function importFromJSON(text: string) {
    try {
      const arr = JSON.parse(text) as Product[];
      if (!Array.isArray(arr)) throw new Error("Invalid");
      saveFavorites(arr);
      setFavorites(arr);
      setSelected({});
    } catch (e) {
      alert("JSON 格式不正確");
    }
  }

  function exportJSON() {
    const blob = new Blob([JSON.stringify(favorites, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "favorites.json";
    a.click();
    URL.revokeObjectURL(url);
  }

  return (
    <div className="mx-auto max-w-7xl p-4 md:p-8">
      {/* Header */}
      <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <h1 className="text-2xl md:text-3xl font-semibold">我的收藏</h1>
          <p className="text-sm text-gray-500 mt-1">共 {favorites.length} 件商品</p>
        </div>
        <div className="flex flex-wrap gap-2">
          <button onClick={exportJSON} className="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">匯出JSON</button>
          <label className="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 cursor-pointer">
            匯入JSON
            <input type="file" accept="application/json" className="hidden" onChange={(e) => {
              const f = e.target.files?.[0];
              if (!f) return;
              f.text().then(importFromJSON);
            }} />
          </label>
          <button onClick={clearAll} className="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">清空全部</button>
        </div>
      </div>

      {/* Toolbar */}
      <div className="mt-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div className="flex items-center gap-2">
          <input value={q} onChange={(e) => setQ(e.target.value)} placeholder="搜尋收藏商品..."
                 className="w-72 max-w-full px-4 py-2 rounded-xl border outline-none focus:ring-2" />
          <select value={sort} onChange={(e) => setSort(e.target.value as any)} className="px-3 py-2 rounded-xl border">
            <option value="new">最新加入</option>
            <option value="priceAsc">價格：低→高</option>
            <option value="priceDesc">價格：高→低</option>
            <option value="rating">評分</option>
          </select>
          <div className="ml-2 inline-flex rounded-xl border overflow-hidden">
            <button onClick={() => setView("grid")} className={`px-3 py-2 ${view === "grid" ? "bg-gray-900 text-white" : "bg-white"}`}>網格</button>
            <button onClick={() => setView("list")} className={`px-3 py-2 ${view === "list" ? "bg-gray-900 text-white" : "bg-white"}`}>列表</button>
          </div>
        </div>
        <div className="flex items-center gap-3">
          <label className="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" checked={allSelected} onChange={toggleSelectAll} />
            全選（{selectedIds.length}）
          </label>
          <button onClick={removeSelected} disabled={selectedIds.length === 0}
                  className={`px-3 py-2 rounded-xl ${selectedIds.length ? "bg-red-600 text-white" : "bg-gray-200 text-gray-400"}`}>
            移除已選
          </button>
        </div>
      </div>

      {/* Content */}
      {filtered.length === 0 ? (
        <EmptyState onSeed={() => { saveFavorites(DEMO_SEED); setFavorites(DEMO_SEED); }} />
      ) : view === "grid" ? (
        <div className="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {filtered.map((p) => (
            <article key={p.id} className="group relative rounded-2xl border shadow-sm overflow-hidden bg-white">
              <img src={p.image} alt={p.title} className="h-48 w-full object-cover" />
              <div className="p-4">
                <div className="flex items-start justify-between gap-3">
                  <h3 className="font-semibold leading-snug line-clamp-2">{p.title}</h3>
                  <label className="shrink-0 inline-flex items-center gap-2 text-xs text-gray-500">
                    <input type="checkbox" checked={!!selected[p.id]} onChange={(e) => setSelected((s) => ({ ...s, [p.id]: e.target.checked }))} />
                    選取
                  </label>
                </div>
                <div className="mt-1 text-sm text-gray-500">{p.rating ? `★ ${p.rating}` : "無評分"}</div>
                <div className="mt-2 font-semibold text-lg">{currency(p.price)}</div>
                {p.tags && (
                  <div className="mt-2 flex flex-wrap gap-2">
                    {p.tags.map((t) => (
                      <span key={t} className="text-xs bg-gray-100 rounded-full px-2 py-1">#{t}</span>
                    ))}
                  </div>
                )}
                <div className="mt-4 flex items-center gap-2">
                  <button onClick={() => addToCart(p.id)} className="flex-1 px-3 py-2 rounded-xl bg-gray-900 text-white hover:opacity-90">加入購物車</button>
                  <button onClick={() => removeOne(p.id)} className="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">取消收藏</button>
                </div>
              </div>
            </article>
          ))}
        </div>
      ) : (
        <div className="mt-6 divide-y rounded-2xl border bg-white">
          {filtered.map((p) => (
            <article key={p.id} className="p-4 flex items-center gap-4">
              <img src={p.image} alt={p.title} className="h-20 w-20 rounded-xl object-cover" />
              <div className="flex-1 min-w-0">
                <h3 className="font-medium truncate">{p.title}</h3>
                <div className="mt-1 text-sm text-gray-500 flex items-center gap-3">
                  <span>{p.rating ? `★ ${p.rating}` : "無評分"}</span>
                  <span className="font-semibold text-base text-gray-900">{currency(p.price)}</span>
                  {p.tags && (
                    <div className="flex flex-wrap gap-2">
                      {p.tags.map((t) => (
                        <span key={t} className="text-xs bg-gray-100 rounded-full px-2 py-1">#{t}</span>
                      ))}
                    </div>
                  )}
                </div>
              </div>
              <div className="flex items-center gap-2">
                <label className="flex items-center gap-2 text-xs text-gray-500">
                  <input type="checkbox" checked={!!selected[p.id]} onChange={(e) => setSelected((s) => ({ ...s, [p.id]: e.target.checked }))} />
                  選取
                </label>
                <button onClick={() => addToCart(p.id)} className="px-3 py-2 rounded-xl bg-gray-900 text-white hover:opacity-90">加入購物車</button>
                <button onClick={() => removeOne(p.id)} className="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">取消收藏</button>
              </div>
            </article>
          ))}
        </div>
      )}

      {/* Dev Helpers */}
      <div className="mt-8 text-xs text-gray-400">
        <p>此頁面為前端範本。實務上請以 API 串接會員收藏資料（GET /me/favorites、POST /me/favorites/:id、DELETE /me/favorites/:id）。</p>
      </div>
    </div>
  );
}

function EmptyState({ onSeed }: { onSeed: () => void }) {
  return (
    <div className="mt-10 flex flex-col items-center justify-center rounded-3xl border border-dashed p-10 text-center bg-white">
      <div className="text-6xl">🤍</div>
      <h2 className="mt-4 text-xl font-semibold">還沒有收藏的商品</h2>
      <p className="mt-2 text-sm text-gray-500">前往商品頁點擊「收藏」即可加入此列表。</p>
      <div className="mt-6 flex gap-2">
        <button onClick={onSeed} className="px-4 py-2 rounded-xl bg-gray-900 text-white">載入示例資料</button>
        <a href="/products" className="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">去逛逛</a>
      </div>
    </div>
  );
}
