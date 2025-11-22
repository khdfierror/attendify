<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        @if (!empty($students))
            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    Daftar Siswa ({{ count($students) }} siswa)
                </x-slot>

                <x-slot name="headerEnd">
                    <div class="flex gap-2">
                        <x-filament::button size="sm" color="success" wire:click="markAll('present')" type="button">
                            Semua Hadir
                        </x-filament::button>

                        <x-filament::button size="sm" color="danger" wire:click="markAll('absent')" type="button">
                            Semua Alpa
                        </x-filament::button>
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    No
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    NIS
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Nama Lengkap
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Catatan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($students as $index => $student)
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ $student['exists'] ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $student['nis'] }}
                                        @if ($student['exists'])
                                            <span class="ml-2 text-xs text-yellow-600 dark:text-yellow-400">(Sudah
                                                diabsen)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $student['full_name'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <select name="student_status[{{ $student['id'] }}]"
                                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                            wire:model="students.{{ $index }}.status">
                                            <option value="present">✅ Hadir</option>
                                            <option value="late">⏰ Terlambat</option>
                                            <option value="sick">🤒 Sakit</option>
                                            <option value="permission">📝 Izin</option>
                                            <option value="absent">❌ Alpa</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <input type="text" wire:model="students.{{ $index }}.notes"
                                            name="student_notes[{{ $student['id'] }}]"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500">

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit" size="lg">
                        💾 Simpan Absensi
                    </x-filament::button>
                </div>
            </x-filament::section>
        @else
            <x-filament::section class="mt-6">
                <div class="text-center py-12">
                    <div class="text-gray-400 text-5xl mb-4">📋</div>
                    <p class="text-gray-500 dark:text-gray-400">
                        Pilih kelas dan tanggal untuk memulai absensi massal
                    </p>
                </div>
            </x-filament::section>
        @endif
    </form>
</x-filament-panels::page>
