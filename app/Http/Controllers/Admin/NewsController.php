<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /** GET /admin/news */
    public function index()
    {
        $news = News::with('uploader:id,first_name,last_name')->latest()->paginate(15);
        return view('admin.news.index', compact('news'));
    }

    /** GET /admin/news/create */
    public function create()
    {
        return view('admin.news.create');
    }

    /** POST /admin/news */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|max:4096|mimes:jpeg,jpg,png,gif,webp',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('news', 'public')
            : null;

        News::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'image_path'  => $imagePath ?? 'images/ICCLOGO.png',
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'News article published.');
    }

    /** GET /admin/news/{news}/edit */
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    /** PUT /admin/news/{news} */
    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|max:4096|mimes:jpeg,jpg,png,gif,webp',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it was stored (not the default logo)
            if ($news->image_path && !str_starts_with($news->image_path, 'images/')) {
                Storage::disk('public')->delete($news->image_path);
            }
            $data['image_path'] = $request->file('image')->store('news', 'public');
        }

        $news->update([
            'title'       => $data['title'],
            'description' => $data['description'],
            'image_path'  => $data['image_path'] ?? $news->image_path,
        ]);

        return redirect()->route('admin.news.index')->with('success', 'News article updated.');
    }

    /** DELETE /admin/news/{news} */
    public function destroy(News $news)
    {
        if ($news->image_path && !str_starts_with($news->image_path, 'images/')) {
            Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();

        return back()->with('success', 'News article deleted.');
    }
}
