@extends('layouts.email')

@section('content')
    <h1 class="header">Ответ на ваше обращение: {{ $subject }}</h1>
    
    <div class="message-block original">
        <h3>Ваше сообщение:</h3>
        <p>{{ $originalMessage }}</p>
    </div>
    
    <div class="message-block response">
        <h3>Наш ответ:</h3>
        {!! $responseContent !!}
    </div>
    
    <p>С уважением,<br>Служба поддержки</p>
@endsection