<?php

namespace App\Filament\Resources\Businesses\Schemas;

use App\Enums\DayOfWeek;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BusinessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(6)
            ->components([
                Section::make('Información del Negocio')
                    ->description('Proporciona los detalles esenciales de tu restaurante para que clientes puedan encontrarte y contactarte fácilmente.')
                    ->columnSpan(4)
                    ->schema([
                        Section::make('Apariencia del Negocio')
                            ->description('Configura cómo se verá el restaurante en la aplicación móvil.')
                            ->columns(1)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        FileUpload::make('logo_path')
                                            ->label('Logo del negocio')
                                            ->image()
                                            ->openable()
                                            ->disk(fn () => config('filesystems.default'))
                                            ->directory('restaurants/logos')
                                            ->imageEditor()
                                            ->imageAspectRatio('1:1')
                                            ->automaticallyOpenImageEditorForAspectRatio()
                                            ->automaticallyCropImagesToAspectRatio('1:1')
                                            ->automaticallyResizeImagesToWidth('300')
                                            ->automaticallyResizeImagesToHeight('300')
                                            ->columnSpan(1),

                                        FileUpload::make('banner_path')
                                            ->label('Banner Promocional')
                                            ->image()
                                            ->openable()
                                            ->disk(fn () => config('filesystems.default'))
                                            ->directory('restaurants/banners')
                                            ->imageEditor()
                                            ->imageAspectRatio('3:1')
                                            ->automaticallyOpenImageEditorForAspectRatio()
                                            ->automaticallyCropImagesToAspectRatio('3:1')
                                            ->automaticallyResizeImagesToWidth('700')
                                            ->automaticallyResizeImagesToHeight('300')
                                            ->columnSpan(3),
                                    ]),
                            ]),

                        Tabs::make('Detalles del Negocio')
                            ->tabs([
                                Tab::make('General')
                                    ->icon('heroicon-m-identification')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre comercial')
                                            ->placeholder('Ej: El Rincón del Sabor')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                                        TextInput::make('slug')
                                            ->label('URL (Slug)')
                                            ->prefix('https://rapidito.com/business/')
                                            ->readOnly()
                                            ->copyable()
                                            ->dehydrated()
                                            ->required(),

                                        Select::make('category_id')
                                            ->label('Categoría principal')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nombre')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique('categories', 'name', ignoreRecord: true),
                                                ToggleButtons::make('is_active')
                                                    ->label('Visibilidad Global')
                                                    ->helperText('Si se oculta, ningún restaurante de esta categoría se mostrará')
                                                    ->options([
                                                        'true' => 'Público',
                                                        'false' => 'Oculto',
                                                    ])
                                                    ->colors([
                                                        'true' => 'success',
                                                        'false' => 'warning',
                                                    ])
                                                    ->icons([
                                                        'true' => 'heroicon-m-eye',
                                                        'false' => 'heroicon-m-eye-slash',
                                                    ])
                                                    ->inline()
                                                    ->formatStateUsing(fn ($state) => $state ? 'true' : 'false')
                                                    ->dehydrateStateUsing(fn ($state) => $state === 'true')
                                                    ->default('true'),
                                            ]),

                                        Select::make('tags')
                                            ->label('Etiquetas (Keywords)')
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nombre de la etiqueta')
                                                    ->placeholder('Ej: Comida Rápida, Vegano, Gourmet...')
                                                    ->required()
                                                    ->unique('tags', 'name', ignoreRecord: true)
                                                    ->maxLength(255),
                                            ]),
                                    ]),

                                Tab::make('Ubicación')
                                    ->icon('heroicon-m-map-pin')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('address')
                                            ->label('Dirección exacta')
                                            ->required()
                                            ->columnSpanFull(),

                                        Select::make('city_id')
                                            ->label('Ciudad')
                                            ->relationship('city', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nombre de la Ciudad')
                                                    ->placeholder('Ej: Santo Domingo, Madrid...')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('state')
                                                    ->label('Estado / Provincia')
                                                    ->placeholder('Ej: Distrito Nacional, Comunidad de Madrid...')
                                                    ->maxLength(255),
                                                TextInput::make('country')
                                                    ->label('País')
                                                    ->placeholder('Ej: República Dominicana, España...')
                                                    ->default('República Dominicana')
                                                    ->required()
                                                    ->maxLength(255),
                                                ToggleButtons::make('active')
                                                    ->label('Estatus Operativo')
                                                    ->boolean()
                                                    ->options([
                                                        true => 'Activa',
                                                        false => 'Inactiva',
                                                    ])
                                                    ->colors([
                                                        true => 'success',
                                                        false => 'danger',
                                                    ])
                                                    ->icons([
                                                        true => 'heroicon-m-check-circle',
                                                        false => 'heroicon-m-x-circle',
                                                    ])
                                                    ->inline()
                                                    ->default(true),
                                            ]),

                                        TextInput::make('postal_code')
                                            ->label('Código postal')
                                            ->numeric(),

                                        TextInput::make('google_maps_url')
                                            ->label('Enlace de Google Maps')
                                            ->url()
                                            ->prefixIcon('heroicon-m-map-pin')
                                            ->columnSpanFull(),

                                        Grid::make(2)
                                            ->columnSpanFull()
                                            ->schema([
                                                TextInput::make('lat')
                                                    ->label('Latitud')
                                                    ->numeric()
                                                    ->inputMode('decimal')
                                                    ->required(),

                                                TextInput::make('lng')
                                                    ->label('Longitud')
                                                    ->numeric()
                                                    ->inputMode('decimal')
                                                    ->required(),
                                            ]),

                                        FileUpload::make('reference_image')
                                            ->label('Foto de fachada')
                                            ->image()
                                            ->openable()
                                            ->disk(fn () => config('filesystems.default'))
                                            ->directory('restaurants/references')
                                            ->imageEditor()
                                            ->imageAspectRatio('16:9')
                                            ->automaticallyOpenImageEditorForAspectRatio()
                                            ->automaticallyCropImagesToAspectRatio('16:9')
                                            ->columnSpanFull(),
                                    ]),

                                Tab::make('Contacto')
                                    ->icon('heroicon-m-phone')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('phone')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->required()
                                            ->prefixIcon('heroicon-m-phone'),

                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->prefixIcon('heroicon-m-envelope'),

                                        TextInput::make('web_site')
                                            ->label('Sitio Web')
                                            ->url()
                                            ->columnSpanFull(),
                                    ]),

                                Tab::make('Horarios de Atención')
                                    ->icon('heroicon-m-clock')
                                    ->schema([
                                        Repeater::make('schedules')
                                            ->relationship('schedules')
                                            ->schema([
                                                Select::make('day_of_week')
                                                    ->label('Día de la Semana')
                                                    ->options(collect(DayOfWeek::cases())->mapWithKeys(fn ($day) => [
                                                        $day->value => $day->label(),
                                                    ]))
                                                    ->enum(DayOfWeek::class)
                                                    ->required()
                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                                // 1. Cambiar open_time -> start_time
                                                TimePicker::make('start_time')
                                                    ->label('Apertura')
                                                    ->seconds(false)
                                                    ->default('09:00')
                                                    ->required(),

                                                // 2. Cambiar close_time -> end_time
                                                TimePicker::make('end_time')
                                                    ->label('Cierre')
                                                    ->seconds(false)
                                                    ->default('22:00')
                                                    ->required(),

                                                Toggle::make('is_active')
                                                    ->label('Abierto')
                                                    ->default(true)
                                                    ->inline(false),
                                            ])
                                            ->columns(4)
                                            ->defaultItems(0)
                                            ->addActionLabel('Agregar Horario de Atención')
                                            ->reorderable(false)
                                            ->collapsible()
                                            ->itemLabel(function (array $state): ?string {
                                                if (! isset($state['day_of_week'])) {
                                                    return 'Nuevo Horario';
                                                }

                                                $val = $state['day_of_week'];
                                                $dayEnum = $val instanceof DayOfWeek
                                                    ? $val
                                                    : DayOfWeek::tryFrom($val);

                                                $dayLabel = $dayEnum?->label() ?? 'Día no seleccionado';

                                                if (isset($state['is_active']) && ! $state['is_active']) {
                                                    return "{$dayLabel} - (Cerrado)";
                                                }

                                                // 3. Ajustar las variables aquí también
                                                $openTime = $state['start_time'] ?? '--:--';
                                                $closeTime = $state['end_time'] ?? '--:--';

                                                return "{$dayLabel} ({$openTime} - {$closeTime})";
                                            }),
                                    ]),
                            ]),
                    ])->grow(true),

                Section::make('Configuración Adicional')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('status')
                            ->label('Estado del Negocio')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                                'pending' => 'Pendiente',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),

                        Toggle::make('is_open')
                            ->label('¿Negocio Abierto?')
                            ->helperText('Permite forzar la apertura/cierre independientemente del horario.')
                            ->default(true),

                        Toggle::make('accepts_delivery')
                            ->label('Acepta Delivery')
                            ->default(true),

                        Toggle::make('accepts_pickup')
                            ->label('Acepta Retiro en Tienda')
                            ->default(true),
                    ]),
            ]);
    }
}