@extends('emails.layout')

@section('content')
<div style="font-family: 'Montserrat', sans-serif; color: #1e1b4b; line-height: 1.6;">
    <h2 style="color: #22A9C8; font-size: 24px; font-weight: 700; margin-bottom: 20px;">
        {{ $subject }}
    </h2>
    
    <div style="background-color: #f8fafc; padding: 25px; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
        {!! $htmlMessage !!}
    </div>

    @if(!empty($attachments))
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 14px; color: #64748b; font-weight: 600; margin-bottom: 10px;">
                Archivos adjuntos:
            </p>
            <ul style="list-style: none; padding: 0;">
                @foreach($attachments as $file)
                    <li style="font-size: 13px; color: #22A9C8; margin-bottom: 5px;">
                        📎 {{ $file['name'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px dotted #cbd5e1; text-align: center;">
        <p style="font-size: 14px; color: #64748b; margin-bottom: 5px;">
            Este mensaje ha sido enviado por <strong>{{ $companyName }}</strong> a través de <strong>Obertrack</strong>.
        </p>
        <p style="font-size: 12px; color: #94a3b8;">
            No respondas directamente a este correo automático.
        </p>
    </div>
</div>
@endsection
