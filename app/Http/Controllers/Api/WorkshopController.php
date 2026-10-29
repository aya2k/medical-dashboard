<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workshop;
use Illuminate\Support\Facades\Storage;

class WorkshopController extends Controller
{
    // ✅ عرض جميع الورش
    public function index()
    {
        $workshops = Workshop::latest('created_at')->get();

        return response()->json([
            'data' => $workshops
        ]);
    }

    // ✅ إنشاء ورشة جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'features' => 'nullable|array',
            'link' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('video')) {
            $videoName = time() . '.' . $request->file('video')->extension();
            $videoDestination = public_path('assets/workshops/videos/');

            if (!file_exists($videoDestination)) {
                mkdir($videoDestination, 0777, true);
            }

            $request->file('video')->move($videoDestination, $videoName);

            // حفظ لينك فعلي يمكن فتحه من المتصفح
            $validated['video'] = asset('public/assets/workshops/videos/' . $videoName);
        }

        $fileName = null;

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/workshops/image/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('image')->move($destinationPath, $fileName);


            $validated['image'] = asset('public/assets/workshops/image/' . $fileName);
        }

        $workshop = Workshop::create($validated);

        return response()->json([
            'message' => 'Workshop created successfully ✅',
            'data' => $workshop
        ]);
    }

    // ✅ عرض ورشة واحدة
    public function show($id)
    {
        $workshop = Workshop::find($id);

        if (! $workshop) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $workshop]);
    }

    // ✅ تحديث ورشة
    public function update(Request $request, $id)
    {
        $workshop = Workshop::find($id);

        if (! $workshop) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'link' => 'nullable|string',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
        ]);

        if ($request->hasFile('video')) {

            // حذف الفيديو القديم لو موجود
            if ($workshop->video) {
                $oldVideoPath = str_replace(url('/'), public_path(), $workshop->video);
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }
            }

            // رفع الفيديو الجديد
            $videoName = time() . '.' . $request->file('video')->extension();
            $videoDestination = public_path('assets/workshops/videos/');

            if (!file_exists($videoDestination)) {
                mkdir($videoDestination, 0777, true);
            }

            $request->file('video')->move($videoDestination, $videoName);

            // حفظ لينك فعلي يمكن فتحه من المتصفح
            $validated['video'] = asset('public/assets/workshops/videos/' . $videoName);
        }

        if ($request->hasFile('image')) {


            if ($workshop->image) {
                $oldImagePath = str_replace(url('/'), public_path(), $workshop->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }


            $fileName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/workshops/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('image')->move($destinationPath, $fileName);
            $validated['image'] = asset('public/assets/workshops/' . $fileName);
        }


        $workshop->update($validated);

        return response()->json([
            'message' => 'Workshop updated successfully ✅',
            'data' => $workshop
        ]);
    }

    // ✅ حذف ورشة
    public function destroy($id)
    {
        $workshop = Workshop::find($id);

        if (! $workshop) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($workshop->video) {
            Storage::disk('public')->delete($workshop->video);
        }

        $workshop->delete();

        return response()->json(['message' => 'Workshop deleted successfully ✅']);
    }
}
