@extends('layouts.app')

@section('content')
<v-content>
    <v-container>
      <v-layout align-center justify-center>

            <v-flex xs12 sm8 md4>
            <v-card class="elevation-12">
                <v-toolbar dark color="primary">
                    <v-toolbar-title>Login</v-toolbar-title>
                    <v-spacer></v-spacer>

                        <a href="/"> <v-icon>home</v-icon></a>

                </v-toolbar>
                <form-login
                    action="{{ route('login') }}"
                    token="{{ csrf_token() }}"
                    old_usr="{{ old('username') }}"
                    err_usr="{{ $errors->first('username') }}"
                    err_pas="{{ $errors->first('password') }}">
                </form-login>
            </v-card>
            </v-flex>
      </v-layout>
    </v-container>
  </v-content>
@endsection
