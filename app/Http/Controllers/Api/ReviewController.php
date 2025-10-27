<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
   public function index()
    {
        $reviews = Review::latest()->get();
        return response()->json($reviews);
    }

    // ✅ إنشاء ريفيو جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'review' => 'required|string',
            'user_name' => 'required|string|max:255',
            'user_job' => 'nullable|string|max:255',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('user_image')) {
            $fileName = time() . '.' . $request->file('user_image')->extension();
            $destinationPath = public_path('assets/reviews/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('user_image')->move($destinationPath, $fileName);
            $validated['user_image'] = 'assets/reviews/' . $fileName;
        }

        $review = Review::create($validated);
        return response()->json(['message' => 'Review created successfully ✅', 'data' => $review]);
    }

    // ✅ عرض ريفيو محدد
    public function show($id)
    {
        $review = Review::findOrFail($id);
        return response()->json($review);
    }

    // ✅ تعديل ريفيو
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'review' => 'required|string',
            'user_name' => 'required|string|max:255',
            'user_job' => 'nullable|string|max:255',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('user_image')) {
            // حذف الصورة القديمة لو موجودة
            if ($review->user_image && file_exists(public_path($review->user_image))) {
                unlink(public_path($review->user_image));
            }

            $fileName = time() . '.' . $request->file('user_image')->extension();
            $destinationPath = public_path('assets/reviews/');
            $request->file('user_image')->move($destinationPath, $fileName);
            $validated['user_image'] = 'assets/reviews/' . $fileName;
        }

        $review->update($validated);
        return response()->json(['message' => 'Review updated successfully ✅', 'data' => $review]);
    }

    // ✅ حذف ريفيو
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_image && file_exists(public_path($review->user_image))) {
            unlink(public_path($review->user_image));
        }

        $review->delete();
        return response()->json(['message' => 'Review deleted successfully 🗑️']);
    }
}
