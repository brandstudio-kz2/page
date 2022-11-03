<?php

namespace BrandStudio\Page\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use BrandStudio\Page\Facades\TemplateManager;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return TemplateManager::getTemplate($this->template)->preparePage($this);
    }
}
