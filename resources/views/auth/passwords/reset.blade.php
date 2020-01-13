@extends('layouts.app')

@section('content')
<br/>
<v-content>
        <v-layout align-center justify-center>
            <v-flex sm8>
                <v-card>
                    <v-toolbar dark color="primary">
                        <v-toolbar-title>Recuperar password</v-toolbar-title>
                        <v-spacer></v-spacer>

                            <a href="/"> <v-icon>home</v-icon></a>

                    </v-toolbar>
                        <reset-login
                            action="{{ route('password.update') }}"
                            token="{{ csrf_token() }}"
                            token_mail="{{ $token }}"
                            email="{{ old('email') }}"
                            err_mail="{{ $errors->first('email') }}"
                            err_pass="{{ $errors->first('password') }}">
                        ></reset-login>
                </v-card>
            </v-flex>
        </v-layout>
    </v-content>

@endsection
