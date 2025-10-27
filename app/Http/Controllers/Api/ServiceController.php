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
        return response()->json(Service::all());
    }

    // 🟢 إنشاء خدمة جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|image',
           
        ]);

      

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
            'image' => 'nullable|file|image',
           
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
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
