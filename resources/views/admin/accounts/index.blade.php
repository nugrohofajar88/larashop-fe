<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Account">
    @php $isSuper = session('admin.user.is_super_admin') ?? (session('admin.user.admin_role') === 'super_admin'); @endphp
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Account</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Kelola akun internal admin</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Modul ini dipakai untuk melihat siapa saja yang memiliki akses ke panel admin dan peran operasional yang mereka pegang.
                </p>
            </div>

            @if ($isSuper)
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.accounts.create') }}" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                        Tambah account
                    </a>
                </div>
            @endif
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Daftar account admin</h2>
                    <p class="mt-1 text-sm text-stone-500">Daftar akun internal yang memiliki akses ke panel admin.</p>
                </div>

                <form method="GET" action="{{ route('admin.accounts.index') }}" class="flex flex-wrap gap-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama, email, atau ID account..."
                        class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none placeholder:text-stone-400 focus:border-emerald-500 focus:bg-white lg:w-80"
                    >
                    <select name="role" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                        <option value="all" {{ $activeRole === 'all' ? 'selected' : '' }}>Semua role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" {{ $activeRole === $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                        <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ $activeStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Terapkan</button>
                </form>
            </div>

            <div class="mt-4 flex items-center justify-between gap-4 text-sm text-stone-500">
                <p>Menampilkan {{ count($accounts) }} account{{ $search !== '' || $activeRole !== 'all' || $activeStatus !== 'all' ? ' sesuai filter' : '' }}.</p>
                @if ($search !== '' || $activeRole !== 'all' || $activeStatus !== 'all')
                    <a href="{{ route('admin.accounts.index') }}" class="font-semibold text-emerald-700">Reset filter</a>
                @endif
            </div>

            {{-- Desktop: tabel --}}
            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Account</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Last login</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($accounts as $account)
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <p class="font-semibold text-stone-900">{{ $account['name'] }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $account['id'] }}</p>
                                    <p class="mt-2 text-sm text-stone-600">{{ $account['email'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top text-stone-700">{{ $account['role'] }}</td>
                                <td class="px-4 py-4 align-top">
                                    <span class="rounded-full {{ $account['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-700' }} px-3 py-1 text-xs font-semibold">
                                        {{ $account['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top text-stone-600">{{ $account['last_login'] }}</td>
                                <td class="px-4 py-4 align-top">
                                    @if ($isSuper)
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.accounts.edit', $account['id']) }}" class="rounded-full bg-stone-900 px-3 py-2 text-xs font-medium text-white">Edit</a>
                                            <form method="POST" action="{{ route('admin.accounts.destroy', $account['id']) }}" onsubmit="return confirm('Hapus account {{ $account['name'] }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-stone-400">Hanya super admin</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada account admin yang cocok dengan filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu --}}
            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($accounts as $account)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-stone-900">{{ $account['name'] }}</p>
                                <p class="mt-0.5 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $account['id'] }}</p>
                                <p class="mt-1 text-sm text-stone-600">{{ $account['email'] }}</p>
                            </div>
                            <span class="shrink-0 rounded-full {{ $account['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-700' }} px-2.5 py-1 text-xs font-semibold">
                                {{ $account['status'] }}
                            </span>
                        </div>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Role</dt>
                                <dd class="text-stone-800">{{ $account['role'] }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Last login</dt>
                                <dd class="text-stone-700">{{ $account['last_login'] }}</dd>
                            </div>
                        </dl>
                        <div class="mt-3 flex gap-2 @if (! $isSuper) hidden @endif">
                            <a href="{{ route('admin.accounts.edit', $account['id']) }}" class="flex-1 rounded-full bg-stone-900 px-3 py-2 text-center text-xs font-medium text-white">Edit</a>
                            <form method="POST" action="{{ route('admin.accounts.destroy', $account['id']) }}" onsubmit="return confirm('Hapus account {{ $account['name'] }}?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada account admin yang cocok dengan filter saat ini.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
