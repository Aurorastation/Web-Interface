{{--Copyright (c) 2016 "Arrow768"--}}

{{--This file is part of the Aurora Webinterface--}}

{{--The Aurora Webinterface is free software: you can redistribute it and/or modify--}}
{{--it under the terms of the GNU Affero General Public License as--}}
{{--published by the Free Software Foundation, either version 3 of the--}}
{{--License, or (at your option) any later version.--}}

{{--This program is distributed in the hope that it will be useful,--}}
{{--but WITHOUT ANY WARRANTY; without even the implied warranty of--}}
{{--MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the--}}
{{--GNU Affero General Public License for more details.--}}

{{--You should have received a copy of the GNU Affero General Public License--}}
{{--along with this program. If not, see <http://www.gnu.org/licenses/>.<!DOCTYPE html>--}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @include('components.formerrors')
                <div class="panel panel-default">
                    <div class="panel-heading">Add a new Book</div>

                    <div class="panel-body">
                        {{Form::open(array('route' => 'server.library.add.post','method' => 'post')) }}
                        {{Form::token()}}

                        {{Form::bsText('author')}}
                        {{Form::bsText('title')}}
                        {{Form::bsSelectList('category',array(
                            'Reference'=>'Reference',
                            'Non-Fiction'=>'Non-Fiction',
                            'Fiction'=>'Fiction',
                            'Religion'=>'Religion',
                            'Adult'=>'Adult'))}}
                        {{Form::bsTextArea('content')}}

                        {{Form::submit('Submit', array('class'=>'btn btn-default'))}}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(
            document.querySelector( '#content' ),{
                removePlugins: ['EasyImage','Image','MediaEmbed','ImageCaption','ImageStyle','ImageToolbar','ImageUpload','Link'],
                toolbar: [ 'heading', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
            }
        ).then( editor => {
            console.log( editor )
        }).catch( error => {
            console.error( error )
        });
    </script>
@endsection