<?php

namespace App\Filament\App\Resources\Office\Students\Tables;

use App\Enums\Gender;
use App\Enums\Status;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\ExportBulkAction;
use App\Filament\Exports\StudentExporter;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('photo_path')
                    ->label('Image')
                    ->disk('public')
                    ->visibility('public')
                    ->circular()
                    ->imageHeight('3rem')
                    ->imageWidth('3rem')
                    ->defaultImageUrl(fn (Student $record): string => $record->facehash_avatar_url)
                    ->action(
                        Action::make('view_avatar')
                            ->label('View image')
                            ->visible(fn (Student $record): bool => filled($record->photo_path))
                            ->modalHeading(fn (Student $record): string => $record->full_name . ' image')
                            ->modalWidth(Width::FitContent)
                            ->modalContent(function (Student $record): HtmlString {
                                $imageUrl = $record->photo_url ?? $record->facehash_avatar_url;

                                return new HtmlString(
                                    '<div style="display: flex; justify-content: center; align-items: center;">'
                                    . '<img src="' . e($imageUrl) . '" alt="' . e($record->full_name) . '" '
                                    . 'style="display: block; width: auto; height: auto; max-width: 90vw; max-height: 70vh; object-fit: contain;" />'
                                    . '</div>'
                                );
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                    ),
                TextColumn::make('student_lrn')
                    ->visible(config('system-features.student_lrn'))
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('suffix')
                    ->searchable(),
                TextColumn::make('gender')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Gender::options()[$state] ?? ''),
                ToggleColumn::make('active')
                    ->onIcon(Heroicon::HandThumbUp)
                    ->offIcon(Heroicon::HandThumbDown)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('active')
                    ->selectablePlaceholder(true)
                    ->native(false)
                    ->options(Status::class),
                SelectFilter::make('gender')
                    ->selectablePlaceholder(true)
                    ->native(false)
                    ->options(Gender::class)
            ])
            ->recordActions([
                EditAction::make()
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('success')
                    ->outlined(true)
                    ->button(),
            ])
            ->defaultSort('last_name')
            ->defaultPaginationPageOption(25)
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Export to Excel')
                        ->exporter(StudentExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(true);
    }
}
