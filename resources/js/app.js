/**
 * Bengkel Yami - Main JavaScript
 * Clean Minimalist UI Interactions
 */

import '../css/app.css';

document.addEventListener('DOMContentLoaded', function () {

    // ---- Sidebar Toggle (Mobile) ----
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const hamburger = document.querySelector('.hamburger');

    function openSidebar() {
        if (sidebar) {
            sidebar.classList.add('open');
        }
        if (overlay) {
            overlay.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove('open');
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            closeAllModals();
        }
    });

    // ---- Modal Open/Close ----
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function closeAllModals() {
        document.querySelectorAll('.modal-backdrop.active').forEach(function (modal) {
            modal.classList.remove('active');
        });
        document.body.style.overflow = '';
    }

    // Expose modal functions globally
    window.openModal = openModal;
    window.closeModal = closeModal;

    // Close modal on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) {
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Close modal buttons
    document.querySelectorAll('.modal-close, [data-dismiss="modal"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var modal = btn.closest('.modal-backdrop');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // ---- Flash Message Auto-Dismiss ----
    document.querySelectorAll('.alert').forEach(function (alert) {
        // Auto dismiss after 3 seconds
        var timer = setTimeout(function () {
            dismissAlert(alert);
        }, 3000);

        // Manual dismiss
        var dismissBtn = alert.querySelector('.alert-dismiss');
        if (dismissBtn) {
            dismissBtn.addEventListener('click', function () {
                clearTimeout(timer);
                dismissAlert(alert);
            });
        }
    });

    function dismissAlert(alert) {
        alert.classList.add('fade-out');
        setTimeout(function () {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }

    // ---- Delete Confirmation ----
    document.querySelectorAll('[data-delete]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            var message = btn.getAttribute('data-delete-message') || 'Apakah Anda yakin ingin menghapus data ini?';
            var formId = btn.getAttribute('data-delete-form');

            // Set modal content
            var modal = document.getElementById('deleteModal');
            if (modal) {
                var messageEl = modal.querySelector('.delete-message');
                if (messageEl) {
                    messageEl.textContent = message;
                }

                var confirmBtn = modal.querySelector('.delete-confirm');
                if (confirmBtn) {
                    confirmBtn.onclick = function () {
                        if (formId) {
                            var form = document.getElementById(formId);
                            if (form) {
                                form.submit();
                            }
                        }
                    };
                }

                openModal('deleteModal');
            }
        });
    });

    // ---- Number Formatting (Indonesian: 1.000.000) ----
    window.formatRupiah = function (number) {
        if (number === null || number === undefined || isNaN(number)) return 'Rp 0';
        var num = parseFloat(number);
        var formatted = num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'Rp ' + formatted;
    };

    window.formatNumber = function (number) {
        if (number === null || number === undefined || isNaN(number)) return '0';
        var num = parseFloat(number);
        return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    window.parseRupiah = function (str) {
        if (!str) return 0;
        var cleaned = str.toString().replace(/[^0-9]/g, '');
        return parseInt(cleaned) || 0;
    };

    // Auto-format number inputs
    document.querySelectorAll('[data-format="rupiah"]').forEach(function (input) {
        input.addEventListener('input', function () {
            var value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                this.value = parseInt(value).toLocaleString('id-ID');
            }
        });

        input.addEventListener('focus', function () {
            var value = this.value.replace(/[^0-9]/g, '');
            this.value = value;
        });

        input.addEventListener('blur', function () {
            var value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                this.value = parseInt(value).toLocaleString('id-ID');
            }
        });
    });

    // ---- Print Function ----
    window.printNota = function () {
        window.print();
    };

    // ---- Form Validation Helpers ----
    window.validateRequired = function (formId) {
        var form = document.getElementById(formId);
        if (!form) return true;

        var valid = true;
        form.querySelectorAll('[required]').forEach(function (field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;

                // Show error message
                var errorEl = field.parentElement.querySelector('.form-error');
                if (!errorEl) {
                    errorEl = document.createElement('div');
                    errorEl.className = 'form-error';
                    field.parentElement.appendChild(errorEl);
                }
                var label = field.parentElement.querySelector('.form-label');
                var fieldName = label ? label.textContent.replace('*', '').trim() : 'Field';
                errorEl.textContent = fieldName + ' wajib diisi';
            } else {
                field.classList.remove('is-invalid');
                var errorEl = field.parentElement.querySelector('.form-error');
                if (errorEl) {
                    errorEl.remove();
                }
            }
        });

        return valid;
    };

    // Clear validation on input
    document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(function (field) {
        field.addEventListener('input', function () {
            this.classList.remove('is-invalid');
            var errorEl = this.parentElement.querySelector('.form-error');
            if (errorEl) {
                errorEl.remove();
            }
        });
    });

    // ---- Pembayaran Live Calculation ----
    window.calculatePembayaran = function () {
        var totalJasa = parseFloat(document.getElementById('total_jasa')?.value) || 0;
        var totalSparepart = parseFloat(document.getElementById('total_sparepart')?.value) || 0;
        var subtotal = totalJasa + totalSparepart;

        var diskonPersen = parseFloat(document.getElementById('diskon_persen')?.value) || 0;
        var diskonNominal = parseFloat(document.getElementById('diskon_nominal')?.value) || 0;

        var diskon = diskonNominal > 0 ? diskonNominal : (subtotal * diskonPersen / 100);
        var dp = parseFloat(document.getElementById('dp')?.value) || 0;

        var totalBayar = subtotal - diskon;
        var sisaBayar = totalBayar - dp;

        // Update display
        var elSubtotal = document.getElementById('display_subtotal');
        var elDiskon = document.getElementById('display_diskon');
        var elTotal = document.getElementById('display_total');
        var elDp = document.getElementById('display_dp');
        var elSisa = document.getElementById('display_sisa');

        if (elSubtotal) elSubtotal.textContent = formatRupiah(subtotal);
        if (elDiskon) elDiskon.textContent = '- ' + formatRupiah(diskon);
        if (elTotal) elTotal.textContent = formatRupiah(totalBayar);
        if (elDp) elDp.textContent = '- ' + formatRupiah(dp);
        if (elSisa) elSisa.textContent = formatRupiah(sisaBayar < 0 ? 0 : sisaBayar);
    };

    // Attach calculation listeners if on pembayaran page
    var calcFields = ['diskon_persen', 'diskon_nominal', 'dp'];
    calcFields.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', window.calculatePembayaran);
        }
    });

    // Initial calculation if on pembayaran page
    if (document.getElementById('total_jasa')) {
        window.calculatePembayaran();
    }

});
