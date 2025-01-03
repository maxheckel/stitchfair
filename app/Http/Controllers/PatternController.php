<?php

namespace App\Http\Controllers;

use App\Models\Pattern;
use Dflydev\DotAccessData\Data;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Mtownsend\RemoveBg\RemoveBg;


class PatternController extends Controller
{
    public function wizard()
    {
        return Inertia::render('Wizard/Index');
    }

    public function index()
    {

    }

    public function variants($uuid)
    {
        $STITCH_IMAGE_GEN_LINUX_BINARY = env('STITCH_BINARY');
        $pattern = Pattern::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();
        $originalImage = Storage::get($pattern->original_image_path);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_');

        // 3. Write the retrieved contents into the temporary file
        file_put_contents($tempFilePath, $originalImage);
        $width = $pattern->width;
        $height = $pattern->height;
        $count = $pattern->color_count;

        $gnerator = function () use ($STITCH_IMAGE_GEN_LINUX_BINARY, $tempFilePath, $width, $height, $count) {
            return json_decode(Process::path(__DIR__)->run(base_path($STITCH_IMAGE_GEN_LINUX_BINARY) . ' --image_path=' . $tempFilePath . ' --width=' . $width . ' --height=' . $height . '  --count=' . $count . ' --thread_db_path=' . base_path('database/threads.db'))->output());
        };
        $result = Concurrency::run([
            $gnerator,
            $gnerator,
            $gnerator,
            $gnerator,
            $gnerator,
            $gnerator,
            $gnerator,
            $gnerator,
        ]);
        return $result;
    }

    public function original_image($uuid)
    {
        $pattern = Pattern::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();
        header("Content-Type: image/png");

        echo Storage::get($pattern->original_image_path);
    }

    public function update_pixel(Request $request, $uuid){
        $pattern = Pattern::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();
        $pixels = json_decode($pattern->pixels);
        $pixels['pixels'][$request->get('coords')] = $request->get('color');
        $pattern->pixels = json_encode($pixels);
        $pattern->save();
    }

    public function update(Request $request, $uuid)
    {
        $pattern = Pattern::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();
        if ($request->has('width')){
            $pattern->width = $request->get('width');
            $pattern->height = $request->get('height');
            $pattern->color_count = $request->get('color_count');
            $pattern->pixels = null;
        }
        if ($request->has('image')){
            Storage::delete($pattern->original_image_path);
            $originalImagePath = $this->uploadBase64ImageToDisk($request->get('image'), $request->get('remove_background'));
            $pattern->original_image_path = $originalImagePath;
        }

        if ($request->has('pixels')){
            $pattern->pixels = json_encode($request->get('pixels'));
        }

        $pattern->save();
        return $pattern;
    }

    public function store(Request $request)
    {
        $originalImagePath = $this->uploadBase64ImageToDisk($request->get('image'), $request->get('remove_background'));
        $pattern = new Pattern();
        $pattern->original_image_path = $originalImagePath;
        $pattern->width = $request->get('width');
        $pattern->height = $request->get('height');
        $pattern->color_count = $request->get('color_count');
        $pattern->save();
        return [
            'pattern' => Pattern::find($pattern->id),
            'image' => Storage::temporaryUrl(
                $pattern->original_image_path,
                now()->addMinutes(5)
            )
        ];

    }

    public function show($uuid)
    {
        $pattern = Pattern::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();

        return Inertia::render('Wizard/Index', [
            'pattern' => $pattern,
            'image' => Storage::temporaryUrl(
                $pattern->original_image_path,
                now()->addMinutes(5)
            ),
            'threads' => DB::connection('threads')->select('select * from threads')
        ]);
    }


    function uploadBase64ImageToDisk($base64String, $removeBG = true)
    {
        if ($removeBG) {
            $removeBG = new RemoveBg(env('REMOVE_BG_API_KEY'));
            $removeBG->base64($base64String)->get();
            // Decode the base64 string
            $imageData = $removeBG->base64($base64String)->body([
                'size' => 'preview',
                'format' => 'png',
                'channels' => 'rgba',
            ])->get();

        } else {
            // Remove any unnecessary base64 metadata if present
            $base64String = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $base64String);
            $imageData = base64_decode($base64String);
        }
        if ($imageData === false) {
            throw new Exception("Invalid base64 data provided.");
        }

        // Generate a unique filename for the image
        $filename = 'images/' . uniqid('image_', true) . '.png';

        // Store the image on the specified disk
        Storage::put($filename, $imageData);

        // Return the full path of the uploaded image
        return $filename;
    }
}
