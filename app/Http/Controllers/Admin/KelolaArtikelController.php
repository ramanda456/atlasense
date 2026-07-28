<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KelolaArtikelController extends Controller
{
    public function index()
    {
        $articles = Artikel::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.artikel.indeks', compact('articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $slug = Str::slug($request->title) . '-' . uniqid();

        Artikel::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Artikel analisis baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $article = Artikel::findOrFail($id);
        
        $article->update([
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'published_at' => $request->status === 'published' && !$article->published_at ? now() : $article->published_at,
        ]);

        return redirect()->back()->with('success', 'Artikel analisis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Artikel::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Artikel analisis berhasil dihapus.');
    }
}
