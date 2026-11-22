/**
 * EVENTIX - Ticket Booking & Live Quota Interaction Script
 * Menangani stepper jumlah tiket, kalkulasi subtotal/grand total,
 * serta polling live quota ke api/check_quota.php
 */

document.addEventListener('DOMContentLoaded', () => {
    const bookingForm = document.getElementById('booking-form');
    if (!bookingForm) return;

    const eventId = bookingForm.getAttribute('data-event-id');
    const totalTicketsElem = document.getElementById('summary-total-tickets');
    const totalPriceElem = document.getElementById('summary-total-price');
    const btnSubmitOrder = document.getElementById('btn-submit-order');
    const orderItemsSummary = document.getElementById('order-items-summary');

    // Format angka ke format mata uang Rupiah
    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    // Hitung total tiket dan grand total harga dari seluruh kategori
    function calculateTotals() {
        let totalCount = 0;
        let grandTotal = 0;
        let selectedListHtml = '';

        const ticketCards = document.querySelectorAll('.ticket-card');

        ticketCards.forEach(card => {
            const ticketId = card.getAttribute('data-ticket-id');
            const price = parseFloat(card.getAttribute('data-price')) || 0;
            const categoryName = card.getAttribute('data-category') || 'Tiket';
            const inputQty = card.querySelector('.ticket-qty-input');
            const subtotalElem = card.querySelector('.ticket-subtotal-val');
            
            let qty = parseInt(inputQty.value, 10) || 0;
            if (qty < 0) qty = 0;

            const subtotal = qty * price;
            totalCount += qty;
            grandTotal += subtotal;

            // Update subtotal per card jika elemen tersedia
            if (subtotalElem) {
                subtotalElem.textContent = formatRupiah(subtotal);
            }

            // Tambahkan rincian ke ringkasan pesanan jika kuantiti > 0
            if (qty > 0) {
                selectedListHtml += `
                    <div class="flex items-center justify-between text-xs py-1.5 border-b border-neutral-100 last:border-0">
                        <div class="truncate pr-2">
                            <span class="font-bold text-neutral-800">${categoryName}</span>
                            <span class="text-neutral-500 block text-[11px]">${qty} x ${formatRupiah(price)}</span>
                        </div>
                        <span class="font-bold text-teal-800 shrink-0">${formatRupiah(subtotal)}</span>
                    </div>
                `;
            }
        });

        // Update ringkasan global
        if (totalTicketsElem) {
            totalTicketsElem.textContent = totalCount + ' Tiket';
        }

        if (totalPriceElem) {
            totalPriceElem.textContent = formatRupiah(grandTotal);
        }

        if (orderItemsSummary) {
            orderItemsSummary.innerHTML = selectedListHtml || '<p class="text-xs text-neutral-400 italic text-center py-2">Belum ada tiket yang dipilih</p>';
        }

        // Aktifkan / Nonaktifkan tombol checkout
        if (btnSubmitOrder) {
            if (totalCount > 0) {
                btnSubmitOrder.removeAttribute('disabled');
                btnSubmitOrder.classList.remove('opacity-50', 'cursor-not-allowed');
                btnSubmitOrder.classList.add('bg-[#FF6082]', 'hover:bg-[#e54b6d]', 'text-white');
            } else {
                btnSubmitOrder.setAttribute('disabled', 'disabled');
                btnSubmitOrder.classList.add('opacity-50', 'cursor-not-allowed');
                btnSubmitOrder.classList.remove('hover:bg-[#e54b6d]');
            }
        }
    }

    // Event listener untuk tombol stepper minus dan plus
    document.querySelectorAll('.btn-stepper').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const action = button.getAttribute('data-action');
            const targetId = button.getAttribute('data-target');
            const card = button.closest('.ticket-card');
            const inputQty = document.getElementById(targetId);
            if (!inputQty || !card) return;

            const maxQuota = parseInt(card.getAttribute('data-quota'), 10) || 0;
            let currentVal = parseInt(inputQty.value, 10) || 0;

            if (action === 'plus') {
                if (currentVal < maxQuota) {
                    inputQty.value = currentVal + 1;
                } else {
                    showToast(`Maksimal pemesanan kategori ini adalah sisa kuota yang tersedia (${maxQuota} tiket).`);
                }
            } else if (action === 'minus') {
                if (currentVal > 0) {
                    inputQty.value = currentVal - 1;
                }
            }

            calculateTotals();
        });
    });

    // Event listener jika user mengetik manual pada input angka
    document.querySelectorAll('.ticket-qty-input').forEach(input => {
        input.addEventListener('change', () => {
            const card = input.closest('.ticket-card');
            const maxQuota = parseInt(card.getAttribute('data-quota'), 10) || 0;
            let currentVal = parseInt(input.value, 10) || 0;

            if (isNaN(currentVal) || currentVal < 0) {
                input.value = 0;
            } else if (currentVal > maxQuota) {
                input.value = maxQuota;
                showToast(`Jumlah disesuaikan dengan sisa kuota (${maxQuota} tiket).`);
            } else {
                input.value = currentVal;
            }

            calculateTotals();
        });
    });

    // Validasi form saat submit
    bookingForm.addEventListener('submit', (e) => {
        let totalCount = 0;
        document.querySelectorAll('.ticket-qty-input').forEach(input => {
            totalCount += parseInt(input.value, 10) || 0;
        });

        if (totalCount <= 0) {
            e.preventDefault();
            showToast('Silakan pilih minimal 1 tiket konser sebelum melanjutkan.');
        }
    });

    // Fungsi Fetch Live Quota dari Server
    async function fetchLiveQuota() {
        if (!eventId) return;

        try {
            const response = await fetch(`api/check_quota.php?id_event=${encodeURIComponent(eventId)}`);
            if (!response.ok) return;

            const data = await response.json();
            if (data.status === 'success' && Array.isArray(data.tickets)) {
                data.tickets.forEach(ticket => {
                    const card = document.querySelector(`.ticket-card[data-ticket-id="${ticket.id_tiket}"]`);
                    if (!card) return;

                    const newQuota = parseInt(ticket.kuota_tersedia, 10);
                    card.setAttribute('data-quota', newQuota);

                    // Update Badge Kuota
                    const badgeElem = card.querySelector('.quota-badge');
                    const inputQty = card.querySelector('.ticket-qty-input');
                    const btnPlus = card.querySelector('.btn-stepper[data-action="plus"]');

                    if (newQuota <= 0) {
                        card.classList.add('opacity-75', 'bg-neutral-50');
                        if (badgeElem) {
                            badgeElem.className = 'quota-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700';
                            badgeElem.textContent = 'Habis Terjual (Sold Out)';
                        }
                        if (inputQty) {
                            inputQty.value = 0;
                            inputQty.setAttribute('disabled', 'disabled');
                        }
                        if (btnPlus) {
                            btnPlus.setAttribute('disabled', 'disabled');
                        }
                    } else {
                        card.classList.remove('opacity-75', 'bg-neutral-50');
                        if (badgeElem) {
                            if (newQuota <= 50) {
                                badgeElem.className = 'quota-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200';
                                badgeElem.textContent = `Sisa Terbatas: ${newQuota} tiket`;
                            } else {
                                badgeElem.className = 'quota-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200';
                                badgeElem.textContent = `Tersedia: ${newQuota} tiket`;
                            }
                        }
                        if (inputQty && inputQty.hasAttribute('disabled')) {
                            inputQty.removeAttribute('disabled');
                        }
                        if (btnPlus && btnPlus.hasAttribute('disabled')) {
                            btnPlus.removeAttribute('disabled');
                        }

                        // Jika input user melebihi kuota terbaru, sesuaikan ke bawah
                        if (inputQty && parseInt(inputQty.value, 10) > newQuota) {
                            inputQty.value = newQuota;
                            showToast(`Kuota kategori '${ticket.kategori}' baru saja berkurang. Jumlah otomatis disesuaikan.`);
                        }
                    }
                });

                calculateTotals();
            }
        } catch (error) {
            console.warn('[Quota Polling] Gagal memuat kuota terbaru:', error);
        }
    }

    // Notifikasi Toast Ringan
    function showToast(message) {
        let toast = document.getElementById('booking-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'booking-toast';
            toast.className = 'fixed bottom-6 right-6 z-50 bg-[#0F172A] text-white text-xs font-semibold py-3 px-4 rounded-xl shadow-2xl transition-all duration-300 max-w-sm flex items-center gap-2 border border-[#FF6082]/40';
            document.body.appendChild(toast);
        }
        toast.innerHTML = `
            <svg class="w-4 h-4 text-[#FF6082] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>${message}</span>
        `;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
        }, 3500);
    }

    // ========================================================
    // INTERACTIVE SEATING MAP & TICKET CARDS SYNCHRONIZATION
    // ========================================================
    const seatingCanvas = document.getElementById('seating-canvas-container');
    const seatingTooltip = document.getElementById('seating-tooltip');
    const tooltipCatName = document.getElementById('tooltip-cat-name');
    const tooltipCatPrice = document.getElementById('tooltip-cat-price');
    const tooltipCatStatus = document.getElementById('tooltip-cat-status');
    const zoneGroups = document.querySelectorAll('.seating-zone-group');
    const legendItems = document.querySelectorAll('.seating-legend-item');

    function highlightTicketCard(ticketId, shouldHighlight) {
        const card = document.querySelector(`.ticket-card[data-ticket-id="${ticketId}"]`);
        if (!card) return;
        if (shouldHighlight) {
            card.classList.add('ring-2', 'ring-[#FF6082]', 'border-[#FF6082]', 'bg-[#fff5f7]');
        } else {
            card.classList.remove('ring-2', 'ring-[#FF6082]', 'border-[#FF6082]', 'bg-[#fff5f7]');
        }
    }

    function highlightZone(ticketId, shouldHighlight) {
        const groups = document.querySelectorAll(`.seating-zone-group[data-ticket-id="${ticketId}"]`);
        groups.forEach(group => {
            const shapes = group.querySelectorAll('.zone-shape');
            shapes.forEach(shape => {
                if (shouldHighlight) {
                    shape.setAttribute('stroke-width', '3');
                    shape.style.filter = 'drop-shadow(0 0 10px rgba(255, 96, 130, 0.65))';
                } else {
                    shape.setAttribute('stroke-width', '1.5');
                    shape.style.filter = 'none';
                }
            });
        });
    }

    zoneGroups.forEach(group => {
        const ticketId = group.getAttribute('data-ticket-id');
        const name = group.getAttribute('data-name');
        const price = group.getAttribute('data-price');
        const quota = parseInt(group.getAttribute('data-quota'), 10) || 0;

        group.addEventListener('mouseenter', () => {
            highlightZone(ticketId, true);
            highlightTicketCard(ticketId, true);

            if (seatingTooltip && seatingCanvas) {
                if (tooltipCatName) tooltipCatName.textContent = name;
                if (tooltipCatPrice) tooltipCatPrice.textContent = price;
                if (tooltipCatStatus) {
                    if (quota <= 0) {
                        tooltipCatStatus.textContent = 'Habis';
                        tooltipCatStatus.className = 'px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-500/20 text-rose-400';
                    } else {
                        tooltipCatStatus.textContent = `Tersedia (${quota})`;
                        tooltipCatStatus.className = 'px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-400';
                    }
                }
                seatingTooltip.style.opacity = '1';
            }
        });

        group.addEventListener('mousemove', (e) => {
            if (seatingTooltip && seatingCanvas) {
                const rect = seatingCanvas.getBoundingClientRect();
                const x = e.clientX - rect.left + 15;
                const y = e.clientY - rect.top + 15;
                seatingTooltip.style.left = `${Math.min(x, rect.width - 180)}px`;
                seatingTooltip.style.top = `${Math.min(y, rect.height - 80)}px`;
            }
        });

        group.addEventListener('mouseleave', () => {
            highlightZone(ticketId, false);
            highlightTicketCard(ticketId, false);
            if (seatingTooltip) {
                seatingTooltip.style.opacity = '0';
            }
        });

        // Click zone to select ticket
        group.addEventListener('click', () => {
            const card = document.querySelector(`.ticket-card[data-ticket-id="${ticketId}"]`);
            if (!card) return;

            if (quota <= 0) {
                showToast(`Tiket kategori ${name} saat ini sudah habis terjual.`);
                return;
            }

            const inputQty = card.querySelector('.ticket-qty-input');
            if (inputQty) {
                let current = parseInt(inputQty.value, 10) || 0;
                if (current < 4 && current < quota) {
                    inputQty.value = current + 1;
                    calculateTotals();
                    showToast(`Kategori ${name} ditambahkan (+1).`);
                }
            }

            // Scroll to booking form or highlight card
            if (window.lenis) {
                window.lenis.scrollTo(card, { offset: -100, duration: 1.0 });
            } else {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });

    // Legend hover/click
    legendItems.forEach(item => {
        const ticketId = item.getAttribute('data-ticket-id');
        item.addEventListener('mouseenter', () => {
            highlightZone(ticketId, true);
            highlightTicketCard(ticketId, true);
        });
        item.addEventListener('mouseleave', () => {
            highlightZone(ticketId, false);
            highlightTicketCard(ticketId, false);
        });
        item.addEventListener('click', () => {
            const card = document.querySelector(`.ticket-card[data-ticket-id="${ticketId}"]`);
            if (card) {
                if (window.lenis) {
                    window.lenis.scrollTo(card, { offset: -100, duration: 1.0 });
                } else {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });

    // Hover from ticket cards back to SVG zone
    document.querySelectorAll('.ticket-card').forEach(card => {
        const ticketId = card.getAttribute('data-ticket-id');
        card.addEventListener('mouseenter', () => {
            highlightZone(ticketId, true);
        });
        card.addEventListener('mouseleave', () => {
            highlightZone(ticketId, false);
        });
    });

    // ========================================================
    // MODAL LIGHTBOX ZOOM DENAH PANGGUNG
    // ========================================================
    window.openSeatingModal = function() {
        const modal = document.getElementById('seating-modal');
        const target = document.getElementById('modal-svg-target');
        const originalSvg = document.getElementById('seating-svg');
        if (!modal || !target || !originalSvg) return;

        target.innerHTML = '';
        const clonedSvg = originalSvg.cloneNode(true);
        clonedSvg.id = 'modal-seating-svg';
        clonedSvg.classList.add('w-full', 'h-auto', 'max-h-[75vh]');
        target.appendChild(clonedSvg);

        modal.classList.remove('hidden');
    };

    window.closeSeatingModal = function() {
        const modal = document.getElementById('seating-modal');
        if (modal) modal.classList.add('hidden');
    };

    // Close on Escape or click outside
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.closeSeatingModal();
        }
    });

    // ========================================================
    // STICKY MOBILE ACTION BAR & SMOOTH SCROLL
    // ========================================================
    window.scrollToBooking = function() {
        const bookingCard = document.getElementById('booking-form');
        if (bookingCard) {
            if (window.lenis) {
                window.lenis.scrollTo(bookingCard, { offset: -20, duration: 1.0 });
            } else {
                bookingCard.scrollIntoView({ behavior: 'smooth' });
            }
        }
    };

    const mobileBottomBar = document.getElementById('mobile-bottom-bar');
    const submitBtn = document.getElementById('btn-submit-order');
    if (mobileBottomBar && submitBtn && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    mobileBottomBar.classList.add('translate-y-full', 'opacity-0');
                    mobileBottomBar.classList.remove('translate-y-0', 'opacity-100');
                } else {
                    mobileBottomBar.classList.remove('translate-y-full', 'opacity-0');
                    mobileBottomBar.classList.add('translate-y-0', 'opacity-100');
                }
            });
        }, { threshold: 0.1 });
        observer.observe(submitBtn);
    }

    // ========================================================
    // SHARE EVENT BUTTON (WEB SHARE API)
    // ========================================================
    const btnShare = document.getElementById('btn-share-event');
    if (btnShare) {
        btnShare.addEventListener('click', async () => {
            const eventTitle = document.title;
            const eventUrl = window.location.href;
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: eventTitle,
                        text: `Beli tiket konser resmi ${eventTitle} di EVENTIX:`,
                        url: eventUrl
                    });
                } catch (err) {}
            } else {
                navigator.clipboard.writeText(eventUrl).then(() => {
                    const textSpan = document.getElementById('share-btn-text');
                    if (textSpan) {
                        const original = textSpan.textContent;
                        textSpan.textContent = 'Tautan Disalin!';
                        setTimeout(() => { textSpan.textContent = original; }, 2000);
                    }
                    showToast('Tautan jadwal konser berhasil disalin ke clipboard!');
                });
            }
        });
    }

    // Inisialisasi awal
    calculateTotals();
    fetchLiveQuota();

    // Polling kuota real-time setiap 20 detik
    setInterval(fetchLiveQuota, 20000);
});
