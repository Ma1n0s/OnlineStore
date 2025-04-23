<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Support\Facades\Log;

class CategoryEditScreen extends Screen
{
    public $category;

    public function query(Category $category): array
    {
        $parentId = request()->input('parent_id');
        
        return [
            'category' => $category,
            'parentCategories' => Category::when($category->exists, function($query) use ($category) {
                    $query->whereNotIn('id', $category->descendants()->pluck('id'))
                        ->where('id', '!=', $category->id);
                })
                ->when($parentId, function($query) use ($parentId) {
                    $query->where('id', $parentId);
                }, function($query) {
                    $query->whereNull('parent_id');
                })
                ->get()
        ];
    }

    public function name(): ?string
    {
        return $this->category->exists ? 'Edit Category' : 'Create Category';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Save')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        $parentId = request()->input('parent_id');
        $isCreatingSubcategory = !$this->category->exists && $parentId;
        
        return [
            Layout::rows([
                Input::make('category.name')
                    ->title('Name')
                    ->required(),
                    
                Input::make('category.title')
                    ->title('Display Title'),
                    
                TextArea::make('category.description')
                    ->title('Description')
                    ->rows(3),

                $isCreatingSubcategory 
                    ? Input::make('category.parent_id')
                        ->title('Parent Category')
                        ->value($parentId)
                        ->readonly()
                        ->help('This category will be a subcategory of: ' . Category::find($parentId)->name)
                    : Select::make('category.parent_id')
                        ->title('Parent Category')
                        ->empty('No parent (root category)', '0')
                        ->fromQuery(
                            Category::when($this->category->exists, function($query) {
                                $query->whereNotIn('id', $this->category->descendants()->pluck('id'))
                                    ->where('id', '!=', $this->category->id);
                            }),
                            'name'
                        ),

                Input::make('category.slug')
                    ->title('Slug')
                    ->help('Leave empty to auto-generate from name'),

                Upload::make('category.image_url')
                    ->title('Main Image')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories'),

                Upload::make('category.description_image_url')
                    ->title('Description Image')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories'),
            ]),
        ];
    }

    public function save(Category $category, Request $request)
    {
        $data = $request->get('category');
        
        // Validation
        $exists = Category::where('name', $data['name'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($exists) {
            Alert::error('Category name already exists!');
            return back();
        }
        
        // Handle parent_id
        if (isset($data['parent_id']) && $data['parent_id'] === '0') {
            $data['parent_id'] = null;
        }
        
        // Set parent_id for new subcategories
        if (!$category->exists && $request->has('parent_id')) {
            $data['parent_id'] = $request->input('parent_id');
        }
        
        // Generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Ensure unique slug
        $slugExists = Category::where('slug', $data['slug'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($slugExists) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }

        // Handle image uploads
        if ($request->has('category.image_url')) {
            $image = $request->input('category.image_url');
            if (is_array($image) && !empty($image)) {
                // Delete old image if exists
                if ($category->exists && $category->image_url) {
                    // If image_url is an ID, find and delete the attachment
                    $attachment = Attachment::find($category->image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                
                // Ensure the file is physically stored
                $attachment = Attachment::find($image[0]);
                if ($attachment) {
                    // Log attachment details for debugging
                    Log::info('Processing attachment', [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'path' => $attachment->path,
                        'disk' => $attachment->disk,
                        'physicalPath' => $attachment->physicalPath(),
                    ]);

                    // Create directory if it doesn't exist
                    $directory = 'app/public/' . $attachment->path;
                    if (!file_exists(storage_path($directory))) {
                        mkdir(storage_path($directory), 0755, true);
                    }
                    
                    // Check if file already exists at destination
                    $destinationPath = storage_path($directory . '/' . $attachment->physicalPath());
                    if (!file_exists($destinationPath)) {
                        // Try to get the file from the upload
                        $sourcePath = storage_path('app/uploads/' . $attachment->physicalPath());
                        if (file_exists($sourcePath)) {
                            // Copy file to final destination
                            copy($sourcePath, $destinationPath);
                            Log::info('Copied file from uploads folder', [
                                'source' => $sourcePath,
                                'destination' => $destinationPath,
                            ]);
                        } else {
                            // Try from temporary directory
                            $tempPath = storage_path('app/public/temp/' . $attachment->name);
                            if (file_exists($tempPath)) {
                                copy($tempPath, $destinationPath);
                                Log::info('Copied file from temp folder', [
                                    'source' => $tempPath,
                                    'destination' => $destinationPath,
                                ]);
                            } else {
                                Log::error('Could not find file to copy', [
                                    'uploadPath' => $sourcePath,
                                    'tempPath' => $tempPath,
                                ]);
                            }
                        }
                    }
                }
                
                $data['image_url'] = $image[0];
            } elseif (empty($image)) {
                // Image was removed
                if ($category->exists && $category->image_url) {
                    // If image_url is an ID, find and delete the attachment
                    $attachment = Attachment::find($category->image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                $data['image_url'] = null;
            }
        }

        if ($request->has('category.description_image_url')) {
            $descImage = $request->input('category.description_image_url');
            if (is_array($descImage) && !empty($descImage)) {
                // Delete old image if exists
                if ($category->exists && $category->description_image_url) {
                    // If description_image_url is an ID, find and delete the attachment
                    $attachment = Attachment::find($category->description_image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                
                // Ensure the file is physically stored
                $attachment = Attachment::find($descImage[0]);
                if ($attachment) {
                    // Log attachment details for debugging
                    Log::info('Processing description attachment', [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'path' => $attachment->path,
                        'disk' => $attachment->disk,
                        'physicalPath' => $attachment->physicalPath(),
                    ]);

                    // Create directory if it doesn't exist
                    $directory = 'app/public/' . $attachment->path;
                    if (!file_exists(storage_path($directory))) {
                        mkdir(storage_path($directory), 0755, true);
                    }
                    
                    // Check if file already exists at destination
                    $destinationPath = storage_path($directory . '/' . $attachment->physicalPath());
                    if (!file_exists($destinationPath)) {
                        // Try to get the file from the upload
                        $sourcePath = storage_path('app/uploads/' . $attachment->physicalPath());
                        if (file_exists($sourcePath)) {
                            // Copy file to final destination
                            copy($sourcePath, $destinationPath);
                            Log::info('Copied file from uploads folder', [
                                'source' => $sourcePath,
                                'destination' => $destinationPath,
                            ]);
                        } else {
                            // Try from temporary directory
                            $tempPath = storage_path('app/public/temp/' . $attachment->name);
                            if (file_exists($tempPath)) {
                                copy($tempPath, $destinationPath);
                                Log::info('Copied file from temp folder', [
                                    'source' => $tempPath,
                                    'destination' => $destinationPath,
                                ]);
                            } else {
                                Log::error('Could not find file to copy', [
                                    'uploadPath' => $sourcePath,
                                    'tempPath' => $tempPath,
                                ]);
                            }
                        }
                    }
                }
                
                $data['description_image_url'] = $descImage[0];
            } elseif (empty($descImage)) {
                // Image was removed
                if ($category->exists && $category->description_image_url) {
                    // If description_image_url is an ID, find and delete the attachment
                    $attachment = Attachment::find($category->description_image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                $data['description_image_url'] = null;
            }
        }

        $category->fill($data)->save();

        Alert::success('Category was saved');
        
        if ($category->parent_id) {
            return redirect()->route('platform.category.action', $category->parent);
        }
        
        return redirect()->route('platform.category.list');
    }
}