<template>
    <header class="topbar">
        <div class="topbar-brand">mini<span>shop</span>kh</div>
        <div class="topbar-sep"></div>
        <div class="topbar-title">Admin Dashboard</div>
        <div class="topbar-right">
            <div class="refresh-indicator">
                <span class="refresh-dot"></span>
                <span>{{ syncStatus }}</span>
            </div>
            <form method="POST" action="/admin/logout" style="margin:0">
                <input type="hidden" name="_token" :value="csrf">
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </header>

    <main class="main">

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card" style="--accent:#3b82f6">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ stats.total }}</div>
                <div class="stat-sub">All time</div>
            </div>
            <div class="stat-card" style="--accent:#22c55e">
                <div class="stat-label">Paid</div>
                <div class="stat-value">{{ stats.paid }}</div>
                <div class="stat-sub">Confirmed payments</div>
            </div>
            <div class="stat-card" style="--accent:#f59e0b">
                <div class="stat-label">Pending</div>
                <div class="stat-value">{{ stats.pending }}</div>
                <div class="stat-sub">Awaiting payment</div>
            </div>
            <div class="stat-card" style="--accent:#a78bfa">
                <div class="stat-label">Revenue</div>
                <div class="stat-value">${{ stats.revenue }}</div>
                <div class="stat-sub">From paid orders</div>
            </div>
        </div>

        <!-- Orders panel -->
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Orders</span>
                <span class="order-count">{{ filteredOrders.length }} shown</span>
                <div class="panel-actions">
                    <div class="filter-pills">
                        <button
                            v-for="f in filters"
                            :key="f.value"
                            class="pill"
                            :class="{ active: activeFilter === f.value }"
                            @click="activeFilter = f.value"
                        >{{ f.label }}</button>
                    </div>
                    <div class="search-wrap">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input
                            v-model="searchQuery"
                            class="search-input"
                            placeholder="Search order #..."
                        >
                    </div>
                    <button class="btn-refresh" title="Refresh" @click="syncData">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="{ spinning: syncing }">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="empty-state">
                <p style="color:rgba(255,255,255,0.3)">Loading orders...</p>
            </div>

            <!-- Empty -->
            <div v-else-if="filteredOrders.length === 0" class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <p>No orders found</p>
                <small>{{ searchQuery || activeFilter !== 'all' ? 'Try clearing the filters.' : 'Orders will appear here once customers check out.' }}</small>
            </div>

            <!-- Table -->
            <table v-else>
                <thead>
                    <tr>
                        <th style="width:32px"></th>
                        <th>Order #</th>
                        <th>Status</th>
                        <th class="th-items">Items</th>
                        <th>Amount</th>
                        <th class="th-date">Date</th>
                        <th class="th-action">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="order in filteredOrders" :key="order.id">
                        <!-- Main row -->
                        <tr :id="'row-' + order.id">
                            <td style="padding-left:16px">
                                <button
                                    class="expand-btn"
                                    :class="{ open: openRows[order.id] }"
                                    @click="toggleRow(order.id)"
                                    title="Show items"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </button>
                            </td>
                            <td><span class="order-num">{{ order.order_number }}</span></td>
                            <td>
                                <span :class="'badge badge-' + order.status">{{ ucfirst(order.status) }}</span>
                            </td>
                            <td class="col-items">
                                <div class="items-preview">
                                    <span v-for="item in order.items.slice(0, 2)" :key="item.product_name">
                                        {{ item.quantity }}× {{ item.product_name }}
                                    </span>
                                    <span v-if="order.items.length > 2" class="items-more">
                                        +{{ order.items.length - 2 }} more
                                    </span>
                                </div>
                            </td>
                            <td><span class="amount">${{ parseFloat(order.total_amount).toFixed(2) }}</span></td>
                            <td class="col-date"><div class="date">{{ order.created_at }}</div></td>
                            <td class="col-action">
                                <div class="action-row">
                                    <button
                                        v-if="order.status !== 'paid'"
                                        class="btn-status btn-mark-paid"
                                        @click="updateStatus(order.id, 'paid')"
                                    >Mark Paid</button>
                                    <button
                                        v-if="order.status !== 'cancelled'"
                                        class="btn-status btn-mark-cancelled"
                                        @click="updateStatus(order.id, 'cancelled')"
                                    >Cancel</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Expandable detail row -->
                        <tr class="detail-row" :id="'detail-' + order.id">
                            <td colspan="7">
                                <div class="detail-inner" :class="{ open: openRows[order.id] }">
                                    <div class="detail-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Unit Price</th>
                                                    <th>Qty</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in order.items" :key="item.product_name">
                                                    <td>
                                                        <span class="item-name">
                                                            <img v-if="item.product_image" :src="item.product_image" :alt="item.product_name" class="item-img">
                                                            {{ item.product_name }}
                                                        </span>
                                                    </td>
                                                    <td>${{ parseFloat(item.unit_price).toFixed(2) }}</td>
                                                    <td>{{ item.quantity }}</td>
                                                    <td style="color:#fff;font-weight:600">${{ parseFloat(item.subtotal).toFixed(2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" style="text-align:right;font-weight:700;color:rgba(255,255,255,0.5);padding-top:12px">Total</td>
                                                    <td style="color:#fff;font-weight:800;font-size:14px;padding-top:12px">
                                                        ${{ parseFloat(order.total_amount).toFixed(2) }} {{ order.currency }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="order.paid_at" style="margin-top:10px;font-size:11.5px;color:rgba(255,255,255,0.3)">
                                            Paid at: {{ order.paid_at }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </main>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';

const csrf = document.querySelector('meta[name="csrf-token"]').content;

const stats      = ref({ total: 0, paid: 0, pending: 0, revenue: '0.00' });
const orders     = ref([]);
const openRows   = reactive({});   // { [orderId]: boolean } — never reset by sync
const activeFilter = ref('all');
const searchQuery  = ref('');
const syncStatus   = ref('Connecting...');
const loading      = ref(true);
const syncing      = ref(false);

const filters = [
    { value: 'all',       label: 'All' },
    { value: 'paid',      label: 'Paid' },
    { value: 'pending',   label: 'Pending' },
    { value: 'cancelled', label: 'Cancelled' },
];

const ucfirst = s => s.charAt(0).toUpperCase() + s.slice(1);

const filteredOrders = computed(() =>
    orders.value.filter(o => {
        const statusOk = activeFilter.value === 'all' || o.status === activeFilter.value;
        const searchOk = !searchQuery.value ||
            o.order_number.toLowerCase().includes(searchQuery.value.toLowerCase());
        return statusOk && searchOk;
    })
);

function toggleRow(id) {
    openRows[id] = !openRows[id];
}

async function updateStatus(id, status) {
    try {
        const res  = await fetch(`/admin/orders/${id}/status`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body:    JSON.stringify({ status }),
        });
        const data = await res.json();
        if (data.ok) {
            await syncData();
        }
    } catch {
        alert('Failed to update order status. Please try again.');
    }
}

async function syncData() {
    syncing.value = true;
    try {
        const res  = await fetch('/admin/data');
        const data = await res.json();
        if (data.error) return;
        stats.value  = data.stats;
        orders.value = data.orders;
        const now = new Date();
        syncStatus.value = `Synced ${now.getHours()}:${String(now.getMinutes()).padStart(2, '0')}`;
    } catch {
        syncStatus.value = 'Sync failed';
    } finally {
        loading.value  = false;
        syncing.value  = false;
    }
}

let syncInterval;
onMounted(async () => {
    await syncData();
    syncInterval = setInterval(syncData, 2000);
});
onUnmounted(() => clearInterval(syncInterval));
</script>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
    background: #060b14;
    color: #e2e8f0;
    min-height: 100vh;
    font-size: 14px;
    line-height: 1.5;
}

/* ── Topbar ── */
.topbar {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(6, 11, 20, 0.92);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255,255,255,0.07);
    padding: 0 28px;
    height: 60px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.topbar-brand { font-size: 15px; font-weight: 800; letter-spacing: -0.02em; color: #fff; }
.topbar-brand span { color: #3b82f6; }
.topbar-sep { width: 1px; height: 18px; background: rgba(255,255,255,0.12); }
.topbar-title { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.45); }
.topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }

.refresh-indicator {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; color: rgba(255,255,255,0.28);
}
.refresh-dot {
    width: 6px; height: 6px; border-radius: 50%; background: #22c55e;
    animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.4; transform: scale(0.7); }
}

.btn-logout {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
    padding: 6px 14px;
    color: rgba(255,255,255,0.55);
    font-size: 12px; font-weight: 600; letter-spacing: 0.04em;
    cursor: pointer; font-family: inherit;
    transition: background 0.2s, color 0.2s;
}
.btn-logout:hover { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); color: #fca5a5; }

/* ── Main ── */
.main { max-width: 1320px; margin: 0 auto; padding: 32px 28px 60px; }

/* ── Stats ── */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 32px; }
.stat-card {
    background: #0d1526;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 10px;
    padding: 22px 24px;
    position: relative; overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: var(--accent, #3b82f6); opacity: 0.6;
}
.stat-label { font-size: 10.5px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(255,255,255,0.3); margin-bottom: 10px; }
.stat-value { font-size: 32px; font-weight: 800; letter-spacing: -0.03em; color: #fff; line-height: 1; }
.stat-sub   { font-size: 11px; color: rgba(255,255,255,0.25); margin-top: 6px; }

/* ── Panel ── */
.panel { background: #0d1526; border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; overflow: hidden; }
.panel-header {
    display: flex; align-items: center; gap: 16px;
    padding: 18px 22px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    flex-wrap: wrap;
}
.panel-title  { font-size: 14px; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.order-count  {
    font-size: 11px;
    background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.25);
    color: #93c5fd; border-radius: 20px; padding: 2px 10px; font-weight: 600;
}
.panel-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* filter pills */
.filter-pills { display: flex; gap: 4px; }
.pill {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 5px 13px;
    font-size: 11.5px; font-weight: 600;
    color: rgba(255,255,255,0.4);
    cursor: pointer; font-family: inherit;
    transition: background 0.2s, border-color 0.2s, color 0.2s;
}
.pill:hover, .pill.active { background: rgba(59,130,246,0.15); border-color: rgba(59,130,246,0.4); color: #93c5fd; }
.pill.active { pointer-events: none; }

/* search */
.search-wrap { position: relative; }
.search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.25); pointer-events: none; }
.search-input {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 7px;
    padding: 7px 12px 7px 32px;
    color: #fff; font-size: 12.5px; font-family: inherit;
    width: 200px; outline: none;
    transition: border-color 0.2s;
}
.search-input:focus { border-color: rgba(59,130,246,0.5); }
.search-input::placeholder { color: rgba(255,255,255,0.22); }

.btn-refresh {
    background: none; border: none;
    color: rgba(255,255,255,0.3);
    cursor: pointer; padding: 6px; border-radius: 5px;
    display: flex; align-items: center;
    transition: color 0.2s;
}
.btn-refresh:hover { color: #fff; }
.btn-refresh svg.spinning { animation: spin 0.6s linear infinite; }

@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* ── Table ── */
table { width: 100%; border-collapse: collapse; }
thead th {
    padding: 11px 16px;
    text-align: left; font-size: 10.5px; font-weight: 700;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: rgba(255,255,255,0.28);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    white-space: nowrap;
}
tbody tr { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: rgba(255,255,255,0.025); }
td { padding: 14px 16px; vertical-align: middle; }

.order-num { font-family: 'Courier New', monospace; font-size: 13px; font-weight: 700; color: #fff; letter-spacing: 0.03em; }

/* badges */
.badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase;
}
.badge::before { content: ''; display: block; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.badge-paid      { background: rgba(34,197,94,0.12);  color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
.badge-pending   { background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
.badge-cancelled { background: rgba(239,68,68,0.12);  color: #f87171; border: 1px solid rgba(239,68,68,0.25); }

.amount { font-weight: 700; color: #fff; font-size: 14px; }
.date   { color: rgba(255,255,255,0.38); font-size: 12.5px; white-space: nowrap; }

.items-preview { display: flex; flex-direction: column; gap: 3px; font-size: 12px; color: rgba(255,255,255,0.5); max-width: 200px; }
.items-preview span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.items-more { color: rgba(255,255,255,0.25); font-style: italic; }

.action-row { display: flex; align-items: center; gap: 6px; }
.btn-status {
    padding: 5px 11px; border-radius: 5px;
    font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase;
    cursor: pointer; font-family: inherit; border: 1px solid transparent;
    transition: background 0.2s, border-color 0.2s, color 0.2s, opacity 0.2s;
}
.btn-mark-paid      { background: rgba(34,197,94,0.1);  border-color: rgba(34,197,94,0.25);  color: #4ade80; }
.btn-mark-paid:hover { background: rgba(34,197,94,0.2); }
.btn-mark-cancelled      { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.2); color: #f87171; }
.btn-mark-cancelled:hover { background: rgba(239,68,68,0.2); }
.btn-status:disabled { opacity: 0.35; cursor: not-allowed; }

/* ── Expand row ── */
.expand-btn {
    background: none; border: none; color: rgba(255,255,255,0.3);
    cursor: pointer; padding: 4px 6px; border-radius: 4px;
    display: flex; align-items: center;
    transition: color 0.2s, background 0.2s;
}
.expand-btn:hover { color: #fff; background: rgba(255,255,255,0.07); }
.expand-btn svg { transition: transform 0.25s ease; }
.expand-btn.open svg { transform: rotate(180deg); }

.detail-row td { padding: 0; background: rgba(255,255,255,0.018); }
.detail-inner { overflow: hidden; max-height: 0; transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
.detail-inner.open { max-height: 600px; }

.detail-table { width: 100%; padding: 0 16px 14px 40px; }
.detail-table table { width: 100%; border-collapse: collapse; }
.detail-table th { padding: 8px 10px; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(255,255,255,0.22); border-bottom: 1px solid rgba(255,255,255,0.05); }
.detail-table td { padding: 9px 10px; font-size: 12.5px; color: rgba(255,255,255,0.6); border-bottom: 1px solid rgba(255,255,255,0.03); }
.detail-table tr:last-child td { border-bottom: none; }

.item-img  { width: 36px; height: 36px; object-fit: cover; border-radius: 4px; background: rgba(255,255,255,0.05); vertical-align: middle; margin-right: 8px; }
.item-name { display: flex; align-items: center; color: rgba(255,255,255,0.8); font-weight: 600; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 72px 24px; color: rgba(255,255,255,0.22); }
.empty-state svg { margin-bottom: 16px; opacity: 0.3; }
.empty-state p   { font-size: 15px; font-weight: 500; }
.empty-state small { font-size: 12px; display: block; margin-top: 6px; opacity: 0.6; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .col-items, .th-items { display: none; }
}
@media (max-width: 600px) {
    .main { padding: 20px 16px 40px; }
    .topbar { padding: 0 16px; }
    .stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
    .stat-value { font-size: 24px; }
    .panel-header { gap: 10px; }
    .search-input { width: 140px; }
    .col-date, .th-date, .col-action, .th-action { display: none; }
}
</style>
