<?php

namespace BrandStudio\Page\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use BrandStudio\Page\Facades\TemplateManager;

use BrandStudio\Page\Http\Resources\PageResource;
use BrandStudio\Page\Http\Resources\PageSmallResource;
use BrandStudio\Page\Http\Resources\MenuItemResource;

class PageController extends Controller
{

    public function index(Request $request)
    {
        $pages = config('page.page_class')::active()
                ->whereNotNull('template')
                ->get()
                ->map(function($page) {
                    return new PageSmallResource($page);
                })
                ->keyBy('template');

        $menu = config('page.page_class')::active()
                ->orderBy('lft')
                ->where(function($query) {
                    $query->whereNull('template')
                          ->orWhereIn('template', array_keys(TemplateManager::getTemplates(true)));
                })
                ->whereDoesntHave('parent')
                ->with([
                    'children' => function($query) {
                        $query->active()->orderBy('lft');
                    }
                ])
                ->get()
                ->map(function($page) {
                    return new MenuItemResource($page);
                });

        return [
            'pages' => $pages,
            'menu' => $menu
        ];
    }

    public function show(Request $request, $page)
    {
        $page = config('page.page_class')::active()->whereSlug($page)->firstOrFail();
        return response()->json(new PageResource($page));
    }



}
