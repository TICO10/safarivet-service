<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AgendaCita;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use App\Mail\ConfirmacionCitaMail;
use Illuminate\Support\Facades\Mail;

class AgendaCitaController extends Controller
{
    public function store(Request $request)
    {
        try {
            $now = Carbon::now();

            $data = $request->validate([
                'name' => 'required|string',
                'lastName' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'petName' => 'required|string',
                'petClase' => 'required|string',
                'date' => 'required|date',
                'hour' => 'required|string',
            ]);

            $dataSql = [
                'name' => $data['name'],
                'lastName' => $data['lastName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'petName' => $data['petName'],
                'petClase' => $data['petClase'],
                'dateCite' => $data['date'], // Fecha de la cita
                'hourCite' => $data['hour'], // Hora de la cita
                'regestxx' => 'ACTIVO',      // Estado de la cita
                'regfecxx' => $data['date'], // Fecha creacion
                'reghorxx' => $data['hour'], // Hora creación
                'regusrxx' => 'sistema',     // Usuario de creación
                'regfecmx' => $data['date'], // Fecha modificación
                'reghormx' => $data['hour'], // Hora modificación
                'regusrmx' => 'sistema',     // Usuario de modificación
                'regstamp' => $now,          // Marca de tiempo actual
            ];

            Log::info('Datos recibidos:', $dataSql);


            $cita = AgendaCita::create($dataSql);
            $calendarLink = $this->createGoogleCalendarEvent($data);
            Mail::to($data['email'])->send(new ConfirmacionCitaMail($data));
            return response()->json([
                'code' => 201,
                'message' => 'Cita registrada exitosamente',
                'data' => $cita,
                'calendar_link' => $calendarLink
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación: ' . $e->getMessage());
            return response()->json([
                'code' => 422,
                'message' => 'Datos inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al procesar la solicitud: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function createGoogleCalendarEvent($data)
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline');

        // Aquí debes manejar el token de acceso, para pruebas puedes usar el token manualmente
        $accessToken = json_decode(file_get_contents(storage_path('app/google/token.json')), true);
        $client->setAccessToken($accessToken);

        $service = new Google_Service_Calendar($client);

        // Combina fecha y hora
        $startDateTime = $data['date'] . 'T' . $data['hour'] . ':00';
        $endDateTime = Carbon::parse($startDateTime)->addHour()->format('Y-m-d\TH:i:s');

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Cita: ' . $data['petName'] . '/' . $data['name'] . ' ' . $data['lastName'],
            'description' => 'Cita:' . $data['name'] . ' ' . $data['lastName'] . "\n" .
                'Mascota: ' . $data['petName'] . "\n" .
                'Clase: ' . $data['petClase'] . "\n" .
                'Teléfono: ' . $data['phone'],
            'start' => [
                'dateTime' => $startDateTime,
                'timeZone' => 'America/Bogota',
            ],
            'end' => [
                'dateTime' => $endDateTime,
                'timeZone' => 'America/Bogota',
            ],
            'attendees' => [
                ['email' => $data['email']],
                ['email' => 'tico1096@gmail.com'],
            ],
        ]);

        Log::info('Datos calendar:', ['event' => $event->toSimpleObject()]);

        $calendarId = 'primary'; // O el ID de tu calendario
        $event = $service->events->insert($calendarId, $event);

        return $event->htmlLink;
    }
}
