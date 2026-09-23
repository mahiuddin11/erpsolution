@extends('backend.layouts.master')
@section('title')
    Settings - {{ $title }}
@endsection

@section('styles')
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Settings </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('settings.account.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.account.index') }}">Account</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Chart of Accounts</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap coa-topbar">
            <div>
                <h3 class="card-title mb-0">Chart of Accounts Tree Map</h3>
                <small class="text-muted">Click a circle to expand/collapse · Hover or click an account to trace its full
                    parent chain · Drag to pan, scroll or use +/− to zoom</small>
            </div>
            <div class="coa-search-wrap">
                <input type="text" id="coaSearch" class="form-control form-control-sm"
                    placeholder="Search account name...">
                <button type="button" id="coaSearchClear" class="btn btn-sm btn-secondary" title="Clear search">✕</button>
                <button type="button" id="coaExpandAll" class="btn btn-sm btn-secondary">Expand all</button>
                <button type="button" id="coaCollapseAll" class="btn btn-sm btn-secondary">Collapse all</button>
            </div>
        </div>

        <div class="card-body p-0">
            <div id="coaLoading" class="text-center text-muted py-5">Loading chart of accounts...</div>

            <div id="coaWrap" class="coa-viewport" style="display:none;">
                <div class="coa-zoom-controls">
                    <button type="button" id="coaZoomIn" class="coa-zbtn" title="Zoom in">+</button>
                    <button type="button" id="coaZoomOut" class="coa-zbtn" title="Zoom out">−</button>
                    <button type="button" id="coaZoomReset" class="coa-zbtn coa-zbtn-wide"
                        title="Reset zoom">Reset</button>
                    <span id="coaZoomLevel" class="coa-zoom-level">100%</span>
                </div>

                <div id="coaTip" class="coa-tip is-placeholder">Hover an account to see its debit/credit summary.</div>

                <div id="coaCanvas" class="coa-canvas">
                    <svg id="coaLinks"></svg>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --coa-bg: #171a21;
            --coa-line: #6f7593;
            --coa-line-active: #e2a23a;
            --coa-root: #585d7d;
            --coa-cat: #454a56;
            --coa-sub: #2c463f;
            --coa-leaf: #223a34;
            --coa-text: #eceef3;
            --coa-toggle-bg: #3a3f4d;
            --coa-active-ring: #e2a23a;
            --coa-dr: #e2895a;
            --coa-cr: #5aa7e2;
            --coa-muted: #9aa0b4;
            --coa-card-bg: #23262f;
            --coa-card-border: #3a3f4d;
        }

        .coa-topbar {
            gap: 12px;
        }

        .coa-search-wrap {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .coa-search-wrap input {
            width: 220px;
        }

        /* viewport = fixed-size window; canvas = the pannable/zoomable content inside it */
        .coa-viewport {
            position: relative;
            width: 100%;
            height: 85vh;
            overflow: hidden;
            background: var(--coa-bg);
            cursor: grab;
            user-select: none;
        }

        .coa-viewport.is-panning {
            cursor: grabbing;
        }

        .coa-canvas {
            position: absolute;
            top: 0;
            left: 0;
            transform-origin: 0 0;
            will-change: transform;
        }

        .coa-canvas svg#coaLinks {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .coa-zoom-controls {
            position: absolute;
            top: 12px;
            right: 16px;
            z-index: 15;
            display: flex;
            gap: 6px;
            align-items: center;
            background: rgba(35, 38, 47, 0.85);
            padding: 6px;
            border-radius: 8px;
            border: 1px solid var(--coa-card-border);
        }

        .coa-zbtn {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--coa-toggle-bg);
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 15px;
            line-height: 1;
        }

        .coa-zbtn:hover {
            filter: brightness(1.2);
        }

        .coa-zbtn-wide {
            width: auto;
            padding: 0 10px;
            font-size: 11px;
        }

        .coa-zoom-level {
            font-size: 11px;
            color: var(--coa-muted);
            min-width: 38px;
            text-align: center;
        }

        .coa-node {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateY(-50%);
        }

        .coa-box {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
            border: 2px solid transparent;
            color: var(--coa-text);
            cursor: pointer;
            transition: border-color .12s ease, transform .12s ease;
        }

        .coa-box.root {
            background: var(--coa-root);
            font-weight: 600;
        }

        .coa-box.cat {
            background: var(--coa-cat);
        }

        .coa-box.sub {
            background: var(--coa-sub);
        }

        .coa-box.leaf {
            background: var(--coa-leaf);
        }

        .coa-box.active {
            border-color: var(--coa-active-ring);
            transform: scale(1.03);
        }

        .coa-box.search-match {
            box-shadow: 0 0 0 2px var(--coa-active-ring), 0 2px 6px rgba(0, 0, 0, .25);
        }

        .coa-toggle {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--coa-toggle-bg);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            flex: 0 0 auto;
            line-height: 1;
        }

        .coa-toggle:hover {
            filter: brightness(1.2);
        }

        .coa-spacer {
            width: 22px;
            flex: 0 0 auto;
        }

        .coa-tip {
            position: absolute;
            top: 56px;
            right: 16px;
            z-index: 15;
            width: 230px;
            background: var(--coa-card-bg);
            border: 1px solid var(--coa-card-border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 12.5px;
            color: var(--coa-text);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .35);
            pointer-events: none;
        }

        .coa-tip.is-placeholder {
            color: var(--coa-muted);
            font-size: 12px;
        }

        .coa-tip .t-title {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .coa-tip .t-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            color: var(--coa-muted);
        }

        .coa-tip .t-row .val {
            color: var(--coa-text);
            font-weight: 600;
        }

        .coa-tip .t-row.dr .val {
            color: var(--coa-dr);
        }

        .coa-tip .t-row.cr .val {
            color: var(--coa-cr);
        }

        .coa-tip .t-type {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid var(--coa-card-border);
            font-size: 11px;
            color: var(--coa-muted);
        }
    </style>

    <script>
        (function() {
            const TREE_DATA_URL = "{{ route('settings.coa.tree.data') }}";

            const COL_WIDTH = 250;
            const ROW_HEIGHT = 56;
            const START_X = 24;
            const START_Y = 40;
            const MIN_SCALE = 0.3;
            const MAX_SCALE = 2.5;

            const loadingEl = document.getElementById('coaLoading');
            const wrap = document.getElementById('coaWrap');
            const canvas = document.getElementById('coaCanvas');
            const svg = document.getElementById('coaLinks');
            const tip = document.getElementById('coaTip');
            const searchInput = document.getElementById('coaSearch');
            const zoomLevelEl = document.getElementById('coaZoomLevel');

            let DATA = null;
            let nodesById = {};

            let searchChain = new Set();
            let lockedChain = new Set();
            let lockedTargetId = null;
            let hoverChain = new Set();

            // ---- pan/zoom state ----
            const view = {
                x: 40,
                y: 0,
                scale: 1
            };
            let isPanning = false;
            let dragMoved = false;
            let panStartClient = {
                x: 0,
                y: 0
            };
            let panStartView = {
                x: 0,
                y: 0
            };

            function applyTransform() {
                canvas.style.transform = `translate(${view.x}px, ${view.y}px) scale(${view.scale})`;
                zoomLevelEl.textContent = Math.round(view.scale * 100) + '%';
            }

            function clamp(v, min, max) {
                return Math.min(max, Math.max(min, v));
            }

            function zoomAt(clientX, clientY, factor) {
                const rect = wrap.getBoundingClientRect();
                const px = clientX - rect.left;
                const py = clientY - rect.top;
                const newScale = clamp(view.scale * factor, MIN_SCALE, MAX_SCALE);
                const ratio = newScale / view.scale;
                view.x = px - (px - view.x) * ratio;
                view.y = py - (py - view.y) * ratio;
                view.scale = newScale;
                applyTransform();
            }

            function zoomAtCenter(factor) {
                const rect = wrap.getBoundingClientRect();
                zoomAt(rect.left + rect.width / 2, rect.top + rect.height / 2, factor);
            }

            // left-button drag to pan
            wrap.addEventListener('mousedown', (e) => {
                if (e.button !== 0) return;
                isPanning = true;
                dragMoved = false;
                panStartClient = {
                    x: e.clientX,
                    y: e.clientY
                };
                panStartView = {
                    x: view.x,
                    y: view.y
                };
                wrap.classList.add('is-panning');
            });

            document.addEventListener('mousemove', (e) => {
                if (!isPanning) return;
                const dx = e.clientX - panStartClient.x;
                const dy = e.clientY - panStartClient.y;
                if (Math.abs(dx) > 3 || Math.abs(dy) > 3) dragMoved = true;
                view.x = panStartView.x + dx;
                view.y = panStartView.y + dy;
                applyTransform();
            });

            document.addEventListener('mouseup', () => {
                isPanning = false;
                wrap.classList.remove('is-panning');
            });

            // scroll wheel to zoom, centered on cursor
            wrap.addEventListener('wheel', (e) => {
                e.preventDefault();
                const factor = e.deltaY < 0 ? 1.12 : 1 / 1.12;
                zoomAt(e.clientX, e.clientY, factor);
            }, {
                passive: false
            });

            document.getElementById('coaZoomIn').onclick = () => zoomAtCenter(1.2);
            document.getElementById('coaZoomOut').onclick = () => zoomAtCenter(1 / 1.2);
            document.getElementById('coaZoomReset').onclick = () => {
                view.x = 40;
                view.y = 0;
                view.scale = 1;
                applyTransform();
            };

            function fmt(n) {
                return '৳' + Number(n).toLocaleString('en-BD');
            }

            function safeName(node) {
                return (node && node.name) ? String(node.name) : '(unnamed)';
            }

            function indexTree(node, parent) {
                node.parent = parent || null;
                node.expanded = node.expanded !== undefined ? node.expanded : (parent ===
                    null);
                nodesById[node.id] = node;
                (node.children || []).forEach(c => indexTree(c, node));
            }

            function ancestorChain(node) {
                const ids = [];
                let cur = node;
                while (cur) {
                    ids.push(cur.id);
                    cur = cur.parent;
                }
                return ids;
            }

            function expandAncestors(node) {
                let cur = node.parent;
                while (cur) {
                    cur.expanded = true;
                    cur = cur.parent;
                }
            }

            function activeSet() {
                const s = new Set();
                searchChain.forEach(id => s.add(id));
                lockedChain.forEach(id => s.add(id));
                hoverChain.forEach(id => s.add(id));
                return s;
            }

            function layout(root) {
                let cursorY = START_Y;
                const visible = [];

                function walk(node, depth) {
                    node.x = START_X + depth * COL_WIDTH;
                    const hasChildren = node.children && node.children.length;
                    const isOpen = node.expanded !== false;
                    visible.push(node);
                    if (hasChildren && isOpen) {
                        node.children.forEach(c => walk(c, depth + 1));
                        const first = node.children[0].y;
                        const last = node.children[node.children.length - 1].y;
                        node.y = (first + last) / 2;
                    } else {
                        node.y = cursorY;
                        cursorY += ROW_HEIGHT;
                    }
                }
                walk(root, 0);
                return {
                    visible,
                    height: cursorY
                };
            }

            function render() {
                const {
                    visible,
                    height
                } = layout(DATA);
                const active = activeSet();

                let maxX = 0;
                visible.forEach(n => {
                    if (n.x > maxX) maxX = n.x;
                });
                const canvasWidth = maxX + COL_WIDTH + 40;
                const canvasHeight = Math.max(height + 40, 400);

                canvas.style.width = canvasWidth + 'px';
                canvas.style.height = canvasHeight + 'px';
                svg.setAttribute('width', canvasWidth);
                svg.setAttribute('height', canvasHeight);
                svg.innerHTML = '';
                canvas.querySelectorAll('.coa-node').forEach(el => el.remove());

                visible.forEach(node => {
                    if (node.children && node.children.length && node.expanded !== false) {
                        node.children.forEach(child => {
                            const x1 = node.x + (node._boxWidth || 170);
                            const y1 = node.y,
                                x2 = child.x,
                                y2 = child.y;
                            const midX = (x1 + x2) / 2;
                            const isActive = active.has(node.id) && active.has(child.id);
                            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                            path.setAttribute('d',
                                `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`);
                            path.setAttribute('stroke', isActive ? 'var(--coa-line-active)' :
                                'var(--coa-line)');
                            path.setAttribute('stroke-width', isActive ? '2.5' : '1.5');
                            path.setAttribute('fill', 'none');
                            path.setAttribute('opacity', isActive ? '1' : '0.85');
                            svg.appendChild(path);
                        });
                    }
                });

                visible.forEach(node => {
                    const div = document.createElement('div');
                    div.className = 'coa-node';
                    div.style.left = node.x + 'px';
                    div.style.top = node.y + 'px';

                    const box = document.createElement('div');
                    box.className = 'coa-box ' + node.type;
                    if (active.has(node.id)) box.classList.add('active');
                    if (node._searchMatch) box.classList.add('search-match');
                    box.textContent = safeName(node);
                    div.appendChild(box);

                    const hasChildren = node.children && node.children.length;
                    if (hasChildren) {
                        const btn = document.createElement('button');
                        btn.className = 'coa-toggle';
                        btn.textContent = node.expanded !== false ? '‹' : '›';
                        btn.onclick = (e) => {
                            e.stopPropagation();
                            node.expanded = node.expanded === false ? true : false;
                            render();
                        };
                        div.appendChild(btn);
                    } else {
                        const sp = document.createElement('div');
                        sp.className = 'coa-spacer';
                        div.appendChild(sp);
                    }

                    box.addEventListener('mouseenter', () => {
                        hoverChain = new Set(ancestorChain(node));
                        render();
                        showTip(node);
                    });
                    box.addEventListener('mouseleave', () => {
                        hoverChain = new Set();
                        resetTip();
                        render();
                    });

                    // click toggles the locked highlight chain — but a drag that
                    // passed through this box should NOT count as a click
                    box.addEventListener('click', () => {
                        if (dragMoved) {
                            dragMoved = false;
                            return;
                        }
                        if (lockedTargetId === node.id) {
                            lockedChain = new Set();
                            lockedTargetId = null;
                        } else {
                            lockedChain = new Set(ancestorChain(node));
                            lockedTargetId = node.id;
                        }
                        render();
                    });

                    canvas.appendChild(div);
                    requestAnimationFrame(() => {
                        node._boxWidth = box.offsetWidth + 32;
                    });
                });
            }

            function showTip(node) {
                const balance = node.balance_type === 'credit' ? (node.credit - node.debit) : (node.debit - node
                    .credit);
                const label = balance >= 0 ? (node.balance_type === 'credit' ? 'CR' : 'DR') : (node.balance_type ===
                    'credit' ? 'DR' : 'CR');
                tip.classList.remove('is-placeholder');
                tip.innerHTML = `
      <div class="t-title">${safeName(node)}</div>
      <div class="t-row dr"><span>Total Debit</span><span class="val">${fmt(node.debit)}</span></div>
      <div class="t-row cr"><span>Total Credit</span><span class="val">${fmt(node.credit)}</span></div>
      <div class="t-row"><span>Net Balance</span><span class="val">${fmt(Math.abs(balance))} ${label}</span></div>
      <div class="t-type">balance_type: ${node.balance_type || '—'}</div>
    `;
            }

            function resetTip() {
                tip.classList.add('is-placeholder');
                tip.textContent = 'Hover an account to see its debit/credit summary.';
            }

            function setAllExpanded(node, val) {
                if (node.children && node.children.length) {
                    node.expanded = val;
                    node.children.forEach(c => setAllExpanded(c, val));
                }
            }

            function runSearch(query) {
                Object.values(nodesById).forEach(n => {
                    n._searchMatch = false;
                });
                searchChain = new Set();

                const q = query.trim().toLowerCase();
                if (!q) {
                    render();
                    return;
                }


                Object.values(nodesById).forEach(n => {
                    if (n !== DATA) n.expanded = false;
                });

                let firstMatch = null;
                Object.values(nodesById).forEach(node => {
                    if (safeName(node).toLowerCase().includes(q)) {
                        node._searchMatch = true;
                        if (!firstMatch) firstMatch = node;
                        ancestorChain(node).forEach(id => searchChain.add(id));
                        expandAncestors(node);
                    }
                });

                render();

                if (firstMatch) {
                    requestAnimationFrame(() => {

                        const rect = wrap.getBoundingClientRect();
                        view.x = rect.width / 2 - firstMatch.x * view.scale;
                        view.y = rect.height / 2 - firstMatch.y * view.scale;
                        applyTransform();
                    });
                }
            }

            searchInput.addEventListener('input', (e) => {
                clearTimeout(window._coaSearchTimer);
                window._coaSearchTimer = setTimeout(() => runSearch(e.target.value), 150);
            });
            document.getElementById('coaSearchClear').onclick = () => {
                searchInput.value = '';
                runSearch('');
            };
            document.getElementById('coaExpandAll').onclick = () => {
                setAllExpanded(DATA, true);
                render();
            };
            document.getElementById('coaCollapseAll').onclick = () => {
                DATA.expanded = true;
                DATA.children.forEach(c => setAllExpanded(c, false));
                render();
            };

            fetch(TREE_DATA_URL, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    DATA = data;
                    indexTree(DATA, null);
                    DATA.expanded = true;
                    loadingEl.style.display = 'none';
                    wrap.style.display = 'block';
                    applyTransform();
                    render();
                })
                .catch(err => {
                    loadingEl.textContent = 'Failed to load chart of accounts.';
                    console.error(err);
                });
        })();
    </script>
@endsection
