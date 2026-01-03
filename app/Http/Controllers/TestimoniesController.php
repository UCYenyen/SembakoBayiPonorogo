<?php

namespace App\Http\Controllers;

use App\Models\ImageTestimony;
use Illuminate\Http\Request;
use App\Models\Testimony;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

class TestimoniesController extends Controller
{
    public function show(Testimony $testimony)
    {
        $testimony->load(['transactionItem.product', 'images']);
        return view('dashboard.user.testimonies.detail', compact('testimony'));
    }

    public function create(TransactionItem $transactionItem)
    {
        // Check if testimony already exists
        if ($transactionItem->testimony) {
            return redirect()->route('user.testimonies.edit', $transactionItem->testimony);
        }
        
        return view('dashboard.user.testimonies.create', compact('transactionItem'));
    }

    public function store(Request $request)
    {
        try {
            // Log untuk debugging
            Log::info('Testimony Store Request', [
                'all_data' => $request->except('media'),
                'has_files' => $request->hasFile('media'),
                'files_count' => $request->hasFile('media') ? count($request->file('media')) : 0
            ]);

            $validated = $request->validate([
                'transaction_item_id' => 'required|exists:transaction_items,id',
                'rating_star' => 'required|integer|min:1|max:5',
                'content' => 'required|string',
                'media' => 'nullable|array',
                'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,webm|max:51200',
            ]);

            // Check if testimony already exists
            $existingTestimony = Testimony::where('transaction_item_id', $request->transaction_item_id)->first();
            if ($existingTestimony) {
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => 'Testimony already exists'], 422);
                }
                return redirect()->back()->withErrors(['message' => 'Testimony already exists']);
            }

            $testimony = Testimony::create([
                'transaction_item_id' => $request->transaction_item_id,
                'rating_star' => $request->rating_star,
                'description' => $request->content,
            ]);

            Log::info('Testimony created', ['testimony_id' => $testimony->id]);

            // Handle file uploads
            if ($request->hasFile('media')) {
                $files = $request->file('media');
                Log::info('Processing files', ['count' => count($files)]);

                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $mimeType = $file->getMimeType();
                        Log::info('Processing file', [
                            'index' => $index,
                            'mime' => $mimeType,
                            'original_name' => $file->getClientOriginalName(),
                            'size' => $file->getSize()
                        ]);

                        if (Str::startsWith($mimeType, 'image/')) {
                            try {
                                $img = Image::read($file)->toWebp(80);
                                $path = 'testimonies/' . uniqid() . '.webp';
                                Storage::disk('public')->put($path, (string) $img);
                                
                                ImageTestimony::create([
                                    'testimony_id' => $testimony->id,
                                    'image_url' => $path,
                                ]);
                                
                                Log::info('Image saved', ['path' => $path]);
                            } catch (\Exception $e) {
                                Log::error('Image processing error', ['error' => $e->getMessage()]);
                            }
                        } elseif (Str::startsWith($mimeType, 'video/')) {
                            try {
                                $path = $file->store('testimonies', 'public');
                                
                                ImageTestimony::create([
                                    'testimony_id' => $testimony->id,
                                    'image_url' => $path,
                                ]);
                                
                                Log::info('Video saved', ['path' => $path]);
                            } catch (\Exception $e) {
                                Log::error('Video processing error', ['error' => $e->getMessage()]);
                            }
                        }
                    } else {
                        Log::warning('Invalid file', ['index' => $index]);
                    }
                }
            } else {
                Log::info('No media files in request');
            }

            if ($request->wantsJson()) {
                return response()->json(['status' => 'success', 'testimony_id' => $testimony->id], 200);
            }
            
            return redirect()->route('dashboard')->with('success', 'Ulasan berhasil dikirim!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Testimony creation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors(['message' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Testimony $testimony)
    {
        $testimony->load(['transactionItem.product', 'images']);
        return view('dashboard.user.testimonies.edit', compact('testimony'));
    }

    public function update(Request $request, Testimony $testimony)
    {
        $request->validate([
            'rating_star' => 'required|integer|min:1|max:5',
            'description' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov|max:51200',
            'deleted_media' => 'nullable|array',
            'deleted_media.*' => 'integer|exists:image_testimonies,id',
        ]);

        $testimony->update([
            'rating_star' => $request->rating_star,
            'description' => $request->description,
        ]);

        // Handle deleted media
        if ($request->has('deleted_media')) {
            foreach ($request->deleted_media as $imageId) {
                $image = ImageTestimony::find($imageId);
                if ($image && $image->testimony_id == $testimony->id) {
                    Storage::disk('public')->delete($image->image_url);
                    $image->delete();
                }
            }
        }

        // Handle new media uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $mimeType = $file->getMimeType();

                if (Str::startsWith($mimeType, 'image/')) {
                    $img = Image::read($file)->toWebp(80);
                    $path = 'testimonies/' . uniqid() . '.webp';
                    Storage::disk('public')->put($path, (string) $img);
                    
                    ImageTestimony::create([
                        'testimony_id' => $testimony->id,
                        'image_url' => $path,
                    ]);
                } elseif (Str::startsWith($mimeType, 'video/')) {
                    $path = $file->store('testimonies', 'public');
                    
                    ImageTestimony::create([
                        'testimony_id' => $testimony->id,
                        'image_url' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'Testimony updated successfully');
    }

    public function destroy(Testimony $testimony)
    {
        foreach ($testimony->images as $image) {
            Storage::disk('public')->delete($image->image_url);
            $image->delete();
        }
        $testimony->delete();
        
        return redirect()->route('dashboard')->with('success', 'Testimony deleted successfully');
    }
}