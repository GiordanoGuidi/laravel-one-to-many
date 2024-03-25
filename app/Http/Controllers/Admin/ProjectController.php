<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreProjectRequest;


use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderByDesc('updated_at')->orderByDesc('created_at')->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $project = new Project();
        return view('admin.projects.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->all();
        $project = new Project();
        $project->fill($data);
        $project->slug = Str::slug($project->title);
        //Controllo se mi arriva un file
        if (Arr::exists($data, 'image')) {
            $extension = $data['image']->extension();
            //Lo salvo e prendo l'url
            $img_url = Storage::putFileAs('project_images', $data['image'], $project->slug . '.' . $extension);
            $project->image = $img_url;
        }
        $project->save();
        return to_route('admin.projects.show', $project->id)
            //Flash data
            ->with('message', "Progetto {$project->title} creato con successo")
            ->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {

        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {


        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        //Controllo se mi arriva un file
        if (Arr::exists($data, 'image')) {
            //Controllo se c'era gia un immagine e la cancello
            if ($project->image) Storage::delete($project->image);
            $extension = $data['image']->extension();

            //Lo salvo e prendo l'url
            $img_url = Storage::putFileAs('project_images', $data['image'], "{$data['slug']}.$extension");
            $project->image = $img_url;
        }

        $project->update($data);
        return to_route('admin.projects.index', compact('project'))
            //Flash data
            ->with('type', 'success')
            ->with('message', "Progetto {$project->title} modificato correttamente.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if ($project->image) Storage::delete($project->image);
        $project->delete();
        return to_route('admin.projects.index')
            //Flash data
            ->with('toast-button-type', 'danger')
            ->with('toast-message', "Progetto {$project->title} eliminato con successo")
            ->with('toast-label', config('app.name'))
            ->with('toast-message', "Progetto {$project->title} eliminato")
            ->with('toast-method', 'PATCH')
            ->with('toast-route', route('admin.projects.restore', $project->id))
            ->with('toast-button-label', 'Annulla');
    }

    //#Rotte soft delete
    public function trash()
    {
        $projects = Project::onlyTrashed()->get();
        return view('admin.projects.trash', compact('projects'));
    }

    public function restore(Project $project)
    {
        $project->restore();

        return to_route('admin.projects.index', $project->id)
            ->with('type', 'success')
            ->with('message', "Progetto {$project->title} ripristinato con successo");
    }

    public function drop(Project $project)
    {
        $project->forceDelete();
        return to_route('admin.projects.trash')
            ->with('type', 'danger')
            ->with('message', "Progetto {$project->title} eliminato definitivamente");
    }
}
