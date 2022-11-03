<?php

namespace BrandStudio\Page\Http\Controllers\Admin;

use BrandStudio\Page\Http\Requests\Admin\PageRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use BrandStudio\Page\Facades\TemplateManager;

class PageCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    public function setup()
    {
        CRUD::setModel(config('page.page_class'));
        CRUD::setRoute(config('backpack.base.route_prefix') . '/page');
        CRUD::setEntityNameStrings(trans_choice('page::admin.pages', 1), trans_choice('page::admin.pages', 2));
        CRUD::orderBy('status', 'desc')->orderBy('parent_id')->orderBy('lft');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'name',
                'label' => trans('page::admin.name'),
            ],
            [
                'name' => 'status',
                'label' => trans('page::admin.status'),
                'type' => 'select_from_array',
                'options' => config('page.page_class')::getStatusOptions(),
            ],
            [
                'name' => 'slug',
                'label' => trans('page::admin.slug'),
            ],
            [
                'name' => 'updated_at',
                'label' => trans('page::admin.updated_at'),
                'type' => 'datetime',
            ],
            [
                'name' => 'created_at',
                'label' => trans('page::admin.created_at'),
            ],
        ]);
    }

    protected function setupShowOperation()
    {
        CRUD::set('show.setFromDb', false);
        $entry = $this->crud->getEntry(request()->id);
        $this->setupListOperation();

        if ($entry->template) {
            $template = TemplateManager::getTemplate($entry->template);
            CRUD::addColumns($template->allColumns());
        }
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PageRequest::class);
        $template = request()->template ?? $this->crud->entry->template ?? null;

        CRUD::addFields([
            [
                'name' => 'name',
                'label' => trans('page::admin.name'),
                'type' => 'text',
                'attributes' => [
                    'required' => true,
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-sm-8 required',
                ]
            ],
            [
                'name' => 'status',
                'label' => trans('page::admin.status'),
                'type' => 'select2_from_array',
                'options' => config('page.page_class')::getStatusOptions(),
                'wrapperAttributes' => [
                    'class' => 'form-group col-sm-4'
                ]
            ],
            [
                'name' => 'template',
                'label' => trans('page::admin.template'),
                'type' => 'select2_from_array',
                'options' => TemplateManager::getTemplates(null, null, false),
                'allows_null' => true,
                'value' => $template,
                'attributes' => [
                    'onchange' => "if (confirm('Обновить страницу?')) {window.location.search='template='+this.value;}",
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-sm-12',
                ],
            ],
        ]);

        $template = TemplateManager::getTemplate($template);
        CRUD::addFields($template->allFields());

        if($template->fake()) {
            CRUD::removeField('template');
        } else if ($template->key() != 'home') {
            $this->crud->addField([
                'name' => 'slug',
                'label' => trans('page::admin.slug'),
                'type' => 'text',
                'tab' => trans('page::admin.advanced'),
            ]);
        }
    }

    protected function setupUpdateOperation()
    {
        $entry = $this->crud->getEntry(request()->id);
        $this->setupCreateOperation();
    }


    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'name');
        CRUD::set('reorder.max_level', 2);
        CRUD::addClause('where', function($query) {
            $query->whereNull('template')->orWhereIn('template', array_keys(TemplateManager::getTemplates(true)));
        });
    }

}
