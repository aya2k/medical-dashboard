<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    // 🟢 عرض كل الخدمات
   public function index()
{
    $services = Service::latest('created_at')->get();

    return response()->json($services);
}

    // 🟢 إنشاء خدمة جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'link' => 'nullable|string',
            'features' => 'nullable|array',

        ]);

        // 👈 default null
        $validated['image'] = null;

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/services/image/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('image')->move($destinationPath, $fileName);

            $validated['image'] = asset('public/assets/services/image/' . $fileName);
        }

        $service = Service::create($validated);

        return response()->json([
            'message' => 'Service created successfully ✅',
            'data' => $service
        ]);
    }


    // 🟢 عرض خدمة محددة
    public function show($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }

    // 🟢 تحديث خدمة
    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'link' => 'nullable|string',
            'features' => 'nullable|array',


        ]);



        if ($request->hasFile('image')) {

            // ✅ حذف الصورة القديمة
            if ($service->image) {
                $oldImagePath = str_replace(url('/'), public_path(), $service->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // ✅ رفع الجديدة
            $fileName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/services/image/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('image')->move($destinationPath, $fileName);

            // ✅ URL كامل (يكسر الكاش)
            $validated['image'] = asset('public/assets/services/image/' . $fileName);
        }

        $service->update($validated);

        return response()->json([
            'message' => 'Service updated successfully ✅',
            'data' => $service
        ]);
    }

    // 🟢 حذف خدمة
    public function destroy($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully ✅']);
    }
}
