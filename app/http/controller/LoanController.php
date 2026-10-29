<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\LoanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    public function index()
    {
        return view('loans.index', ['loans' => LoanRequest::with('equipment')->latest()->paginate(10)]);
    }

    public function create(Request $request)
    {
        return view('loans.create', [
            'equipments' => Equipment::where('status', 'tersedia')->orderBy('name')->get(),
            'selectedEquipment' => $request->integer('equipment_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_id' => ['required', 'exists:equipments,id'],
            'borrower_name' => ['required', 'string', 'min:3', 'max:100'],
            'borrower_nim' => ['required', 'regex:/^[A-Za-z0-9]{8}$/'],
            'borrower_email' => ['required', 'email', 'max:100'],
            'loan_date' => ['required', 'date', 'after_or_equal:today'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'purpose' => ['required', 'string', 'min:5'],
        ], [
            'borrower_nim.regex' => 'NIM harus terdiri dari tepat 8 karakter angka atau huruf.',
            'loan_date.after_or_equal' => 'Tanggal peminjaman tidak boleh sebelum hari ini.',
        ]);

        $days = now()->parse($data['loan_date'])->diffInDays(now()->parse($data['expected_return_date'])) + 1;
        if ($days > 7) {
            throw ValidationException::withMessages([
                'expected_return_date' => 'Durasi peminjaman maksimal adalah 7 hari kalender.',
            ]);
        }

        DB::transaction(function () use ($data, $days) {
            $equipment = Equipment::whereKey($data['equipment_id'])->lockForUpdate()->firstOrFail();
            if ($equipment->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'equipment_id' => "Perangkat {$equipment->name} saat ini tidak tersedia.",
                ]);
            }

            LoanRequest::create([
                ...$data,
                'total_estimated_fee' => $days * $equipment->daily_rental_fee,
            ]);
            $equipment->update(['status' => 'dipinjam']);
        });

        return redirect()->route('equipments.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }
}
