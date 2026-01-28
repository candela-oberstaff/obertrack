@extends('emails.layout')

@section('title', $subject ?? 'Comunicación de Obertrack')

@section('content')
<div style="font-family: 'Montserrat', sans-serif;">
    <h2 class="title" style="color: #1a202c; font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: -0.025em; border-bottom: 2px solid #22A9C8; display: inline-block; padding-bottom: 8px; margin-bottom: 30px;">
        {{ $subject }}
    </h2>
    
    <div style="color: #4a5568; font-size: 16px; line-height: 1.8;">
        {!! $content !!}
    </div>

    <div style="margin-top: 40px; padding-top: 25px; border-top: 1px solid #e2e8f0; text-align: center;">
        <p style="font-size: 13px; color: #718096; font-weight: 500;">
            Enviado desde el panel de administración de <strong>Obertrack</strong>.
        </p>
    </div>
</div>
@endsection
