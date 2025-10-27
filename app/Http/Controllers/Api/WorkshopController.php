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
        $workshops = Workshop::latest()->get();
        return response()->json(['data' => $workshops]);
    }

    // ✅ إنشاء ورشة جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'link' => 'nullable|url',
            'features' => 'nullable|array',
        ]);

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('workshops/videos', 'public');
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

            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'link' => 'nullable|url',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
        ]);

        if ($request->hasFile('video')) {
            if ($workshop->video) {
                Storage::disk('public')->delete($workshop->video);
            }
            $validated['video'] = $request->file('video')->store('workshops/videos', 'public');
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
