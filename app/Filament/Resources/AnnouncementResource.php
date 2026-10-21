<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements;
use App\Models\Announcement;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Marcelodelgado\Announcements\Enums\AnnouncementType;
use Marcelodelgado\Announcements\Filament\Resources\AnnouncementResource as PackageAnnouncementResource;

class AnnouncementResource extends PackageAnnouncementResource
{
    protected static ?string $model = Announcement::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('announcements::filament.form.title.label'))
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('announcements::filament.form.title.helper')),
                        Select::make('type')
                            ->label(__('announcements::filament.form.type.label'))
                            ->options(collect(AnnouncementType::cases())
                                ->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
                            ->required()
                            ->native(false)
                            ->helperText(__('announcements::filament.form.type.helper')),
                    ]),
                Textarea::make('body')
                    ->label(__('announcements::filament.form.body.label'))
                    ->required()
                    ->rows(4)
                    ->helperText(__('announcements::filament.form.body.helper')),
                Grid::make(2)
                    ->schema([
                        Select::make('target_type')
                            ->label('Target announcement')
                            ->options([
                                'all' => 'All',
                                'teachers' => 'Teachers',
                                'office' => 'Office',
                                'homeroom' => 'Homeroom',
                            ])
                            ->default('all')
                            ->required()
                            ->live()
                            ->native(false),
                    ]),
                Grid::make(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('announcements::filament.form.is_active.label'))
                            ->default(true)
                            ->helperText(__('announcements::filament.form.is_active.helper')),
                        Toggle::make('is_dismissible')
                            ->label(__('announcements::filament.form.is_dismissible.label'))
                            ->default(true)
                            ->helperText(__('announcements::filament.form.is_dismissible.helper')),
                    ]),
                Grid::make(2)
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label(__('announcements::filament.form.starts_at.label'))
                            ->seconds(false)
                            ->helperText(__('announcements::filament.form.starts_at.helper')),
                        DateTimePicker::make('expires_at')
                            ->label(__('announcements::filament.form.expires_at.label'))
                            ->seconds(false)
                            ->helperText(__('announcements::filament.form.expires_at.helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
