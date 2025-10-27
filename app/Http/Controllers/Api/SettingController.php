<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
   public function index()
    {
        $settings = Setting::first();
        return response()->json($settings);
    }

    // 🟢 إنشاء الإعدادات (مرة واحدة فقط)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'twitter' => 'nullable|string',
            'whatsapp'=> 'nullable|string',
        ]);

        $setting = Setting::create($validated);

        return response()->json([
            'message' => 'Settings created successfully ✅',
            'data' => $setting
        ]);
    }

    // 🟢 تحديث الإعدادات
    public function update(Request $request, $id)
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json(['message' => 'Settings not found'], 404);
        }

        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'twitter' => 'nullable|string',
            'whatsapp'=> 'nullable|string',
        ]);

        $setting->update($validated);

        return response()->json([
            'message' => 'Settings updated successfully ✅',
            'data' => $setting
        ]);
    }
}
