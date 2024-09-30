<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Password;
use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->rules(['required']),
                Forms\Components\TextInput::make('email')
                    ->rules(['required'])
                    ->email(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->rule(Password::default())
                    ->rules(['required']),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->same('password')
                    ->rule(Password::default())
                    ->rules(['required']),
                Forms\Components\Select::make('role')->relationship('roles', 'name')->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('created_at')->date('d/m/Y H:i:s')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('change_password')
                ->form([
                    TextInput::make('password')
                    ->password()
                    ->rule(Password::default())
                    ->rules(['required']),
                TextInput::make('password_confirmation')
                    ->password()
                    ->same('password')
                    ->rule(Password::default())
                    ->rules(['required'])
                ])
                ->action(function(User $record, array $data){
                    $record->update([
                        'password' => bcrypt($data['password'])
                    ]);
                    Filament::notify('success', 'Senha atualizada com sucesso!');
                }),
            ])
            ->bulkActions([
                // Ações em massa
                //Tables\Actions\DeleteBulkAction::make(),
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
        // Caso comentar uma página abaixo, será aberto um modal
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // Desabilitando recursos
    public static function canCreate() :bool
    {
        return true;
    }
}
