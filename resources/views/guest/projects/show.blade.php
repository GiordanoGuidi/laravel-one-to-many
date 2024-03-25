@extends('layouts.app')

@section('title','Project')

@section('content')
    <section id="guest-show" class="my-5">
        <h1 class="mb-5">{{$project->title}}</h1>
        <div>
            <div class="clearfix">
                @if($project->image)
                    <img class="float-start me-5" src="{{asset('storage/' . $project->image)}}" alt="{{$project->title}}">
                    @endif
                <p>{{$project->content}}</p>
                <div>
                    <strong>Creato il :</strong><p>{{$project->created_at}}</p>
                    <strong>Ultima modifica :</strong><p>{{$project->updated_at}}</p>
                </div>
            </div>
        </div>
    
        <footer class="d-flex justify-content-between align-items-center my-5">
            <a href="{{route('guest.home')}}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Torna indietro</a>
        </footer>
    </section>
@endsection
