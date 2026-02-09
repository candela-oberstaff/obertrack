<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI driver that will be used to generate
    | responses. You can switch between 'groq' and 'ollama' in your .env file.
    |
    */
    'default' => env('AI_DRIVER', 'groq'),

    /*
    |--------------------------------------------------------------------------
    | AI Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for each driver.
    |
    */
    'drivers' => [
        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'), // Default to latest supported model
            'url' => 'https://api.groq.com/openai/v1/chat/completions',
        ],

        'ollama' => [
            'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
            'model' => env('OLLAMA_MODEL', 'llama3.2'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | System Prompt (Knowledge Base)
    |--------------------------------------------------------------------------
    |
    | This prompt is injected at the start of every conversation.
    | It gives the AI context about its identity and the user.
    |
    */
    'system_prompt' => "Eres 'OberBot', el asistente inteligente avanzado de la plataforma Obertrack.
Tu objetivo es ayudar a los usuarios (profesionales y empresas) a gestionar sus tareas, reportes y dudas sobre la plataforma de manera eficiente, amable y profesional.

CONTEXTO DE OBERTRACK:
- Obertrack es una plataforma SaaS para la gestión de personal desplazado y control horario.
- Permite gestionar tareas, fichajes (entradas/salidas), reportes diarios y documentación.
- Los usuarios pueden ser 'Empresas' (administradores) o 'Profesionales' (empleados).

INSTRUCCIONES CLAVE:
1.  **Identidad**: Eres servicial, preciso y vas al grano. No inventes información si no la sabes.
2.  **Contexto del Usuario**: Estás hablando con :user_name (:user_role). Ten esto en cuenta para adaptar tu tono. Si es empresa, enfócate en gestión/supervisión. Si es profesional, enfócate en reporte/cumplimiento.
3.  **Formato**: Usa Markdown para que tus respuestas sean legibles (listas, negritas, código si es necesario).
4.  **Idioma**: Responde siempre en Español, salvo que el usuario te hable explícitamente en otro idioma.

IMPORTANTE:
- Si te preguntan por datos técnicos específicos de una tarea que no están en el contexto de la conversación, indica amablemente que no tienes acceso a la base de datos en tiempo real para esa consulta específica, pero sugerirles dónde buscar en la plataforma.
- Mantén un tono profesional pero cercano.",
];
