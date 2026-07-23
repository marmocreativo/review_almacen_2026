<div x-data="pinConfirm()" x-cloak x-show="open" class="modal" :class="{ 'modal-open': open }">
    <div class="modal-box max-w-sm">
        <h3 class="font-bold text-lg mb-1">Confirma con tu PIN</h3>
        <p class="text-sm text-base-content/60 mb-4">Esta acción es irreversible. Ingresa tu PIN para continuar.</p>
        <input type="password" inputmode="numeric" pattern="[0-9]*" maxlength="8"
            x-model="pin" @keyup.enter="confirmar()"
            class="input input-bordered w-full mb-2" placeholder="PIN" x-ref="pinInput" />
        <p class="text-error text-sm mb-2" x-show="error" x-text="error"></p>
        <div class="modal-action">
            <button type="button" class="btn btn-ghost btn-sm" @click="cancelar()">Cancelar</button>
            <button type="button" class="btn btn-error btn-sm" @click="confirmar()" :disabled="loading">
                <span x-show="!loading">Confirmar</span>
                <span x-show="loading">Verificando...</span>
            </button>
        </div>
    </div>
</div>

<script>
function pinConfirm() {
    return {
        open: false,
        pin: '',
        error: '',
        loading: false,
        targetForm: null,
        callback: null,
        init() {
            window.addEventListener('pin-confirm', (e) => {
                this.targetForm = e.detail.form || null;
                this.callback = e.detail.callback || null;
                this.pin = '';
                this.error = '';
                this.open = true;
                this.$nextTick(() => this.$refs.pinInput.focus());
            });
        },
        cancelar() {
            this.open = false;
            this.targetForm = null;
            this.callback = null;
        },
        async confirmar() {
            if (!this.pin) { this.error = 'Ingresa tu PIN.'; return; }
            this.loading = true;
            this.error = '';
            try {
                const res = await fetch("{{ route('pin.verificar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ pin: this.pin }),
                });
                const data = await res.json();
                if (data.success) {
                    this.open = false;
                    if (this.targetForm) this.targetForm.submit();
                    if (this.callback) this.callback();
                } else {
                    this.error = data.message || 'PIN incorrecto.';
                }
            } catch (e) {
                this.error = 'Error al verificar el PIN.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>