<?php

namespace App\Filament\Resources\Businesses\Pages;

use App\Filament\Resources\Businesses\BusinessResource;
use App\Models\Business;
use App\Services\WhatsAppBusinessNotificationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBusiness extends EditRecord
{
    protected static string $resource = BusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('welcome_business')
                ->label('Dar la bienvenida')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Enviar mensaje de bienvenida')
                ->modalDescription('¿Deseas enviar el mensaje de bienvenida por WhatsApp a este negocio?')
                ->modalSubmitActionLabel('Sí, enviar')
                ->action(function (Business $record, WhatsAppBusinessNotificationService $service) {
                    try {
                        $sent = $service->sendWelcomeMessage($record);

                        if ($sent) {
                            Notification::make()
                                ->title('Notificación enviada')
                                ->body('El mensaje de bienvenida ha sido enviado correctamente por WhatsApp.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Error en el envío')
                                ->body('No se pudo enviar el mensaje. Verifica que el negocio tenga usuarios con un número de teléfono válido o revisa los logs de Laravel.')
                                ->warning()
                                ->send();
                        }
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Error al enviar la notificación')
                            ->body($th->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            DeleteAction::make(),
        ];
    }
}