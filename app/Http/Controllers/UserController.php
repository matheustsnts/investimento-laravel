<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Lista todos os usuários
     */
    public function index()
    {
        $usuarios = User::orderBy('created_at', 'desc')->get();
        return view('users.index', compact('usuarios'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Salva novo usuário
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'is_admin' => 'nullable|boolean',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        DB::beginTransaction();
        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => $request->has('is_admin'),
            ]);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuário criado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao criar usuário: ' . $e->getMessage());

            return back()->with('error', 'Erro ao criar usuário.')
                ->withInput();
        }
    }

    /**
     * Formulário de edição
     */
    public function edit(string $id)
    {
        $usuario = User::findOrFail($id);
        return view('users.edit', compact('usuario'));
    }

    /**
     * Atualiza usuário
     */
    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'is_admin' => 'nullable|boolean',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        DB::beginTransaction();
        try {
            $usuario->name = $validated['name'];
            $usuario->email = $validated['email'];
            $usuario->is_admin = $request->has('is_admin');

            if ($request->filled('password')) {
                $usuario->password = Hash::make($validated['password']);
            }

            $usuario->save();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());

            return back()->with('error', 'Erro ao atualizar usuário.')
                ->withInput();
        }
    }

    /**
     * Deleta usuário
     */
    public function destroy(string $id)
    {
        $usuario = User::findOrFail($id);

        // Impede que o admin delete a si mesmo
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode deletar sua própria conta.');
        }

        DB::beginTransaction();
        try {
            $usuario->delete();
            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuário deletado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao deletar usuário: ' . $e->getMessage());

            return back()->with('error', 'Erro ao deletar usuário.');
        }
    }
}