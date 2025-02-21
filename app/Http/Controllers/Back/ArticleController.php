<?php

namespace App\Http\Controllers\Back;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ArticleController extends Controller
{
    /**
     * Tampilkan list artikel. 
     * Jika request Ajax (DataTables), kembalikan JSON.
     */
    public function index(Request $request)
    {
        // Jika request dari DataTables (AJAX)
        if ($request->ajax()) {
            // Gunakan query builder tanpa ->get() agar serverSide processing berfungsi
            $articles = Article::with('category')->latest();

            return DataTables::of($articles)
                ->addIndexColumn()  // Menambahkan kolom DT_RowIndex
                ->addColumn('category', function($article) {
                    // Tampilkan nama kategori (cek null terlebih dulu)
                    return $article->category ? $article->category->name : '-';
                })
                ->addColumn('status', function($article) {
                    // Tampilkan badge status
                    return $article->status == 0
                        ? '<span class="badge bg-danger">Private</span>'
                        : '<span class="badge bg-success">Published</span>';
                })
                ->addColumn('button', function($article) {
                    // Tombol aksi (Detail, Edit, Delete)
                    // Sesuaikan route jika perlu
                    return '
                        <div class="text-center">
                            <a href="'.route('article.show', $article->id).'" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="'.route('article.edit', $article->id).'" class="btn btn-primary btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="deleteArticle('.$article->id.')">Delete</button>
                        </div>
                    ';
                })
                // Pastikan kolom yang mengandung HTML ditambahkan di rawColumns
                ->rawColumns(['status','button'])
                ->make(true);
        }

        // Jika bukan Ajax, tampilkan Blade
        return view('back.article.index');
    }

    /**
     * Form create artikel.
     */
    public function create()
    {
        return view('back.article.create', [
            'categories' => Category::all()
        ]);
    }

    /**
     * Simpan artikel baru.
     */
    public function store(ArticleRequest $request)
    {
        $data = $request->validated();

        // Upload file gambar
        $file = $request->file('img');
        $fileName = uniqid().'.'.$file->getClientOriginalExtension();
        $file->storeAs('public/back/', $fileName);
        $data['img'] = $fileName;

        // Tambahkan slug
        $data['slug'] = Str::slug($data['title']);

        Article::create($data);

        return redirect()->route('article.index')
            ->with('success', 'Data article has been created');
    }

    /**
     * Detail artikel.
     */
    public function show($id)
    {
        return view('back.article.show', [
            'article' => Article::findOrFail($id)
        ]);
    }

    /**
     * Form edit artikel.
     */
    public function edit($id)
    {
        return view('back.article.update', [
            'article' => Article::findOrFail($id),
            'categories' => Category::all()
        ]);
    }

    /**
     * Update artikel.
     */
    public function update(UpdateArticleRequest $request, $id)
    {
        $data = $request->validated();

        // Jika ada file baru
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/back/', $fileName);

            // Hapus file lama
            Storage::delete('public/back/'.$request->oldImg);

            $data['img'] = $fileName;
        } else {
            // Pakai file lama
            $data['img'] = $request->oldImg;
        }

        // Tambahkan slug
        $data['slug'] = Str::slug($data['title']);

        // Update data
        Article::findOrFail($id)->update($data);

        return redirect()->route('article.index')
            ->with('success', 'Data article has been updated');
    }

    /**
     * Hapus artikel.
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        // Hapus file gambar
        Storage::delete('public/back/'.$article->img);

        // Hapus record di database
        $article->delete();

        // Return response JSON untuk notifikasi di front-end
        return response()->json(['message' => 'Article deleted successfully']);
    }
}
