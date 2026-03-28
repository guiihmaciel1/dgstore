<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Models\Customer;
use App\Domain\Schedule\Enums\AppointmentStatus;
use App\Domain\Schedule\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $attendantFilter = $request->get('attendant');

        $query = Appointment::query()
            ->forDate($date)
            ->orderBy('start_time');

        if ($attendantFilter && array_key_exists($attendantFilter, Appointment::ATTENDANTS)) {
            $query->forAttendant($attendantFilter);
        }

        $appointments = $query->get();

        $appointmentsByAttendant = [];
        foreach (Appointment::ATTENDANTS as $key => $name) {
            $appointmentsByAttendant[$key] = $appointments
                ->where('attendant', $key)
                ->keyBy(fn (Appointment $a) => $a->start_time);
        }

        $timeSlots = $this->generateTimeSlots();

        return view('schedule.index', [
            'date'                    => Carbon::parse($date),
            'attendantFilter'         => $attendantFilter,
            'appointments'            => $appointments,
            'appointmentsByAttendant' => $appointmentsByAttendant,
            'timeSlots'               => $timeSlots,
            'attendants'              => Appointment::ATTENDANTS,
            'durationOptions'         => Appointment::DURATION_OPTIONS,
            'statuses'                => AppointmentStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attendant'           => 'required|string|in:' . implode(',', array_keys(Appointment::ATTENDANTS)),
            'date'                => 'required|date',
            'start_time'          => 'required|date_format:H:i',
            'duration_minutes'    => 'required|integer|in:' . implode(',', array_keys(Appointment::DURATION_OPTIONS)),
            'service_description' => 'nullable|string|max:500',
            'notes'               => 'nullable|string|max:1000',
            'customer_id'         => 'nullable|exists:customers,id',
            'customer_name'       => 'required|string|max:255',
            'customer_phone'      => 'nullable|string|max:20',
            'customer_instagram'  => 'nullable|string|max:100',
            'customer_address'    => 'nullable|string|max:500',
            'customer_birth_date' => 'nullable|date',
            'create_customer'     => 'nullable|boolean',
        ]);

        $endTime = Carbon::createFromFormat('H:i', $validated['start_time'])
            ->addMinutes((int) $validated['duration_minutes'])
            ->format('H:i');

        if (Appointment::hasConflict(
            $validated['attendant'],
            $validated['date'],
            $validated['start_time'],
            $endTime,
        )) {
            return back()
                ->withInput()
                ->with('error', 'Conflito de horário! Já existe um agendamento neste período.');
        }

        $customerId = $validated['customer_id'] ?? null;

        if (!$customerId && ($request->boolean('create_customer'))) {
            $customer = Customer::create([
                'name'       => $validated['customer_name'],
                'phone'      => $validated['customer_phone'] ?? '',
                'instagram'  => $validated['customer_instagram'] ?? null,
                'address'    => $validated['customer_address'] ?? null,
                'birth_date' => $validated['customer_birth_date'] ?? null,
            ]);
            $customerId = $customer->id;
        }

        Appointment::create([
            'customer_id'         => $customerId,
            'customer_name'       => $validated['customer_name'],
            'customer_phone'      => $validated['customer_phone'] ?? null,
            'attendant'           => $validated['attendant'],
            'date'                => $validated['date'],
            'start_time'          => $validated['start_time'],
            'end_time'            => $endTime,
            'duration_minutes'    => $validated['duration_minutes'],
            'service_description' => $validated['service_description'] ?? null,
            'notes'               => $validated['notes'] ?? null,
            'status'              => AppointmentStatus::Scheduled,
        ]);

        return redirect()
            ->route('schedule.index', ['date' => $validated['date']])
            ->with('success', 'Agendamento criado com sucesso!');
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'attendant'           => 'required|string|in:' . implode(',', array_keys(Appointment::ATTENDANTS)),
            'date'                => 'required|date',
            'start_time'          => 'required|date_format:H:i',
            'duration_minutes'    => 'required|integer|in:' . implode(',', array_keys(Appointment::DURATION_OPTIONS)),
            'service_description' => 'nullable|string|max:500',
            'notes'               => 'nullable|string|max:1000',
            'customer_name'       => 'required|string|max:255',
            'customer_phone'      => 'nullable|string|max:20',
        ]);

        $endTime = Carbon::createFromFormat('H:i', $validated['start_time'])
            ->addMinutes((int) $validated['duration_minutes'])
            ->format('H:i');

        if (Appointment::hasConflict(
            $validated['attendant'],
            $validated['date'],
            $validated['start_time'],
            $endTime,
            $appointment->id,
        )) {
            return back()
                ->withInput()
                ->with('error', 'Conflito de horário! Já existe um agendamento neste período.');
        }

        $appointment->update([
            'attendant'           => $validated['attendant'],
            'date'                => $validated['date'],
            'start_time'          => $validated['start_time'],
            'end_time'            => $endTime,
            'duration_minutes'    => $validated['duration_minutes'],
            'service_description' => $validated['service_description'] ?? null,
            'notes'               => $validated['notes'] ?? null,
            'customer_name'       => $validated['customer_name'],
            'customer_phone'      => $validated['customer_phone'] ?? null,
        ]);

        return redirect()
            ->route('schedule.index', ['date' => $validated['date']])
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_map(
                fn (AppointmentStatus $s) => $s->value,
                AppointmentStatus::cases()
            )),
        ]);

        $appointment->update(['status' => $validated['status']]);

        return redirect()
            ->route('schedule.index', ['date' => $appointment->date->format('Y-m-d')])
            ->with('success', 'Status atualizado para: ' . AppointmentStatus::from($validated['status'])->label());
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $date = $appointment->date->format('Y-m-d');
        $appointment->update(['status' => AppointmentStatus::Cancelled]);
        $appointment->delete();

        return redirect()
            ->route('schedule.index', ['date' => $date])
            ->with('success', 'Agendamento cancelado com sucesso!');
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'attendant'        => 'required|string|in:' . implode(',', array_keys(Appointment::ATTENDANTS)),
            'date'             => 'required|date',
            'duration_minutes' => 'required|integer|in:' . implode(',', array_keys(Appointment::DURATION_OPTIONS)),
            'exclude_id'       => 'nullable|string',
        ]);

        $slots = Appointment::getAvailableSlots(
            $request->get('attendant'),
            $request->get('date'),
            (int) $request->get('duration_minutes'),
        );

        return response()->json($slots);
    }

    public function whatsappMessage(Appointment $appointment, string $type): JsonResponse
    {
        $messages = [
            'confirmation' => $this->buildConfirmationMessage($appointment),
            'reminder'     => $this->buildReminderMessage($appointment),
            'followup'     => $this->buildFollowupMessage($appointment),
        ];

        if (!isset($messages[$type])) {
            return response()->json(['error' => 'Tipo de mensagem inválido'], 422);
        }

        return response()->json(['message' => $messages[$type]]);
    }

    private function buildConfirmationMessage(Appointment $appointment): string
    {
        return "Olá {$appointment->customer_name}! 😊\n\n"
             . "Seu agendamento na *DG Store* foi confirmado! ✅\n\n"
             . "📅 *Data:* {$appointment->formatted_date}\n"
             . "🕐 *Horário:* {$appointment->formatted_start_time} - {$appointment->formatted_end_time}\n"
             . "👤 *Atendente:* {$appointment->attendant_name}\n\n"
             . "Até lá! 🤝";
    }

    private function buildReminderMessage(Appointment $appointment): string
    {
        return "Olá {$appointment->customer_name}! 👋\n\n"
             . "Passando para lembrar do seu agendamento na *DG Store*:\n\n"
             . "📅 *Data:* {$appointment->formatted_date}\n"
             . "🕐 *Horário:* {$appointment->formatted_start_time}\n"
             . "👤 *Atendente:* {$appointment->attendant_name}\n\n"
             . "Qualquer dúvida, estamos à disposição! 😉";
    }

    private function buildFollowupMessage(Appointment $appointment): string
    {
        return "Olá {$appointment->customer_name}! 😄\n\n"
             . "Obrigado pela visita na *DG Store*! 🙏\n"
             . "Esperamos que tenha gostado do atendimento.\n\n"
             . "Qualquer dúvida sobre seu produto, estamos aqui! 💬";
    }

    private function generateTimeSlots(): array
    {
        $slots = [];
        $current = Carbon::createFromTime(Appointment::MIN_HOUR, 0);
        $end     = Carbon::createFromTime(Appointment::MAX_HOUR, 0);

        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes(30);
        }

        return $slots;
    }
}
