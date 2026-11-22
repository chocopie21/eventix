/**
 * EVENTIX - Catalog Real-time Live Search & Filter
 * Modern Minimalist Card Rendering
 */

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const dateFilter  = document.getElementById('date-filter');
    const resetFilterBtn = document.getElementById('reset-filter');
    const eventsGrid  = document.getElementById('events-grid');
    const eventsCount = document.getElementById('events-count');
    const categoryPills = document.querySelectorAll('.category-pill');

    if (!eventsGrid) return;

    let debounceTimer = null;
    let currentAbortController = null;

    /**
     * Escape HTML untuk proteksi XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Tampilkan Skeleton Loading saat Fetch data
     */
    function renderLoadingSkeleton() {
        let skeletons = '';
        for (let i = 0; i < 3; i++) {
            skeletons += `
                <div class="modern-card animate-pulse bg-white border border-slate-200/80 rounded-2xl overflow-hidden">
                    <div class="w-full aspect-[16/10] bg-slate-200"></div>
                    <div class="p-5 space-y-3">
                        <div class="h-3 w-28 bg-slate-200 rounded"></div>
                        <div class="h-4 w-4/5 bg-slate-200 rounded"></div>
                        <div class="h-3 w-3/5 bg-slate-200 rounded"></div>
                        <div class="pt-3.5 border-t border-slate-100 flex justify-between items-center">
                            <div class="h-4 w-24 bg-slate-200 rounded"></div>
                            <div class="h-9 w-24 bg-slate-200 rounded-xl"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        eventsGrid.innerHTML = skeletons;
    }

    /**
     * Render Template Kartu Konser
     */
    function createEventCard(event) {
        const posterSrc = event.poster_url || 'assets/uploads/posters/default.jpg';
        const isSoldOut = event.is_sold_out;

        return `
            <article class="modern-card flex flex-col justify-between overflow-hidden group">
                <!-- Poster -->
                <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 rounded-t-2xl">
                    <img src="${escapeHtml(posterSrc)}" alt="${escapeHtml(event.nama_event)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                    
                    <!-- Genre Badge -->
                    <div class="absolute top-3 left-3 z-10">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#0F172A]/80 backdrop-blur-md text-white shadow-xs">
                            ${escapeHtml(event.genre_musik || 'Konser')}
                        </span>
                    </div>

                    <!-- Kuota Badge -->
                    <div class="absolute top-3 right-3 z-10">
                        ${isSoldOut ? `
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-600/90 backdrop-blur-md text-white shadow-xs">
                                Habis
                            </span>
                        ` : `
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-600/90 backdrop-blur-md text-white shadow-xs">
                                Tersedia
                            </span>
                        `}
                    </div>
                </div>

                <!-- Info Konser -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div class="space-y-1.5">
                        <p class="text-xs font-semibold text-slate-500">
                            ${escapeHtml(event.formatted_tanggal)} • ${escapeHtml(event.formatted_jam)}
                        </p>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-[#FF6082] transition-colors leading-snug line-clamp-1" title="${escapeHtml(event.nama_event)}">
                            ${escapeHtml(event.nama_event)}
                        </h3>
                        <p class="text-xs text-slate-600 font-medium line-clamp-1">
                            ${escapeHtml(event.pengisi_acara)}
                        </p>
                        <p class="text-xs text-slate-500 line-clamp-1 flex items-center gap-1.5 pt-1" title="${escapeHtml(event.venue)}">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">${escapeHtml(event.venue)}</span>
                        </p>
                    </div>

                    <!-- Harga & CTA -->
                    <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between mt-auto">
                        <div>
                            <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-400">Mulai dari</span>
                            <span class="text-base font-extrabold text-slate-900">
                                ${escapeHtml(event.formatted_harga)}
                            </span>
                        </div>

                        <a href="${escapeHtml(event.detail_url)}" 
                           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all ${
                               isSoldOut 
                               ? 'bg-slate-200 text-slate-400 pointer-events-none' 
                               : 'bg-[#FF6082] hover:bg-[#e54b6d] text-white shadow-xs hover:shadow-sm'
                           }">
                            ${isSoldOut ? 'Tiket Habis' : 'Pilih Tiket'}
                        </a>
                    </div>
                </div>
            </article>
        `;
    }

    /**
     * Eksekusi Fetch ke api/events.php
     */
    function fetchEvents() {
        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();

        const q = searchInput ? searchInput.value.trim() : '';
        const date = dateFilter ? dateFilter.value : '';
        
        let genre = '';
        const activePill = document.querySelector('.category-pill.active');
        if (activePill) {
            genre = activePill.getAttribute('data-genre') || '';
        }

        const params = new URLSearchParams();
        if (q) params.append('q', q);
        if (genre) params.append('genre', genre);
        if (date) params.append('tanggal', date);

        renderLoadingSkeleton();

        fetch(`api/events.php?${params.toString()}`, {
            signal: currentAbortController.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(res => {
            if (res.status === 'success' && Array.isArray(res.data)) {
                if (eventsCount) {
                    eventsCount.textContent = `Menampilkan ${res.data.length} konser musik`;
                }

                if (res.data.length === 0) {
                    eventsGrid.innerHTML = `
                        <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-slate-200">
                            <p class="font-bold text-slate-800 text-base">Tidak ada konser musik yang ditemukan</p>
                            <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian atau filter genre lain.</p>
                        </div>
                    `;
                } else {
                    eventsGrid.innerHTML = res.data.map(createEventCard).join('');
                    if (window.initCardsAnimation) {
                        window.initCardsAnimation();
                    }
                }
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Fetch error:', err);
                eventsGrid.innerHTML = `
                    <div class="col-span-full py-10 text-center text-rose-600 bg-rose-50 rounded-2xl border border-rose-200 text-xs">
                        Gagal memuat jadwal konser. Silakan muat ulang halaman.
                    </div>
                `;
            }
        });
    }

    // Event listener search input
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchEvents, 300);
        });
    }

    // Event listener tanggal
    if (dateFilter) {
        dateFilter.addEventListener('change', fetchEvents);
    }

    // Event listener kategori pill
    categoryPills.forEach(pill => {
        pill.addEventListener('click', (e) => {
            e.preventDefault();
            categoryPills.forEach(p => {
                p.classList.remove('active', 'bg-[#FF6082]', 'text-white', 'shadow-xs');
                p.classList.add('bg-white', 'text-slate-700', 'border-slate-200/80');
            });
            pill.classList.add('active', 'bg-[#FF6082]', 'text-white', 'shadow-xs');
            pill.classList.remove('bg-white', 'text-slate-700', 'border-slate-200/80');
            fetchEvents();
        });
    });

    // Event listener reset
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (dateFilter) dateFilter.value = '';
            categoryPills.forEach(p => {
                p.classList.remove('active', 'bg-[#FF6082]', 'text-white', 'shadow-xs');
                p.classList.add('bg-white', 'text-slate-700', 'border-slate-200/80');
            });
            const firstPill = categoryPills[0];
            if (firstPill) {
                firstPill.classList.add('active', 'bg-[#FF6082]', 'text-white', 'shadow-xs');
                firstPill.classList.remove('bg-white', 'text-slate-700');
            }
            fetchEvents();
        });
    }
});
