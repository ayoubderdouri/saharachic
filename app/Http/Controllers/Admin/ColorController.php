<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ColorController extends Controller
{
    public function index()
    {
        return view('admin.colors.index', ['colors' => Color::orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.colors.form', ['color' => new Color]);
    }

    public function store(Request $request)
    {
        Color::create($this->validated($request));

        return redirect()->route('admin.colors.index')->with('status', 'Couleur créée.');
    }

    public function edit(Color $color)
    {
        return view('admin.colors.form', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $color->update($this->validated($request, $color));

        return redirect()->route('admin.colors.index')->with('status', 'Couleur mise à jour.');
    }

    public function destroy(Color $color)
    {
        $color->delete();

        return back()->with('status', 'Couleur supprimée.');
    }

    private function validated(Request $request, ?Color $color = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('colors')->ignore($color)],
            'hex_code' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
    }
}
