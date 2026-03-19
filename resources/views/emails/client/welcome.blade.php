<x-mail::message>
# ¡Bienvenido a {{ config('app.name', 'Sistema Contratamos') }}!

Hola **{{ $client->contact_name }}** de **{{ $client->business_name }}**,

Tu cuenta como empresa ha sido creada exitosamente. Para ingresar por primera vez al portal y configurar tu contraseña, deberás utilizar tu correo electrónico y el NIT de tu empresa.

- **Usuario (Correo):** {{ $client->email }}
- **NIT/Documento:** {{ $client->document_number }}

<x-mail::button :url="route('company.first-login')">
Configurar mi contraseña
</x-mail::button>

Gracias,<br>
El equipo de {{ config('app.name', 'Sistema Contratamos') }}
</x-mail::message>
