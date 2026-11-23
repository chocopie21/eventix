/**
 * EVENTIX - Catalog Real-time Live Search
 * Modern Minimalist Card Rendering & Motion Integration
 */

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const searchBtn   = document.getElementById('search-btn');
    const searchForm  = document.getElementById('hero-search-form');
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const eventsGrid  = document.getElementById('events-grid');
    const eventsCount = document.getElementById('events-count');

    if (!eventsGrid) return;

    let debounceTimer = null;
    let currentAbortController = null;
    const apiEndpoint = window.EVENTIX_API_EVENTS || 'api/events.php';

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
                <div class="concert-card modern-card animate-pulse bg-white border border-slate-200/80 rounded-2xl overflow-hidden">
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
            <article class="concert-card modern-card flex flex-col justify-between overflow-hidden group">
                <!-- Poster Container -->
                <div class="relative aspect-[16/10] overflow-hidden bg-slate-900 rounded-t-[19px]">
                    <a href="${escapeHtml(event.detail_url)}" class="block w-full h-full">
                        <img src="${escapeHtml(posterSrc)}" alt="${escapeHtml(event.nama_event)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                    </a>
                    
                    <!-- Genre Badge -->
                    <div class="absolute top-3 left-3 z-10 pointer-events-none">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#0F172A]/85 backdrop-blur-md text-white border border-white/10 shadow-xs">
                            ${escapeHtml(event.genre_musik || 'Konser')}
                        </span>
                    </div>

                    <!-- Kuota Badge -->
                    <div class="absolute top-3 right-3 z-10 pointer-events-none">
                        ${isSoldOut ? `
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-600/90 backdrop-blur-md text-white border border-white/10 shadow-xs">
                                Habis
                            </span>
                        ` : `
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-600/90 backdrop-blur-md text-white border border-white/10 shadow-xs">
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
                        <h3 class="text-base font-bold text-slate-900 leading-snug line-clamp-1" title="${escapeHtml(event.nama_event)}">
                            <a href="${escapeHtml(event.detail_url)}" class="hover:text-[#FF6082] transition-colors">
                                ${escapeHtml(event.nama_event)}
                            </a>
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
                           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-button cursor-pointer ${
                               isSoldOut 
                               ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 shadow-2xs hover:shadow-xs' 
                               : 'bg-[#FF6082] hover:bg-[#E0486D] text-white shadow-xs hover:shadow'
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
        const params = new URLSearchParams();
        if (q) params.append('q', q);

        renderLoadingSkeleton();

        fetch(`${apiEndpoint}?${params.toString()}`, {
            signal: currentAbortController.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(res => {
            if (res.status === 'success' && Array.isArray(res.data)) {
                if (eventsCount) {
                    eventsCount.textContent = `Menampilkan ${res.data.length} jadwal konser musik`;
                }

                if (res.data.length === 0) {
                    eventsGrid.innerHTML = `
                        <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-slate-200">
                            <p class="font-bold text-slate-900 text-base">Tidak ada konser musik yang ditemukan</p>
                            <p class="text-xs text-slate-500 mt-1">
                                Coba kata kunci lain atau kosongkan kolom pencarian.
                            </p>
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

    /**
     * Jalankan pencarian dan scroll halus ke katalog
     */
    function executeSearchAndScroll() {
        clearTimeout(debounceTimer);
        fetchEvents();
        const katalogSection = document.getElementById('katalog');
        if (katalogSection) {
            if (window.lenis) {
                window.lenis.scrollTo(katalogSection, { offset: -30, duration: 1.2 });
            } else {
                katalogSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }

    // Event listener search input (Live Search saat mengetik)
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const hasText = searchInput.value.trim().length > 0;
            if (clearSearchBtn) {
                clearSearchBtn.classList.toggle('hidden', !hasText);
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchEvents, 250);
        });

        // Tekan tombol Enter pada keyboard
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                executeSearchAndScroll();
            }
        });
    }

    // Event listener tombol Cari Tiket & Form submit
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            executeSearchAndScroll();
        });
    } else if (searchBtn) {
        searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            executeSearchAndScroll();
        });
    }

    // Tombol Clear Search (X)
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            clearSearchBtn.classList.add('hidden');
            fetchEvents();
        });
    }
});
