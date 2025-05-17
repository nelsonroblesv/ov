<?php

namespace App\Filament\Auth;

use Illuminate\Validation\ValidationException;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class CustomLogin extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
               // $this->getEmailFormComponent(), 
                $this->getLoginFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component 
    {
        return TextInput::make('login')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    } 

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';
 
        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
            'is_active' => true,
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
}

}
