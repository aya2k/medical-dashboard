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
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'category' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'user_name' => 'nullable|string',
            'user_image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
        ]);

        $fileName = null;

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/blogs/image/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('image')->move($destinationPath, $fileName);


            $validated['image'] = asset('public/assets/blogs/image/' . $fileName);
        }



        $fileName = null;

        if ($request->hasFile('user_image')) {
            $fileName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/blogs/image/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('user_image')->move($destinationPath, $fileName);


            $validated['image'] = asset('public/assets/blogs/image/' . $fileName);
        }

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
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'is_active' => 'nullable|boolean',
            'user_name' => 'nullable|string',
            'user_image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'category' => 'nullable|string|max:255',


        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }


        if ($request->hasFile('image')) {

            // ✅ حذف الصورة القديمة (لو موجودة)
            if ($blog->image) {
                // نرجع المسار الحقيقي في السيرفر من الرابط الكامل
                $oldImagePath = str_replace(url('/'), public_path(), $blog->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // ✅ رفع الصورة الجديدة
            $fileName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/blogs/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('image')->move($destinationPath, $fileName);

            // ✅ حفظ لينك فعلي يمكن فتحه في المتصفح
            $validated['image'] = asset('public/assets/blogs/' . $fileName);
        }


        if ($request->hasFile('imuser_imageage')) {

            // ✅ حذف الصورة القديمة (لو موجودة)
            if ($blog->image) {
                // نرجع المسار الحقيقي في السيرفر من الرابط الكامل
                $oldImagePath = str_replace(url('/'), public_path(), $blog->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // ✅ رفع الصورة الجديدة
            $fileName = time() . '.' . $request->file('user_image')->extension();
            $destinationPath = public_path('assets/blogs/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('user_image')->move($destinationPath, $fileName);

            // ✅ حفظ لينك فعلي يمكن فتحه في المتصفح
            $validated['image'] = asset('public/assets/blogs/' . $fileName);
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
