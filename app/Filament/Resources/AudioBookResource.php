<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\AudioBook;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AudioBookResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AudioBookResource\RelationManagers;
use Filament\Forms\Components\Textarea;

class AudioBookResource extends Resource
{
    protected static ?string $model = AudioBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Audiobooks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('AudioBook')
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
                            ->unique(AudioBook::class, 'slug', ignoreRecord: true),
                        Select::make('audiobook_serie_id') // Matches the database column
                            ->label('Audiobook Serie')
                            ->relationship('audiobookSerie', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('cover_image')
                            ->image()
                            ->columnSpanFull()
                            ->directory('cover_images')
                            ->required(),
                        DatePicker::make('year')
                            ->required(),
                        TextInput::make('narrator')
                            ->required(),
                        Repeater::make('chapters')
                            ->relationship('audiobookChapters')
                            ->label('Chapters')
                            ->schema([
                                TextInput::make('title')
                                    ->required(),
                                FileUpload::make('audio_file')
                                    ->required(),
                            ])->columnSpan(2)
                            ->collapsible(),
                        Toggle::make('is_free')
                            ->label('Sample content?')
                            ->columnSpan(2),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('year')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('narrator')
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
            'index' => Pages\ListAudioBooks::route('/'),
            'create' => Pages\CreateAudioBook::route('/create'),
            'edit' => Pages\EditAudioBook::route('/{record}/edit'),
        ];
    }
}
