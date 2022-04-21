@extends('layouts.app')

@section('coursEtudiant')
<div class="container">
    <h1>Mes Formations </h1>    
<div class="row "> 
   
@foreach($formationInscrit as $fr)
<a href="{{ route('CoursDeFormation',$fr->formation)}}" class="btn btn-warning" style="margin-right: 2vw">Cours de {{$fr->formation}}</a><br>
{{-- <div class="col-lg-6 mb-4" style="padding-top:15px">
<div class="card" style="height:40vh;width:20vw;">
  <div class="card-header">
 
    Cours : {{$data->titre}}
 
  </div>
  <div class="card-body" >
   
    <a href="{{route('listeC', $data->file)}}">
  <img class="card-img-top" src="{{asset('css/pdflogo.png')}}" style="width:140px" alt="Card image cap">
    <p class="card-text">{{$data->description}}</p></a>
    <p>publié le {{$data->created_at}}</p>
  </div>
  </div>
  <a href="{{route('downloadCours',$data->file)}}"><button type="submit">Download</button></a>
</div> --}}
@endforeach 
</div>
</div>
@endsection