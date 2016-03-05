<?php
/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 3/5/16
 * Time: 10:14 AM
 */

namespace Michaeljoyner\Edible\Http;


use Illuminate\Routing\Controller;

class EdiblesController extends Controller
{
    public function showPage($pageId)
    {
        dd($pageId. ' Fuck yooooooo!');
        $page = Page::with('textblocks', 'galleries')->findOrFail($pageId);

        return view('edible::showpage')->with(compact('page'));
    }

    public function editTextblock($pageId, $textblockId)
    {
        $textblock = Textblock::findOrFail($textblockId);
        $page = Page::findOrFail($pageId);

        return view('edible::textblock')->with(compact('textblock', 'page'));
    }

    public function editGallery($pageId, $galleryId)
    {
        $gallery = Gallery::findOrFail($galleryId);
        $page = Page::findOrFail($pageId);

        return view('edible::gallery')->with(compact('gallery', 'page'));
    }

    public function updateTextblock(Request $request, $textblockId)
    {
        $textblock = Textblock::findOrFail($textblockId);
        $textblock->update(['content' => $request->get('content')]);

        return redirect('package-edible/pages/'.$textblock->page->id);
    }

    public function storeUploadedImage(Request $request, $galleryId)
    {
//        $this->validate($request, ['file' => 'required|image']);

        $image = $gallery = Gallery::findOrFail($galleryId)->addImage($request->file('file'));

        return response()->json([
            'image_id' => $image->id,
            'src' => $image->getUrl(),
            'thumb_src' => $image->getUrl('thumb')
        ]);
    }

    public function getGalleryImages($galleryId)
    {
        $images = Gallery::findOrFail($galleryId)->getMedia()->map(function($image) {
            return [
                'image_id' => $image->id,
                'src' => $image->getUrl(),
                'web_src' => $image->getUrl('web'),
                'thumb_src' => $image->getUrl('thumb')
            ];
        });

        return response()->json($images);
    }
}