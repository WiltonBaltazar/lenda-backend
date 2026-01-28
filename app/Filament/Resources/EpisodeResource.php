<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Episode;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\PodcastResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PodcastResource\RelationManagers;
use App\Filament\Resources\EpisodeResource\Pages\EditEpisode;
use App\Filament\Resources\EpisodeResource\Pages\ListEpisodes;
use App\Filament\Resources\EpisodeResource\Pages\CreateEpisode;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Podcast';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Podcast')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Episode::class, 'slug', ignoreRecord: true),
                        Select::make('podcast_id') // Matches the database column
                            ->label('Podcast Name')
                            ->relationship('podcast', 'name')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('cover_image')
                            ->image()
                            ->columnSpanFull()
                            ->required(),
                        Textarea::make('description')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('guest')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('audio_file')
                            ->required()
                            ->columnSpanFull(),
                        DateTimePicker::make('release_date')
                            ->required(),
                        TextInput::make('duration')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest')
                    ->searchable(),
                Tables\Columns\TextColumn::make('release_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEpisodes::route('/'),
            'create' => CreateEpisode::route('/create'),
            'edit' => EditEpisode::route('/{record}/edit'),
        ];
    }
}
