@extends("layout.bst")
@section('title'){{$title}}@endsection
@section('content')
 <h2>电影选座</h2>
   @foreach($data as $k=>$v)
       <p>
           @if($v==1)
               <a href="/movie/buy/{{$k}}/{{$v}}" class="btn btn-danger">座位{{$k+1}}</a>
           @else
               <a href="/movie/buy/{{$k}}/{{$v}}" class="btn btn-success">座位{{$k+1}}</a>
           @endif
       </p>
   @endforeach
@endsection
@section("footer")
    @parent
@endsection