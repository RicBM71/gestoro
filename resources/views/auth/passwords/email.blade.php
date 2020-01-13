@extends('layouts.app')

@section('content')
<v-content>
    <v-container>
      <v-layout align-center justify-center>

            <v-flex xs12 sm8 md4>
            <v-card class="elevation-12">
                <v-toolbar dark color="primary">
                    <v-toolbar-title>Recuperar password</v-toolbar-title>
                    <v-spacer></v-spacer>

                        <a href="/"> <v-icon>home</v-icon></a>

                </v-toolbar>
                <mail-login
                    action="{{ route('password.email') }}"
                    token="{{ csrf_token() }}"
                    old_mail="{{ old('email') }}"
                    err_mail="{{ $errors->first('email') }}">
                </mail-login>
            </v-card>
            </v-flex>
      </v-layout>
    </v-container>
  </v-content>
@endsection
