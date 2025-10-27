<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    // 🟢 عرض كل المقالات
    public function index()
    {
        return response()->json(Blog::all());
    }

    // 🟢 إنشاء مقال جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|file|image',
            'is_active' => 'nullable|boolean',
            'user_name'=> 'nullable|string',
             'user_image'=> 'nullable|string',
        ]);

      

        $blog = Blog::create($validated);

        return response()->json([
            'message' => 'Blog created successfully ✅',
            'data' => $blog
        ]);
    }

    // 🟢 عرض مقال محدد
    public function show($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        return response()->json($blog);
    }

    // 🟢 تحديث مقال
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image' => 'nullable|file|image',
            'is_active' => 'nullable|boolean',
             'user_name'=> 'nullable|string',
              'user_image'=> 'nullable|string',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $blog->update($validated);

        return response()->json([
            'message' => 'Blog updated successfully ✅',
            'data' => $blog
        ]);
    }

    // 🟢 حذف مقال
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully ✅']);
    }
}
