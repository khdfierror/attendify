<?php

// app/Filament/Resources/AttendanceResource/Pages/BulkAttendance.php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class BulkAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.bulk-attendance';

    protected static ?string $title = 'Absensi Massal';

    protected static ?string $navigationLabel = 'Absensi Massal';

    public ?array $data = [];

    public $students = [];

    public $selectedClass = null;

    public $selectedDate = null;

    public $selectedSubject = null;

    public function mount(): void
    {
        $this->form->fill([
            'date' => now()->format('Y-m-d'),
            'academic_year_id' => AcademicYear::where('is_active', true)->first()?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pilih Kelas dan Tanggal')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYear::where('is_active', true)->first()?->id)
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->options(Classroom::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedClass = $state;
                                $this->loadStudents();
                            })
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedDate = $state;
                                $this->loadStudents();
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('subject_id')
                            ->label('Mata Pelajaran (Opsional)')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('check_in_time')
                            ->label('Jam Masuk Default')
                            ->seconds(false)
                            ->default(now()->format('H:i'))
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ])
            ->statePath('data')
            ->model(Attendance::class);
    }

    public function loadStudents()
    {
        if (! $this->selectedClass || ! $this->selectedDate) {
            $this->students = [];

            return;
        }

        $students = Student::where('class_id', $this->selectedClass)
            ->where('status', 'active')
            ->orderBy('nis')
            ->get();

        // Check existing attendance
        $existingAttendances = Attendance::where('class_id', $this->selectedClass)
            ->whereDate('date', $this->selectedDate)
            ->pluck('status', 'student_id')
            ->toArray();

        $this->students = $students->map(function ($student) use ($existingAttendances) {
            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'full_name' => $student->full_name,
                'status' => $existingAttendances[$student->id] ?? 'present',
                'exists' => isset($existingAttendances[$student->id]),
            ];
        })->toArray();
    }

    public function save()
    {
        $data = $this->form->getState();

        if (empty($this->students)) {
            Notification::make()
                ->warning()
                ->title('Tidak Ada Data')
                ->body('Silakan pilih kelas dan tanggal terlebih dahulu.')
                ->send();

            return;
        }

        try {
            DB::beginTransaction();

            $academicYearId = $data['academic_year_id'];
            $classId = $data['class_id'];
            $date = $data['date'];
            $subjectId = $data['subject_id'] ?? null;
            $checkInTime = $data['check_in_time'] ?? now()->format('H:i');

            $created = 0;
            $updated = 0;

            foreach ($this->students as $student) {

                // Ambil status dari Livewire
                $status = $student['status'] ?? 'present';

                // Notes juga harus di-wire:model supaya ikut tersimpan
                $notes = $student['notes'] ?? null;

                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $student['id'],
                        'class_id' => $classId,
                        'date' => $date,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'academic_year_id' => $academicYearId,
                        'status' => $status,
                        'check_in_time' => $checkInTime,
                        'notes' => $notes,
                        'updated_by' => auth()->id(),
                        'created_by' => $student['exists'] ? null : auth()->id(),
                    ]
                );

                // Hitung apakah ini create atau update
                $attendance->wasRecentlyCreated ? $created++ : $updated++;
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title('Absensi Berhasil Disimpan')
                ->body("$created data baru ditambahkan, $updated data diperbarui.")
                ->send();

            $this->loadStudents();

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title('Gagal Menyimpan Absensi')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function markAll($status)
    {
        foreach ($this->students as $key => $student) {
            $this->students[$key]['status'] = $status;
        }

        Notification::make()
            ->info()
            ->title('Status Diubah')
            ->body('Semua siswa ditandai sebagai: '.$this->getStatusLabel($status))
            ->send();
    }

    protected function getStatusLabel($status)
    {
        return match ($status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'absent' => 'Alpa',
            default => $status,
        };
    }
}
