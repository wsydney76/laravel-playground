<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        $topics = Topic::withCount('articles')->orderByRaw("JSON_EXTRACT(title, '$.en')")->get();

        return view('topics.index', compact('topics'));
    }

    public function show(string $locale, Topic $topic, string $slug): View
    {
        $articles = $topic
            ->articles()
            ->with(['user', 'topics'])
            ->published()
            ->latest()
            ->paginate(8);

        return view('topics.show', compact('topic', 'articles'));
    }
}
