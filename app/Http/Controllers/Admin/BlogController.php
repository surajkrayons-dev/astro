<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends AdminController
{
    public function getIndex(Request $request)
    {
        return view('admin.blogs.index');
    }

    public function getList(Request $request)
    {
        $list = Blog::select('blogs.*')
            ->with('category')
            ->when($request->blog_category_id, function ($q) use ($request) {
                $q->where('blogs.blog_category_id', $request->blog_category_id);
            })
            ->when($request->blog_id, function ($q) use ($request) {
                $q->where('blogs.id', $request->blog_id);
            })
            ->orderBy('blogs.id', 'desc');

        return \DataTables::of($list)
            ->addColumn('category_name', function ($row) {
                return $row->category?->name ?? '-';
            })
            ->editColumn('name', function ($row) {
                $words = explode(' ', $row->name);
                return count($words) > 5
                    ? implode(' ', array_slice($words, 0, 5)) . '...'
                    : $row->name;
            })
            ->editColumn('slug', function ($row) {
                $words = explode('-', $row->slug);
                return count($words) > 5
                    ? implode('-', array_slice($words, 0, 5)) . '...'
                    : $row->slug;
            })
            ->make();
    }

    public function getCreate()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blogs.create', compact('categories'));
    }

    public function postCreate(Request $request)
    {
        $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|unique:blogs,slug',
            'image'    => 'nullable|image',
            'date'             => 'nullable|date',
            'description'      => 'nullable|string',
        ]);

        try {
            $blog = new Blog();
            $blog->blog_category_id = $request->blog_category_id;
            $blog->name = $request->name;
            $blog->slug = Str::slug($request->slug ?? $request->name);
            $blog->date = $request->date;
            $blog->description = $request->description;

            if ($request->hasFile('image')) {
                $blog->image = uploadFile('image', null, null, 'blog');
            }

            $blog->save();

            return response()->json([
                'message' => 'Blog created successfully'
            ]);
        } catch (\Throwable $e) {
            \Log::error($e);

            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function getUpdate(Request $request)
    {
        $blog = Blog::findOrFail($request->id);
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blogs.update', compact('blog', 'categories'));
    }

    public function postUpdate(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'name'             => 'required|string|max:255',
            'slug'             => 'required|unique:blogs,slug,' . $blog->id,
            'image'    => 'nullable|image',
            'date'             => 'nullable|date',
            'description'      => 'nullable|string',
        ]);

        try {
            $blog->blog_category_id = $request->blog_category_id;
            $blog->name = $request->name;
            $blog->slug = Str::slug($request->slug);
            $blog->date = $request->date;
            $blog->description = $request->description;

            if ($request->hasFile('image')) {
                $blog->image = uploadFile('image', null, null, 'blog', $blog->image);
            }

            $blog->save();

            return response()->json([
                'message' => 'Blog updated successfully'
            ]);
        } catch (\Throwable $e) {
            \Log::error($e);

            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function getDelete(Request $request)
    {
        $blog = Blog::findOrFail($request->id);
        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully'
        ]);
    }
}