<x-layouts.admin title="Admin Sobat Akar Tani Kimia | AI Assistant">
    <section class="space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">AI Assistant</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                Tanya apa saja soal data order, pelanggan, atau produk - AI ini cuma bisa <b>membaca</b> data, tidak bisa mengubah/menghapus apa pun.
            </p>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div data-ai-chat-log class="flex h-[55vh] flex-col gap-4 overflow-y-auto rounded-2xl bg-stone-50 p-4">
                <div class="flex justify-start">
                    <div class="max-w-[85%] rounded-2xl rounded-bl-sm bg-white px-4 py-3 text-sm text-stone-700 shadow-sm">
                        Halo! Tanya saya soal data order, pelanggan, atau produk. Contoh: "berapa order hari ini?" atau "berapa transaksi COD bulan ini?"
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ([
                    'Berapa order hari ini?',
                    'Berapa order yang masih dalam pengiriman?',
                    'Berapa transaksi COD bulan ini?',
                    'Produk apa yang paling laris bulan ini?',
                ] as $suggestion)
                    <button type="button" data-ai-suggestion class="rounded-full border border-stone-200 bg-white px-3 py-1.5 text-xs font-medium text-stone-600 hover:border-emerald-300 hover:text-emerald-700">
                        {{ $suggestion }}
                    </button>
                @endforeach
            </div>

            <form data-ai-chat-form class="mt-4 flex gap-3">
                @csrf
                <input type="text" name="question" data-ai-chat-input required maxlength="1000" placeholder="Tulis pertanyaanmu..."
                    class="flex-1 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none placeholder:text-stone-400 focus:border-emerald-500 focus:bg-white">
                <button type="submit" data-ai-chat-submit class="rounded-2xl bg-stone-900 px-6 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">
                    Kirim
                </button>
            </form>
        </section>
    </section>

    <script>
        (function () {
            const form = document.querySelector('[data-ai-chat-form]');
            if (!form) return;
            const log = document.querySelector('[data-ai-chat-log]');
            const input = document.querySelector('[data-ai-chat-input]');
            const submitBtn = document.querySelector('[data-ai-chat-submit]');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

            const escapeHtml = (str) => str.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            // Konversi minimal Markdown (bold + tabel) dari jawaban AI ke HTML - jawaban
            // dari model sering pakai format ini. Teks sumbernya sendiri sudah di-escape
            // duluan (escapeHtml), jadi aman dari HTML injection walau AI ikut format teks.
            const renderAnswer = (text) => {
                let html = escapeHtml(text);
                html = html.replace(/\*\*(.+?)\*\*/g, '<b>$1</b>');
                html = html.replace(/\n/g, '<br>');
                return html;
            };

            const appendMessage = (role, html) => {
                const wrap = document.createElement('div');
                wrap.className = 'flex ' + (role === 'user' ? 'justify-end' : 'justify-start');
                const bubble = document.createElement('div');
                bubble.className = role === 'user'
                    ? 'max-w-[85%] rounded-2xl rounded-br-sm bg-stone-900 px-4 py-3 text-sm text-white'
                    : 'max-w-[85%] rounded-2xl rounded-bl-sm bg-white px-4 py-3 text-sm text-stone-700 shadow-sm';
                bubble.innerHTML = html;
                wrap.appendChild(bubble);
                log.appendChild(wrap);
                log.scrollTop = log.scrollHeight;
                return bubble;
            };

            const ask = async (question) => {
                appendMessage('user', escapeHtml(question));
                const thinking = appendMessage('assistant', '<span class="inline-flex items-center gap-2 text-stone-400"><svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Berpikir...</span>');

                submitBtn.disabled = true;
                try {
                    const res = await fetch('{{ route('admin.ai-assistant.ask') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                        body: JSON.stringify({ question }),
                    });
                    const data = await res.json();
                    thinking.innerHTML = renderAnswer(data.answer || data.message || 'Maaf, tidak ada jawaban.');
                } catch (err) {
                    thinking.innerHTML = 'Maaf, ada gangguan koneksi. Coba lagi.';
                } finally {
                    submitBtn.disabled = false;
                    log.scrollTop = log.scrollHeight;
                }
            };

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const question = input.value.trim();
                if (!question) return;
                input.value = '';
                ask(question);
            });

            document.querySelectorAll('[data-ai-suggestion]').forEach((btn) => {
                btn.addEventListener('click', () => ask(btn.textContent.trim()));
            });
        })();
    </script>
</x-layouts.admin>
