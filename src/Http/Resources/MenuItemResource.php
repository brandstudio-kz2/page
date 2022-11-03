<?php

namespace BrandStudio\Page\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use BrandStudio\Page\Facades\TemplateManager;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge([
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'template' => $this->template,
            'children' => $this->children->map(function($page) {
                return new MenuItemResource($page);
            }),
        ], $this->template ? TemplateManager::getTemplate($this->template)->prepareMenuItem($this) : []);
    }
}
