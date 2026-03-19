<?php

namespace App\Domains\Commercial\Jobs;

use App\Domains\Commercial\Models\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientWelcomeMail;

class SendClientWelcomeJob implements ShouldQueue
{
    use Queueable;

    protected $client;

    /**
     * Create a new job instance.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Simular envío de WhatsApp (Mercately API Call iría aquí)
        // Por ahora lo logueamos, aunque puedes integrar con la API de WhatsApp
        $whatsappMessage = "¡Bienvenido a SISTEMA CONTRATAMOS! Para tu primer ingreso al portal, ingresa a nuestro sitio y configura tu clave con tu NIT y correo: {$this->client->email}";
        Log::info("WhatsApp a {$this->client->phone}: {$whatsappMessage}");

        // 2. Enviar Correo Electrónico
        Mail::to($this->client->email)->send(new ClientWelcomeMail($this->client));
        
        // 3. Registrar la trazabilidad del evento en Activity Log
        activity()
            ->causedBy($this->client->user)
            ->performedOn($this->client)
            ->event('notification_sent')
            ->log('Se enviaron las credenciales de acceso al cliente vía WhatsApp y Correo.');
            
        Log::info('Credenciales enviadas al cliente: ' . $this->client->business_name);
    }
}
