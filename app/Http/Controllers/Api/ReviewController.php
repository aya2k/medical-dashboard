<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;




class ReviewController extends Controller
{
    private function imageDirectory(): string
    {
        return public_path('assets/reviews/image');
    }

    private function uploadImage($file): string
    {
        $directory = $this->imageDirectory();

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Unique filename prevents browser caching old image
        $fileName = Str::uuid()->toString()
            . '.' . $file->extension();

        $file->move($directory, $fileName);

        return asset('assets/reviews/image/' . $fileName);
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $urlPath = parse_url($image, PHP_URL_PATH);

        if (!$urlPath) {
            return;
        }

        $directory = '/assets/reviews/image/';

        if (!str_contains($urlPath, $directory)) {
            return;
        }

        $fileName = basename($urlPath);

        $path = $this->imageDirectory()
            . DIRECTORY_SEPARATOR
            . $fileName;

        if (File::isFile($path)) {
            File::delete($path);
        }
    }

    private function getImageFile(Request $request)
    {
        if ($request->hasFile('user_image')) {
            return $request->file('user_image');
        }

        if ($request->hasFile('image')) {
            return $request->file('image');
        }

        return null;
    }

    public function index()
    {
        return response()->json(
            Review::latest()->get()
        )->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'review' => 'required|string',
            'user_name' => 'required|string|max:255',
            'user_job' => 'nullable|string|max:255',

            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'is_active' => 'nullable|boolean',
        ]);

        unset($validated['image'], $validated['user_image']);

        $file = $this->getImageFile($request);

        $newImage = null;

        if ($file) {
            $newImage = $this->uploadImage($file);
        }

        $validated['user_image'] = $newImage;

        try {
            $review = Review::create($validated);
        } catch (\Throwable $e) {
            if ($newImage) {
                $this->deleteImage($newImage);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Review created successfully',
            'data' => $review->fresh(),
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            Review::findOrFail($id)
        )->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'review' => 'sometimes|nullable|string',
            'user_name' => 'sometimes|nullable|string|max:255',
            'user_job' => 'sometimes|nullable|string|max:255',

            'user_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'is_active' => 'sometimes|nullable|boolean',
        ]);

        unset($validated['image'], $validated['user_image']);

        $oldImage = $review->user_image;
        $file = $this->getImageFile($request);
        $newImage = null;

        if ($file) {
            if (!$file->isValid()) {
                throw ValidationException::withMessages([
                    'user_image' => 'Invalid image upload.',
                ]);
            }

            $newImage = $this->uploadImage($file);

            $validated['user_image'] = $newImage;
        }

        try {
            $review->update($validated);
        } catch (\Throwable $e) {
            if ($newImage) {
                $this->deleteImage($newImage);
            }

            throw $e;
        }

        $review->refresh();

        if ($newImage && $review->user_image === $newImage) {
            if ($oldImage !== $newImage) {
                $this->deleteImage($oldImage);
            }
        }

        return response()->json([
            'message' => 'Review updated successfully',
            'data' => $review,
        ])->header(
            'Cache-Control',
            'no-store, no-cache, must-revalidate'
        );
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        $oldImage = $review->user_image;

        $review->delete();

        $this->deleteImage($oldImage);

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }
}

