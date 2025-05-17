<?php
/**
 * Copyright (c) 2016-2017 "Arrow768"
 *
 * This file is part of Aurorastation-Wi
 * Aurorastation-Wi is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ServerForm;
use Yajra\DataTables\Datatables;
Use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->cannot('server_forms_show')) {
                abort('403', 'You do not have the required permission');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('server.forms.index');
    }

    public function getEdit($form_id)
    {
        $form = ServerForm::findOrFail($form_id);
        return view('server.forms.edit', ['form' => $form]);
    }

    public function postEdit($form_id, Request $request)
    {
        if ($request->user()->cannot('server_forms_edit')) {
            abort('403', 'You do not have the required permission');
        }
        $this->validate($request, [
            'id' => 'required|max:9999|numeric',
            'name' => 'required|max:50',
            'department' => 'required|max:32',
            'data' => 'required',
            'info' => 'required'
        ]);

        $form = ServerForm::findOrFail($form_id);
        $form->id = $request->input('id');
        $form->name = $request->input('name');
        $form->department = $request->input('department');
        $form->data = $request->input('data');
        $form->info = $request->input('info');
        $form->save();

        Log::notice('perm.forms.edit - Form has been edited', ['user_id' => $request->user()->user_id, 'form_id' => $form->form_id]);

        return redirect()->route('admin.forms.index');
    }

    public function delete($form_id, Request $request)
    {
        if ($request->user()->cannot('server_forms_edit')) {
            abort('403', 'You do not have the required permission');
        }
        $form = ServerForm::findOrFail($form_id);

        Log::notice('perm.forms.delete - Form has been deleted', ['user_id' => $request->user()->user_id, 'form_id' => $form->form_id, 'form_ingameid' => $form->id, 'form_name' => $form->name]);

        $form->delete();

        return redirect()->route('admin.forms.index');
    }

    public function getAdd(Request $request)
    {
        if ($request->user()->cannot('server_forms_edit')) {
            abort('403', 'You do not have the required permission');
        }

        return view('server.forms.add');
    }

    public function postAdd(Request $request)
    {
        if ($request->user()->cannot('server_forms_edit')) {
            abort('403', 'You do not have the required permission');
        }

        $this->validate($request, [
            'id' => 'required|max:9999|numeric',
            'name' => 'required|max:50',
            'department' => 'required|max:32',
            'data' => 'required',
            'info' => 'required'

        ]);

        $form = new ServerForm();
        $form->id = $request->input('id');
        $form->name = $request->input('name');
        $form->department = $request->input('department');
        $form->data = $request->input('data');
        $form->info = $request->input('info');
        $form->save();

        Log::notice('perm.forms.add - Form has been added', ['user_id' => $request->user()->user_id, 'form_id' => $form->form_id]);

        return redirect()->route('admin.forms.index');
    }

    public function getFormData(Request $request)
    {
        $forms = ServerForm::select(['form_id', 'id', 'name', 'department']);

        return Datatables::of($forms)
            ->removeColumn('form_id')
            ->editColumn('name', function( ServerForm $form) {
                return '<a href="'.route('admin.forms.edit.get',['form_id'=>$form->form_id]).'">'.$form->name.'</a></p>';
            })
            ->addColumn('action', function( ServerForm $form) use ($request) {
                $actionstring = '<div class="btn-group"><a href="'.route('admin.forms.edit.get',['form_id'=>$form->form_id]).'" class="btn btn-success" role="button">Show/Edit</a>';
                if($request->user()->can('server_forms_edit')){
                    $actionstring .= '<a href="'.route('admin.forms.delete',['form_id'=>$form->form_id]).'" class="btn btn-danger" role="button">Delete</a>';
                }
                $actionstring .= '</div>';
                return $actionstring;
            })
            ->rawColumns(['name', 'action'])
            ->make();
    }
}
