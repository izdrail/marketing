<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Webview;

use Exception;
use Illuminate\Contracts\View\View as ViewContract;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Models\Message;
use Cornatul\Marketing\Base\Services\Content\MergeContentService;

class WebviewController extends Controller
{
    /** @var MergeContentService */
    private MergeContentService $merger;

    public function __construct(MergeContentService $merger)
    {
        $this->merger = $merger;
    }

    /**
     * @throws Exception
     */
    public function show(string $messageHash): ViewContract
    {
        /** @var Message $message */
        $message = Message::with('subscriber')->where('hash', $messageHash)->first();

        $content = $this->merger->handle($message);

        return view('marketing::webview.show', compact('content'));
    }
}
