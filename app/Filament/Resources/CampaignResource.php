<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Campaign AutoPost';
    protected static ?string $modelLabel = 'Campaign';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('blog_id')
                            ->relationship('blog', 'name')
                            ->label('Pilih Blog Tujuan')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Campaign (Untuk Kategori)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('source_type')
                            ->label('Metode Pencarian Keyword')
                            ->options([
                                'google_trends' => 'Google Trends (Topik Viral)',
                                'custom_keywords' => 'Keyword Manual (Evergreen SEO)',
                            ])
                            ->default('google_trends')
                            ->reactive()
                            ->required(),

                        Forms\Components\Select::make('geo_location')
                            ->label('Target Negara (Khusus Google Trends)')
                            ->options([
                                'ID' => 'Indonesia',
                                'US' => 'Amerika Serikat',
                                'SG' => 'Singapura',
                                'MY' => 'Malaysia',
                            ])
                            ->default('ID')
                            ->hidden(fn (\Filament\Forms\Get $get) => $get('source_type') === 'custom_keywords'),

                        Forms\Components\Textarea::make('keyword_queue')
                            ->label('Daftar Antrean Keyword')
                            ->placeholder("Cara merawat laptop\nTips belajar bahasa Inggris\nResep nasi goreng")
                            ->helperText('Masukkan 1 keyword per baris (Enter). Bot akan mengambil baris paling atas dan menghapusnya setelah selesai.')
                            ->rows(10)
                            ->hidden(fn (\Filament\Forms\Get $get) => $get('source_type') !== 'custom_keywords'),

                        Forms\Components\TextInput::make('interval_minutes')
                            ->label('Interval Bot (Menit)')
                            ->numeric()
                            ->default(120)
                            ->required()
                            ->helperText('Jarak waktu antar postingan. Misal: 120 (untuk 2 jam sekali).'),

                        Forms\Components\Toggle::make('include_image')
                            ->label('Sertakan Gambar (Auto-Download)')
                            ->default(true)
                            ->helperText('Matikan jika Anda hanya ingin memposting artikel berbasis teks (tanpa gambar ilustrasi).'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('blog.name')
                    ->label('Blog Tujuan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Campaign')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source_type')
                    ->label('Metode')
                    ->formatStateUsing(fn (string $state): string => $state === 'custom_keywords' ? 'Manual' : 'Google Trends')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'custom_keywords' ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('interval_minutes')
                    ->label('Interval (Menit)'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                    
                Tables\Columns\IconColumn::make('include_image')
                    ->label('Pakai Gambar')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('last_run_at')
                    ->label('Terakhir Jalan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}