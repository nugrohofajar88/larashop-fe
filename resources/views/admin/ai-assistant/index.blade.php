<x-layouts.admin title="Admin Sobat Akar Tani Kimia | AI Assistant">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">AI Assistant</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Tanya apa saja soal data order, pelanggan, atau produk - AI ini cuma bisa <b>membaca</b> data, tidak bisa mengubah/menghapus apa pun.
                </p>
            </div>
            <button type="button" data-ai-reset class="shrink-0 rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50">
                Percakapan Baru
            </button>
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
            const resetBtn = document.querySelector('[data-ai-reset]');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const greetingHtml = log.innerHTML;

            // Asisten di BE stateless (tiap request dibangun ulang dari nol) - riwayat
            // percakapan disimpan & dikirim balik dari SINI tiap tanya, supaya AI
            // "nyambung" dgn pertanyaan sebelumnya. Dibatasi 3 pasang tanya-jawab (6
            // pesan) biar tidak makin boros token tiap giliran (limit Groq 8.000 TPM).
            const MAX_HISTORY = 6;
            let history = [];

            const escapeHtml = (str) => str.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            // Konversi Markdown (bold, tabel GFM, bullet list) dari jawaban AI ke HTML -
            // jawaban model sering pakai format ini (mis. tabel produk terlaris). SEMUA
            // teks di-escape LEBIH DULU (escapeHtml) sebelum dibungkus tag - AI tidak
            // pernah diizinkan kirim HTML mentah (data yg dijawabnya berasal dari database,
            // bukan input tepercaya, jadi tetap aman dari HTML/script injection).
            const inlineMd = (str) => str.replace(/\*\*(.+?)\*\*/g, '<b>$1</b>');

            const parseTableRow = (line) => line.trim().replace(/^\|/, '').replace(/\|$/, '').split('|').map((c) => c.trim());
            const isTableSeparator = (line) => /^\s*\|?\s*:?-{2,}:?\s*(\|\s*:?-{2,}:?\s*)*\|?\s*$/.test(line || '');

            const renderAnswer = (text) => {
                const lines = escapeHtml(text).split('\n');
                const blocks = [];
                let i = 0;

                while (i < lines.length) {
                    // Tabel GFM: baris header ber-'|', lalu baris pemisah '---|---'.
                    if (lines[i].includes('|') && isTableSeparator(lines[i + 1])) {
                        const head = parseTableRow(lines[i]);
                        let j = i + 2;
                        const rows = [];
                        while (j < lines.length && lines[j].includes('|')) {
                            rows.push(parseTableRow(lines[j]));
                            j++;
                        }
                        let table = '<div class="my-2 overflow-x-auto"><table class="w-full border-collapse text-xs"><thead><tr>';
                        head.forEach((c) => { table += '<th class="border border-stone-200 bg-stone-100 px-2 py-1.5 text-left font-semibold">' + inlineMd(c) + '</th>'; });
                        table += '</tr></thead><tbody>';
                        rows.forEach((r) => {
                            table += '<tr>';
                            r.forEach((c) => { table += '<td class="border border-stone-200 px-2 py-1.5">' + inlineMd(c) + '</td>'; });
                            table += '</tr>';
                        });
                        table += '</tbody></table></div>';
                        blocks.push(table);
                        i = j;
                        continue;
                    }

                    // Bullet list ('- item' atau '* item').
                    if (/^\s*[-*]\s+/.test(lines[i])) {
                        const items = [];
                        while (i < lines.length && /^\s*[-*]\s+/.test(lines[i])) {
                            items.push(lines[i].replace(/^\s*[-*]\s+/, ''));
                            i++;
                        }
                        blocks.push('<ul class="my-1 list-disc space-y-0.5 pl-5">' + items.map((it) => '<li>' + inlineMd(it) + '</li>').join('') + '</ul>');
                        continue;
                    }

                    blocks.push(inlineMd(lines[i]) + '<br>');
                    i++;
                }

                return blocks.join('');
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
                        body: JSON.stringify({ question, history }),
                    });
                    const data = await res.json();
                    if (data.answer) {
                        // Cuma simpan ke riwayat kalau beneran dapat jawaban - biar
                        // giliran yang gagal/error tidak ikut mengotori context.
                        history.push({ role: 'user', content: question }, { role: 'assistant', content: data.answer });
                        history = history.slice(-MAX_HISTORY);
                    }
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

            resetBtn?.addEventListener('click', () => {
                history = [];
                log.innerHTML = greetingHtml;
            });
        })();
    </script>
</x-layouts.admin>
