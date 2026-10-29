<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('category')->latest();
        if ($request->filled('search')) {
            $keyword = $request->string('search');
            $query->where(function ($builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('asset_code', 'like', "%{$keyword}%")
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$keyword}%"));
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        return view('equipments.index', [
            'equipments' => $query->paginate(6)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('equipments.create', ['categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_code' => ['required', 'string', 'max:20', 'unique:equipments,asset_code'],
            'name' => ['required', 'string', 'min:5', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'specification' => ['required', 'string'],
            'condition' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'daily_rental_fee' => ['required', 'numeric', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $data['image_path'] = $request->file('image')->store('equipments', 'public');
        unset($data['image']);
        Equipment::create($data);

        return redirect()->route('equipments.index')
            ->with('success', "Perangkat {$data['name']} ({$data['asset_code']}) berhasil didaftarkan.");
    }

    public function show(Equipment $equipment)
    {
        return view('equipments.show', compact('equipment'));
    }
}
