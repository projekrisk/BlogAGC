<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    
    protected static ?string $navigationLabel = 'Daftar Blog';
    protected static ?string $modelLabel = 'Blog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Blog')
                    ->description('Masukkan data blog Blogger Anda di sini.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nama Pengingat Blog')
                            ->placeholder('Misal: Web Indo Tekno'),
                            
                        Forms\Components\TextInput::make('blogger_blog_id')
                            ->label('ID Blog (Blogger)')
                            ->helperText('Bisa dilihat pada URL saat Anda membuka dashboard Blogger Anda (opsional untuk saat ini).'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Blog')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Blog')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('blogger_blog_id')
                    ->label('Blog ID')
                    ->searchable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('login_google')
                    ->label('Hubungkan')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->url(fn (Blog $record) => url('/auth/google?blog_id=' . $record->id))
                    ->openUrlInNewTab(false),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}